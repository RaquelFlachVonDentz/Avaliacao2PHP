<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\ClientRepository;

class OrderService
{
    private ClientRepository $clientRepo;

    public function __construct()
    {
        $this->clientRepo = new ClientRepository();
    }

    public function validate(array $data): array
    {
        $errors = [];

        $client_id = isset($data['client_id']) ? (int)$data['client_id'] : 0;
        $order_date = trim($data['order_date'] ?? '');
        $status = trim($data['status'] ?? '');

        if ($client_id <= 0) {
            $errors['client_id'] = 'Cliente é obrigatório!';
        } else {
            $client = $this->clientRepo->find($client_id);
            if (!$client) {
                $errors['client_id'] = 'Cliente não encontrado!';
            }
        }

        if ($order_date === '') {
            $errors['order_date'] = 'Data do pedido é obrigatória!';
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $order_date)) {
            $errors['order_date'] = 'Data inválida! Use o formato YYYY-MM-DD';
        }

        $allowedStatuses = ['pendente', 'processando', 'enviado', 'entregue', 'cancelado'];
        if ($status === '') {
            $errors['status'] = 'Status é obrigatório!';
        } elseif (!in_array(strtolower($status), $allowedStatuses, true)) {
            $errors['status'] = 'Status inválido! Use: ' . implode(', ', $allowedStatuses);
        }

        return $errors;
    }

    public function make(array $data): Order
    {
        $id = isset($data['id']) ? (int)$data['id'] : null;
        $client_id = (int)($data['client_id'] ?? 0);
        $order_date = trim($data['order_date'] ?? date('Y-m-d'));
        $status = trim($data['status'] ?? 'pendente');

        return new Order($id, $client_id, $order_date, $status);
    }
}

