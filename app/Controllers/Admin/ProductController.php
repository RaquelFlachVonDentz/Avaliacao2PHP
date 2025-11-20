<?php
namespace App\Controllers\Admin;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Url;
use App\Core\View;
use App\Repositories\CategoryRepository;
use App\Repositories\OrderItemRepository;
use App\Repositories\ProductRepository;
use App\Services\ProductService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController {
    private View $view;
    private ProductRepository $repo;
    private ProductService $service;
    private CategoryRepository $categoryRepo;
    private OrderItemRepository $orderItemRepo;

    public function __construct() {
        $this->view = new View();
        $this->repo = new ProductRepository();
        $this->service = new ProductService();
        $this->categoryRepo = new CategoryRepository();
        $this->orderItemRepo = new OrderItemRepository();
    }

    public function index(Request $request): Response {
        $page = max(1, (int)$request->query->get('page', 1));
        $perPage = 5;
        $total = $this->repo->countAll();
        $products = $this->repo->paginate($page, $perPage);
        $pages = (int)ceil($total / $perPage);
        $categories = $this->categoryRepo->getArray();
        $html = $this->view->render('admin/products/index', compact('products','page','pages', 'categories'));
        return new Response($html);
    }

    public function create(): Response {
        $categories = $this->categoryRepo->findAll();
        $html = $this->view->render('admin/products/create', ['csrf' => Csrf::token(), 'errors' => [], 'categories' => $categories]);
        return new Response($html);
    }

    public function store(Request $request): Response {
        if (!Csrf::validate($request->request->get('_csrf'))) return new Response('Token CSRF inválido', 419);
        $file = $request->files->get('image');
        $errors = $this->service->validate($request->request->all(), $file);
        if ($errors) {
            $categories = $this->categoryRepo->findAll();
            $html = $this->view->render('admin/products/create', ['csrf' => Csrf::token(), 'errors' => $errors, 'old' => $request->request->all(), 'categories' => $categories]);
            return new Response($html, 422);
        }
        $imagePath = $this->service->storeImage($file);
        $product = $this->service->make($request->request->all(), $imagePath);
        $id = $this->repo->create($product);
        return new RedirectResponse(Url::to('admin/products/show?id=' . $id));
    }

    public function show(Request $request): Response {
        $id = (int)$request->query->get('id', 0);
        $product = $this->repo->find($id);
        if (!$product) return new Response('Produto não encontrado', 404);
        $html = $this->view->render('admin/products/show', ['product' => $product]);
        return new Response($html);
    }

    public function edit(Request $request): Response {
        $id = (int)$request->query->get('id', 0);
        $product = $this->repo->find($id);
        $categories = $this->categoryRepo->findAll();
        if (!$product) return new Response('Produto não encontrado', 404);
        $html = $this->view->render('admin/products/edit', ['product' => $product, 'csrf' => Csrf::token(), 'errors' => [], 'categories' => $categories]);
        return new Response($html);
    }

    public function update(Request $request): Response {
        if (!Csrf::validate($request->request->get('_csrf'))) return new Response('Token CSRF inválido', 419);
        $data = $request->request->all();
        $file = $request->files->get('image');
        $errors = $this->service->validate($data, $file);
        if ($errors) {
            $categories = $this->categoryRepo->findAll();
            $html = $this->view->render('admin/products/edit', ['product' => array_merge($this->repo->find((int)$data['id']), $data), 'csrf' => Csrf::token(), 'errors' => $errors, 'categories' => $categories]);
            return new Response($html, 422);
        }
        $imagePath = $this->service->storeImage($file) ?? ($this->repo->find((int)$data['id'])['image_path'] ?? null);
        $product = $this->service->make($data, $imagePath);
        if (!$product->id) return new Response('ID inválido', 422);
        $this->repo->update($product);
        return new RedirectResponse(Url::to('admin/products/show?id=' . $product->id));
    }

    public function delete(Request $request): Response {
        if (!Csrf::validate($request->request->get('_csrf'))) {
            return new Response('Token CSRF inválido', 419);
        }

        $id = (int)$request->request->get('id', 0);

        if ($id <= 0) {
            Flash::push('danger', 'ID inválido para exclusão');
            return new RedirectResponse(Url::to('admin/products'));
        }

        $product = $this->repo->find($id);
        if (!$product) {
            Flash::push('danger', 'Produto não encontrado.');
            return new RedirectResponse(Url::to('admin/products'));
        }

        // Verifica se há pedidos ativos usando este produto (exceto entregues e cancelados)
        $activeOrders = $this->orderItemRepo->findActiveOrdersByProductId($id, ['entregue', 'cancelado']);
        
        if (!empty($activeOrders)) {
            $orderNumbers = array_map(function($order) {
                return '#' . $order['id'];
            }, $activeOrders);
            
            Flash::push('danger', "Não é possível excluir o produto '{$product['name']}' porque ele está sendo usado em " . count($activeOrders) . " pedido(s) ativo(s) (Pedidos: " . implode(', ', $orderNumbers) . "). Finalize ou cancele os pedidos antes de excluir o produto.");
            return new RedirectResponse(Url::to('admin/products'));
        }

        // Se chegou aqui, só há pedidos entregues ou cancelados usando o produto (ou não há pedidos)
        // Busca todos os itens de pedido que usam este produto
        $allOrderItems = $this->orderItemRepo->findByProductId($id);
        
        try {
            // Exclui todos os itens de pedido que usam este produto
            if (!empty($allOrderItems)) {
                $this->orderItemRepo->deleteByProductId($id);
            }
            
            // Agora pode excluir o produto
            $deleted = $this->repo->delete($id);

            if ($deleted) {
                $message = 'Produto excluído com sucesso.';
                if (count($allOrderItems) > 0) {
                    $message .= ' ' . count($allOrderItems) . ' item(ns) de pedido(s) entregue(s) ou cancelado(s) também foram excluído(s).';
                }
                Flash::push('success', $message);
            } else {
                Flash::push('danger', 'Falha ao excluir o produto.');
            }
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                Flash::push('danger', "Não é possível excluir o produto '{$product['name']}' porque ele está sendo usado em pedidos que não podem ser excluídos.");
            } else {
                Flash::push('danger', 'Erro ao excluir o produto: ' . $e->getMessage());
            }
        }

        return new RedirectResponse(Url::to('admin/products'));
    }
}
