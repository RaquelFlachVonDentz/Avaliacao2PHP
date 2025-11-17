<?php

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Url;
use App\Core\View;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController
{
    private View $view;
    private UserRepository $repo;

    private AuthService $auth;

    public function __construct(){
        $this->view = new View();
        $this->auth = new AuthService();
    }

    function showLogin(): Response
    {
        $html = $this->view->render('auth/login', ['csrf' => Csrf::token()]);
        return new Response($html);
    }

    public function login(Request $req): Response
    {
        if (!Csrf::validate($req->request->get('_csrf'))) return new Response('CSRF inválido', 419);
        $email = (string)$req->request->get('email', '');
        $password = (string)$req->request->get('password', '');

        if (!$this->auth->attempt($email, $password)) {
            Flash::push('danger', 'Credenciais inválidas');
            return new RedirectResponse(Url::to('auth/login'));
        }

        Flash::push('success', 'Bem-vindo!');
        return new RedirectResponse(Url::to('admin'));
    }

    public function logout(): Response
    {
        $this->auth->logout();
        Flash::push('info', 'Sessão encerrada.');
        return new RedirectResponse(Url::to('auth/login'));
    }

    public function create(): Response
    {
        $email = 'teste@teste.com';
        
        // Verifica se o usuário já existe
        $existingUser = $this->auth->getUserRepository()->findByEmail($email);
        
        if ($existingUser) {
            Flash::push('info', 'Usuário de teste já existe! Use o e-mail: ' . $email . ' e senha: teste123 para fazer login.');
        } else {
            try {
                $id = $this->auth->register('Teste', $email, 'teste123');
                Flash::push('success', 'Usuário de teste criado com sucesso! ID: #' . $id);
            } catch (\PDOException $e) {
                // Se ainda assim der erro (duplicata), informa que já existe
                if ($e->getCode() === '23000') {
                    Flash::push('info', 'Usuário de teste já existe! Use o e-mail: ' . $email . ' e senha: teste123 para fazer login.');
                } else {
                    Flash::push('danger', 'Erro ao criar usuário: ' . $e->getMessage());
                }
            }
        }
        
        return new RedirectResponse(Url::to('auth/login'));
    }
}
