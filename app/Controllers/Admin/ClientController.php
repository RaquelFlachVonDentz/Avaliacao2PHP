<?php

namespace App\Controllers\Admin;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Url;
use App\Core\View;
use App\Repositories\ClientRepository;
use App\Repositories\OrderRepository;
use App\Services\ClientService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientController
{
    private View $view;
    private ClientRepository $repo;
    private ClientService $service;
    private OrderRepository $orderRepo;

    public function __construct()
    {
        $this->view = new View();
        $this->repo = new ClientRepository();
        $this->service = new ClientService();
        $this->orderRepo = new OrderRepository();
    }

    public function index(Request $request): Response
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $perPage = 10;
        $total = $this->repo->countAll();
        $clients = $this->repo->paginate($page, $perPage);
        $pages = (int)ceil($total / $perPage);

        $html = $this->view->render('admin/clients/index', compact('clients', 'page', 'pages'));
        return new Response($html);
    }

    public function create(): Response
    {
        $html = $this->view->render('admin/clients/create', [
            'csrf' => Csrf::token(),
            'errors' => [],
            'old' => []
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
            $html = $this->view->render('admin/clients/create', [
                'csrf' => Csrf::token(),
                'errors' => $errors,
                'old' => $data
            ]);
            return new Response($html, 422);
        }

        $client = $this->service->make($data);
        $id = $this->repo->create($client);

        Flash::push("success", "Cliente criado com sucesso!");
        return new RedirectResponse(Url::to('admin/clients/show?id=' . $id));
    }

    public function show(Request $request): Response
    {
        $id = (int)$request->query->get('id', 0);
        $client = $this->repo->find($id);
        if (!$client) {
            return new Response('Cliente não encontrado!', 404);
        }

        $html = $this->view->render('admin/clients/show', ['client' => $client]);
        return new Response($html);
    }

    public function edit(Request $request): Response
    {
        $id = (int)$request->query->get('id', 0);
        $client = $this->repo->find($id);
        if (!$client) {
            return new Response('Cliente não encontrado!', 404);
        }

        $html = $this->view->render('admin/clients/edit', [
            'client' => $client,
            'csrf' => Csrf::token(),
            'errors' => []
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
            $html = $this->view->render('admin/clients/edit', [
                'client' => array_merge($this->repo->find((int)$data['id']), $data),
                'csrf' => Csrf::token(),
                'errors' => $errors
            ]);
            return new Response($html, 422);
        }

        $client = $this->service->make($data);
        if (!$client->id) {
            return new Response('ID inválido', 422);
        }

        $this->repo->update($client);

        Flash::push("success", "Cliente atualizado com sucesso!");
        return new RedirectResponse(Url::to('admin/clients/show?id=' . $client->id));
    }

    public function delete(Request $request): Response
    {
        if (!Csrf::validate($request->request->get('_csrf'))) {
            return new Response('Token CSRF inválido', 419);
        }

        $id = (int)$request->request->get('id', 0);

        if ($id <= 0) {
            Flash::push('danger', 'ID inválido para exclusão');
            return new RedirectResponse(Url::to('admin/clients'));
        }

        $client = $this->repo->find($id);
        if (!$client) {
            Flash::push('danger', 'Cliente não encontrado.');
            return new RedirectResponse(Url::to('admin/clients'));
        }

        // Verifica se há pedidos ativos (exceto entregues e cancelados)
        $activeOrdersCount = $this->orderRepo->countByClientId($id, ['entregue', 'cancelado']);
        
        if ($activeOrdersCount > 0) {
            $activeOrders = $this->orderRepo->findByClientId($id, ['entregue', 'cancelado']);
            $orderNumbers = array_map(function($order) {
                return '#' . $order['id'];
            }, $activeOrders);
            
            Flash::push('danger', "Não é possível excluir o cliente '{$client['name']}' porque ele possui {$activeOrdersCount} pedido(s) ativo(s) (Pedidos: " . implode(', ', $orderNumbers) . "). Finalize ou cancele os pedidos antes de excluir o cliente.");
            return new RedirectResponse(Url::to('admin/clients'));
        }

        // Se chegou aqui, só há pedidos entregues ou cancelados (ou não há pedidos)
        // Busca todos os pedidos do cliente para excluí-los primeiro
        $allOrders = $this->orderRepo->findByClientId($id);
        
        try {
            // Exclui todos os pedidos do cliente (entregues e cancelados)
            foreach ($allOrders as $order) {
                $this->orderRepo->delete($order['id']);
            }
            
            // Agora pode excluir o cliente
            $deleted = $this->repo->delete($id);

            if ($deleted) {
                $message = 'Cliente excluído com sucesso.';
                if (count($allOrders) > 0) {
                    $message .= ' ' . count($allOrders) . ' pedido(s) entregue(s) ou cancelado(s) também foram excluído(s).';
                }
                Flash::push('success', $message);
            } else {
                Flash::push('danger', 'Falha ao excluir o cliente.');
            }
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                Flash::push('danger', "Não é possível excluir o cliente '{$client['name']}' porque ele possui pedidos associados que não podem ser excluídos.");
            } else {
                Flash::push('danger', 'Erro ao excluir o cliente: ' . $e->getMessage());
            }
        }

        return new RedirectResponse(Url::to('admin/clients'));
    }
}