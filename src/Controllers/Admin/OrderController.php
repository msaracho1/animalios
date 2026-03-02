<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Repositories\OrderHistoryRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderStatusRepository;

final class OrderController
{
    public function index(Request $req): void
    {
        $page = max(1, (int)($req->query['page'] ?? 1));
        $filters = [
            'estado' => $req->query['estado'] ?? null,
            'dias' => $req->query['dias'] ?? null,
        ];

        $orders = (new OrderRepository())->paginateAdmin($filters, $page, 15);
        $estadosPosibles = (new OrderStatusRepository())->all();
        View::render('admin.orders.index', compact('orders','estadosPosibles','filters'));
    }

    public function show(Request $req, string $id): void
    {
        $order = (new OrderRepository())->findAdminFull((int)$id);
        if (!$order) {
            http_response_code(404);
            echo 'Pedido no encontrado';
            return;
        }
        $estadosPosibles = (new OrderStatusRepository())->all();
        View::render('admin.orders.show', compact('order','estadosPosibles'));
    }

    public function updateStatus(Request $req, string $id): void
    {
        $data = $req->validate([
            'id_estado_pedido' => ['required','integer'],
        ]);

        $order = (new OrderRepository())->find((int)$id);
        if (!$order) {
            http_response_code(404);
            echo 'Pedido no encontrado';
            return;
        }
        $user = Auth::userOrFail();
        $statusId = (int)$data['id_estado_pedido'];
        (new OrderRepository())->updateStatus((int)$order->id_pedido, $statusId);
        (new OrderHistoryRepository())->create((int)$order->id_pedido,(int)$user->id_usuario, $statusId, date('Y-m-d H:i:s'));
        Session::flash('success', 'Estado actualizado.');
        Response::redirect(route('admin.orders.show', ['id'=>$order->id_pedido]));
    }
}
