<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\View;
use App\Repositories\OrderRepository;

final class OrdersController
{
    public function index(Request $req): void
    {
        $user = Auth::userOrFail();
        $orders = (new OrderRepository())->listForUser((int)$user->id_usuario);
        View::render('orders.index', compact('orders'));
    }

    public function show(Request $req, string $id): void
    {
        $user = Auth::userOrFail();
        $order = (new OrderRepository())->findWithItemsAndHistory((int)$id);
        if (!$order) {
            http_response_code(404);
            echo 'Pedido no encontrado';
            return;
        }
        if ((int)$order->id_usuario !== (int)$user->id_usuario) {
            http_response_code(403);
            echo '403 - Forbidden';
            return;
        }
        View::render('orders.show', compact('order'));
    }
}
