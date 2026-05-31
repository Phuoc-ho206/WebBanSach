<?php

require_once __DIR__ . '/../app/Controllers/Controllers.php';

/**
 * Router - Single Responsibility: điều hướng request
 */
class Router {
    private array $routes = [];

    public function add(string $method, string $pattern, callable $handler): void {
        $this->routes[] = compact('method', 'pattern', 'handler');
    }

    public function dispatch(string $method, string $uri): void {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method && $route['method'] !== 'ANY') continue;

            $regex = '#^' . preg_replace('/\{(\w+)\}/', '(\d+)', $route['pattern']) . '$#';
            if (preg_match($regex, $uri, $matches)) {
                array_shift($matches);
                call_user_func_array($route['handler'], $matches);
                return;
            }
        }
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Route not found']);
    }
}

// ── Bootstrap ─────────────────────────────────
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

$router = new Router();

// Lấy PATH_INFO: phần URI sau tên file index.php
// Ví dụ: /WebBanSach/sangxuan/api/index.php/books  →  /books
// Hoạt động trên WAMP không cần .htaccess
$uri = $_SERVER['PATH_INFO'] ?? '/';
$uri = rtrim($uri, '/') ?: '/';

$method = $_SERVER['REQUEST_METHOD'];

// ── Books ──────────────────────────────────────
$router->add('GET', '/books',              fn()    => (new BookController)->index());
$router->add('GET', '/books/best-sellers', fn()    => (new BookController)->bestSellers());
$router->add('GET', '/books/promotions',   fn()    => (new BookController)->promotions());
$router->add('GET', '/books/search',       fn()    => (new BookController)->search());
$router->add('GET', '/books/{id}',         fn($id) => (new BookController)->show((int)$id));

// ── Book Images ────────────────────────────────
$router->add('GET',    '/books/{id}/images',              fn($id)            => (new ImageController)->list((int)$id));
$router->add('POST',   '/books/{id}/image',               fn($id)            => (new ImageController)->upload((int)$id));
$router->add('DELETE', '/books/{bookId}/image/{imageId}', fn($bId, $iId)     => (new ImageController)->delete((int)$bId, (int)$iId));

// ── Categories ────────────────────────────────
$router->add('GET', '/categories',         fn()    => (new CategoryController)->index());

// ── Cart ──────────────────────────────────────
$router->add('GET',    '/cart',            fn()    => (new CartController)->get());
$router->add('POST',   '/cart',            fn()    => (new CartController)->add());
$router->add('PUT',    '/cart',            fn()    => (new CartController)->update());
$router->add('DELETE', '/cart',            fn()    => (new CartController)->remove());

// ── Orders ────────────────────────────────────
$router->add('POST', '/orders/checkout',   fn()    => (new OrderController)->checkout());
$router->add('GET',  '/orders/mine',       fn()    => (new OrderController)->myOrders());

// ── Auth ──────────────────────────────────────
$router->add('POST', '/auth/login',           fn() => (new AuthController)->login());
$router->add('POST', '/auth/register',        fn() => (new AuthController)->register());
$router->add('POST', '/auth/logout',          fn() => (new AuthController)->logout());
$router->add('GET',  '/auth/me',              fn() => (new AuthController)->me());
$router->add('POST', '/auth/forgot-password', fn() => (new AuthController)->forgotPassword());
$router->add('POST', '/auth/verify-otp',      fn() => (new AuthController)->verifyOtp());
$router->add('POST', '/auth/reset-password',  fn() => (new AuthController)->resetPassword());

$router->dispatch($method, $uri);
