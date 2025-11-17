<?php
namespace App\Services;

use App\Models\Client;
use App\Repositories\ClientRepository;

class ClientService
{
    private ClientRepository $repo;

    public function __construct()
    {
        $this->repo = new ClientRepository();
    }
    
    public function validate(array $data): array
    {
        $errors = [];

        $name  = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $city  = trim($data['city'] ?? '');
        $state = strtoupper(trim($data['state'] ?? ''));
        $id    = isset($data['id']) ? (int)$data['id'] : null;

        if ($name === '') {
            $errors['name'] = 'Nome é obrigatório!';
        }

        if ($email === '') {
            $errors['email'] = 'E-mail é obrigatório!';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'E-mail inválido!';
        } else {
            $existing = $this->repo->findByEmail($email);
            if ($existing && (int)$existing['id'] !== $id) {
                $errors['email'] = 'Já existe um cliente cadastrado com este e-mail!';
            }
        }

        if ($phone === '') {
            $errors['phone'] = 'Telefone é obrigatório!';
        } else {
            $cleanPhone = preg_replace('/\D+/', '', $phone);
            if (strlen($cleanPhone) < 8 || strlen($cleanPhone) > 15) {
                $errors['phone'] = 'Telefone deve conter entre 8 e 15 dígitos!';
            }
        }

        if ($city === '') {
            $errors['city'] = 'Cidade é obrigatória!';
        }

        if ($state === '') {
            $errors['state'] = 'UF é obrigatória!';
        } elseif (strlen($state) !== 2) {
            $errors['state'] = 'UF deve conter 2 caracteres (ex: SP, RJ)!';
        }

        return $errors;
    }

    public function make(array $data): Client
    {
        $id = isset($data['id']) ? (int)$data['id'] : null;
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = $this->sanitizePhone($data['phone'] ?? '');
        $city = trim($data['city'] ?? '');
        $state = strtoupper(trim($data['state'] ?? ''));
        $created_at = $data['created_at'] ?? '';

        return new Client($id, $name, $email, $phone, $city, $state, $created_at);
    }

    private function sanitizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);
        return $digits ?: '';
    }
}
