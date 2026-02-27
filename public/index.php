<?php

// --- Simple PSR-4 autoloader (no Composer) ---
spl_autoload_register(function(string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) return;

    $relative = substr($class, strlen($prefix));
    $path = __DIR__ . '/../src/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($path)) require $path;
});

require __DIR__ . "/../src/Core/helpers.php";

use App\Core\Env;
use App\Core\Session;
use App\Core\Router;
use App\Core\View;
use App\Core\Auth;
use App\Core\Request;

Env::load(__DIR__ . '/../.env');
Session::start();
View::init(__DIR__ . '/../resources/views');

$router = new Router();

// --- Middlewares ---
$auth = function(Request $req) {
    if (!Auth::check()) {
        return App\Core\Response::redirect(route('login'));
    }
    return null;
};

$admin = function(Request $req) {
    $u = Auth::user();
    if (!$u || (($u->role->nombre ?? null) !== 'administrador')) {
        http_response_code(403);
        echo '403 - Forbidden';
        exit;
    }
    return null;
};

// --- Controllers ---
use App\Controllers\HomeController;
use App\Controllers\StoreController;
use App\Controllers\CartController;
use App\Controllers\OrdersController;
use App\Controllers\CheckoutController;
use App\Controllers\AuthController;
use App\Controllers\ProfileController;
use App\Controllers\Admin\UserController as AdminUserController;
use App\Controllers\Admin\ProductController as AdminProductController;
use App\Controllers\Admin\OrderController as AdminOrderController;

// Public
$router->get('/', [HomeController::class, 'index'])->name('home');
$router->get('/nosotros', [HomeController::class, 'about'])->name('about');
$router->get('/tienda', [StoreController::class, 'index'])->name('store.index');
$router->get('/producto/{id}', [StoreController::class, 'show'])->name('store.show');

// Auth
$router->get('/login', [AuthController::class, 'showLogin'])->name('login');
$router->post('/login', [AuthController::class, 'login'])->name('login.post');
$router->get('/registro', [AuthController::class, 'showRegister'])->name('register');
$router->post('/registro', [AuthController::class, 'register'])->name('register.post');
$router->post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware($auth);

// Cart + orders (auth)
$router->get('/carrito', [CartController::class, 'index'])->name('cart.index')->middleware($auth);
$router->post('/carrito/agregar', [CartController::class, 'add'])->name('cart.add')->middleware($auth);
$router->post('/carrito/quitar', [CartController::class, 'remove'])->name('cart.remove')->middleware($auth);
$router->post('/carrito/actualizar', [CartController::class, 'update'])->name('cart.update')->middleware($auth);

$router->get('/pedidos', [OrdersController::class, 'index'])->name('orders.index')->middleware($auth);
$router->get('/pedidos/{id}', [OrdersController::class, 'show'])->name('orders.show')->middleware($auth);

$router->post('/checkout', [CheckoutController::class, 'store'])->name('checkout')->middleware($auth);

// Profile
$router->get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit')->middleware($auth);
$router->post('/perfil', [ProfileController::class, 'update'])->name('profile.update')->middleware($auth);
$router->post('/perfil/password', [ProfileController::class, 'updatePassword'])->name('profile.password')->middleware($auth);

// Admin
$adminMw = fn(Request $req) => ($auth($req) ?? $admin($req));

$router->get('/admin/usuarios', [AdminUserController::class, 'index'])->name('admin.users.index')->middleware($adminMw);
$router->get('/admin/usuarios/crear', [AdminUserController::class, 'create'])->name('admin.users.create')->middleware($adminMw);
$router->post('/admin/usuarios', [AdminUserController::class, 'store'])->name('admin.users.store')->middleware($adminMw);
$router->get('/admin/usuarios/{id}/editar', [AdminUserController::class, 'edit'])->name('admin.users.edit')->middleware($adminMw);
$router->post('/admin/usuarios/{id}', [AdminUserController::class, 'update'])->name('admin.users.update')->middleware($adminMw);
$router->post('/admin/usuarios/{id}/borrar', [AdminUserController::class, 'destroy'])->name('admin.users.destroy')->middleware($adminMw);

$router->get('/admin/productos', [AdminProductController::class, 'index'])->name('admin.products.index')->middleware($adminMw);
$router->get('/admin/productos/crear', [AdminProductController::class, 'create'])->name('admin.products.create')->middleware($adminMw);
$router->post('/admin/productos', [AdminProductController::class, 'store'])->name('admin.products.store')->middleware($adminMw);
$router->get('/admin/productos/{id}/editar', [AdminProductController::class, 'edit'])->name('admin.products.edit')->middleware($adminMw);
$router->post('/admin/productos/{id}', [AdminProductController::class, 'update'])->name('admin.products.update')->middleware($adminMw);
$router->post('/admin/productos/{id}/borrar', [AdminProductController::class, 'destroy'])->name('admin.products.destroy')->middleware($adminMw);

$router->get('/admin/pedidos', [AdminOrderController::class, 'index'])->name('admin.orders.index')->middleware($adminMw);
$router->get('/admin/pedidos/{id}', [AdminOrderController::class, 'show'])->name('admin.orders.show')->middleware($adminMw);
$router->post('/admin/pedidos/{id}/estado', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.status')->middleware($adminMw);

// Dispatch
$router->dispatch(Request::fromGlobals());
