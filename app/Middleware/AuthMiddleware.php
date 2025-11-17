<?php

namespace App\Middleware;

use App\Core\Url;
use App\Services\AuthService;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AuthMiddleware
{
    /**
     * Garante que o usuário esteja logado.
     * Retorna RedirectResponse para /admin/login se não estiver autenticado.
     */
    public static function requireLogin(): ?RedirectResponse
    {
        if (!AuthService::check()) {
            return new RedirectResponse(Url::to('auth/login'));
        }
        return null; // sessão válida
    }
}
