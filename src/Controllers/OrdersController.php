<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\View;
use App\Core\Response;
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
            Response::notFound('El pedido que buscás no existe.');
            return;
        }
        if ((int)$order->id_usuario !== (int)$user->id_usuario) {
            Response::notFound();
            return;
        }
        View::render('orders.show', compact('order'));
    }
}
