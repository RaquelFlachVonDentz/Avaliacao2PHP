<?php

namespace App\Controllers\Admin;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Url;
use App\Core\View;
use App\Repositories\ClientRepository;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Services\OrderService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController
{
    private View $view;
    private OrderRepository $repo;
    private OrderService $service;
    private ClientRepository $clientRepo;
    private ProductRepository $productRepo;
    private OrderItemRepository $itemRepo;

    public function __construct()
    {
        $this->view = new View();
        $this->repo = new OrderRepository();
        $this->service = new OrderService();
        $this->clientRepo = new ClientRepository();
        $this->productRepo = new ProductRepository();
        $this->itemRepo = new OrderItemRepository();
    }

    public function index(Request $request): Response
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $perPage = 10;
        $total = $this->repo->countAll();
        $orders = $this->repo->paginate($page, $perPage);
        $pages = (int)ceil($total / $perPage);

        $html = $this->view->render('admin/orders/index', compact('orders', 'page', 'pages'));
        return new Response($html);
    }

    public function create(): Response
    {
        $clients = $this->clientRepo->getArray();
        
        $html = $this->view->render('admin/orders/create', [
            'csrf' => Csrf::token(),
            'errors' => [],
            'old' => [],
            'clients' => $clients
        ]);
        return new Response($html);
    }

    public function store(Request $request): Response
    {
        if (!Csrf::validate($request->request->get('_csrf'))) {
            return new Response('Token CSRF inválido', 419);
        }

        $data = $request->request->all();
        $errors = $this->service->validate($data);

        if ($errors) {
            $clients = $this->clientRepo->getArray();
            $html = $this->view->render('admin/orders/create', [
                'csrf' => Csrf::token(),
                'errors' => $errors,
                'old' => $data,
                'clients' => $clients
            ]);
            return new Response($html, 422);
        }

        $order = $this->service->make($data);
        $id = $this->repo->create($order);

        Flash::push("success", "Pedido criado com sucesso!");
        return new RedirectResponse(Url::to('admin/orders/show?id=' . $id));
    }

    public function show(Request $request): Response
    {
        $id = (int)$request->query->get('id', 0);
        $order = $this->repo->find($id);
        if (!$order) {
            return new Response('Pedido não encontrado!', 404);
        }

        $items = $this->itemRepo->findByOrderId($id);

        $html = $this->view->render('admin/orders/show', [
            'order' => $order,
            'items' => $items
        ]);
        return new Response($html);
    }

    public function edit(Request $request): Response
    {
        $id = (int)$request->query->get('id', 0);
        $order = $this->repo->find($id);
        if (!$order) {
            return new Response('Pedido não encontrado!', 404);
        }

        $clients = $this->clientRepo->getArray();

        $html = $this->view->render('admin/orders/edit', [
            'order' => $order,
            'csrf' => Csrf::token(),
            'errors' => [],
            'clients' => $clients
        ]);
        return new Response($html);
    }

    public function update(Request $request): Response
    {
        if (!Csrf::validate($request->request->get('_csrf'))) {
            return new Response('Token CSRF inválido', 419);
        }

        $data = $request->request->all();
        $errors = $this->service->validate($data);

        if ($errors) {
            $clients = $this->clientRepo->getArray();
            $html = $this->view->render('admin/orders/edit', [
                'order' => array_merge($this->repo->find((int)$data['id']), $data),
                'csrf' => Csrf::token(),
                'errors' => $errors,
                'clients' => $clients
            ]);
            return new Response($html, 422);
        }

        $order = $this->service->make($data);
        if (!$order->id) {
            return new Response('ID inválido', 422);
        }

        $this->repo->update($order);

        Flash::push("success", "Pedido atualizado com sucesso!");
        return new RedirectResponse(Url::to('admin/orders/show?id=' . $order->id));
    }

    public function delete(Request $request): Response
    {
        if (!Csrf::validate($request->request->get('_csrf'))) {
            return new Response('Token CSRF inválido', 419);
        }

        $id = (int)$request->request->get('id', 0);

        if ($id <= 0) {
            Flash::push('danger', 'ID inválido para exclusão');
            return new RedirectResponse(Url::to('admin/orders'));
        }

        $deleted = $this->repo->delete($id);

        if ($deleted) {
            Flash::push('success', 'Pedido excluído com sucesso.');
        } else {
            Flash::push('danger', 'Falha ao excluir o pedido.');
        }

        return new RedirectResponse(Url::to('admin/orders'));
    }
}



