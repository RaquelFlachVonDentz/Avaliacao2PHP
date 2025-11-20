<?php

use App\Controllers\Admin\AdminController;
use App\Controllers\Admin\CategoryController;
use App\Controllers\Admin\ProductController;
use App\Controllers\Admin\ClientController;
use App\Controllers\Admin\OrderController;
use App\Controllers\Admin\UserController;
use App\Controllers\AuthController;
use App\Controllers\SiteController;
use App\Middleware\AuthMiddleware;
use Symfony\Component\HttpFoundation\Request;

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $routeCollector) {

    $routeCollector->addGroup('/', function (FastRoute\RouteCollector $site) {
        $site->addRoute('GET', '', [SiteController::class, 'index']);
    });

    $routeCollector->addGroup('/auth', function (FastRoute\RouteCollector $auth) {
        $auth->addRoute('GET', '/login', [AuthController::class, 'showLogin']);
        $auth->addRoute('GET', '/create', [AuthController::class, 'create']);
        $auth->addRoute('POST', '/login', [AuthController::class, 'login']);
        $auth->addRoute('POST', '/logout', [AuthController::class, 'logout']);
    });

    $routeCollector->addGroup('/admin', function (FastRoute\RouteCollector $group) {
        $group->addGroup('', function (FastRoute\RouteCollector $admin) {
            $admin->addRoute('GET', '', [AdminController::class, 'index']);
        });

        $group->addGroup('/products', function (FastRoute\RouteCollector $products) {
            $products->addRoute('GET', '', [ProductController::class, 'index']);
            $products->addRoute('GET', '/create', [ProductController::class, 'create']);
            $products->addRoute('POST', '/store', [ProductController::class, 'store']);
            $products->addRoute('GET', '/show', [ProductController::class, 'show']);
            $products->addRoute('GET', '/edit', [ProductController::class, 'edit']);
            $products->addRoute('POST', '/update', [ProductController::class, 'update']);
            $products->addRoute('POST', '/delete', [ProductController::class, 'delete']);
        });

        $group->addGroup('/categories', function (FastRoute\RouteCollector $categories) {
            $categories->addRoute('GET', '', [CategoryController::class, 'index']);
            $categories->addRoute('GET', '/create', [CategoryController::class, 'create']);
            $categories->addRoute('POST', '/store', [CategoryController::class, 'store']);
            $categories->addRoute('GET', '/show', [CategoryController::class, 'show']);
            $categories->addRoute('GET', '/edit', [CategoryController::class, 'edit']);
            $categories->addRoute('POST', '/update', [CategoryController::class, 'update']);
            $categories->addRoute('POST', '/delete', [CategoryController::class, 'delete']);
        });

        $group->addGroup('/clients', function (FastRoute\RouteCollector $clients) {
            $clients->addRoute('GET', '', [ClientController::class, 'index']);
            $clients->addRoute('GET', '/create', [ClientController::class, 'create']);
            $clients->addRoute('POST', '/store', [ClientController::class, 'store']);
            $clients->addRoute('GET', '/show', [ClientController::class, 'show']);
            $clients->addRoute('GET', '/edit', [ClientController::class, 'edit']);
            $clients->addRoute('POST', '/update', [ClientController::class, 'update']);
            $clients->addRoute('POST', '/delete', [ClientController::class, 'delete']);
        });

        $group->addGroup('/orders', function (FastRoute\RouteCollector $orders) {
            $orders->addRoute('GET', '', [OrderController::class, 'index']);
            $orders->addRoute('GET', '/create', [OrderController::class, 'create']);
            $orders->addRoute('POST', '/store', [OrderController::class, 'store']);
            $orders->addRoute('GET', '/show', [OrderController::class, 'show']);
            $orders->addRoute('GET', '/edit', [OrderController::class, 'edit']);
            $orders->addRoute('POST', '/update', [OrderController::class, 'update']);
            $orders->addRoute('POST', '/delete', [OrderController::class, 'delete']);
            // Rotas para itens do pedido
            $orders->addRoute('POST', '/store-item', [OrderController::class, 'storeItem']);
            $orders->addRoute('POST', '/update-item', [OrderController::class, 'updateItem']);
            $orders->addRoute('POST', '/delete-item', [OrderController::class, 'deleteItem']);
        });

        $group->addGroup('/users', function (FastRoute\RouteCollector $users) {
            $users->addRoute('GET', '', [UserController::class, 'index']);
            $users->addRoute('GET', '/create', [UserController::class, 'create']);
            $users->addRoute('POST', '/store', [UserController::class, 'store']);
            $users->addRoute('GET', '/show', [UserController::class, 'show']);
//            $users->addRoute('GET', '/edit', [UserController::class, 'edit']);
//            $users->addRoute('POST', '/update', [UserController::class, 'update']);
            $users->addRoute('POST', '/delete', [UserController::class, 'delete']);
        });
    });
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];


if (false !== $pos = strpos($uri, '?')) $uri = substr($uri, 0, $pos);
$uri = rawurldecode($uri);

$scriptPath = $_SERVER['SCRIPT_NAME']; 
if (strpos($scriptPath, '/public/') !== false) {
    $projectBase = substr($scriptPath, 0, strpos($scriptPath, '/public'));
    if ($projectBase !== '' && str_starts_with($uri, $projectBase)) {
        $uri = substr($uri, strlen($projectBase));
    }
}
if ($uri === '') $uri = '/';

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
$request = Request::createFromGlobals();

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '404';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo '405';
        break;
    case FastRoute\Dispatcher::FOUND:
        [$class, $method] = $routeInfo[1];
        $controller = new $class();

        $protectedRoutes = [
            '/admin',
        ];

        foreach ($protectedRoutes as $prefix) {
            if (str_starts_with($uri, $prefix)) {
                $redirect = AuthMiddleware::requireLogin();
                if ($redirect) { $redirect->send(); exit; }
                break;
            }
        }

        $response = $controller->$method($request);
        $response->send();
        break;
}