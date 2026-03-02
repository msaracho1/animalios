<?php
declare(strict_types=1);

namespace App\Controllers\Vendor;

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
        $orders = (new OrderRepository())->paginateAdmin([], $page, 15);
        $statuses = (new OrderStatusRepository())->all();

        View::render('vendor.orders.index', compact('orders', 'statuses'));
    }

    public function updateStatus(Request $req, string $id): void
    {
        $data = $req->validate([
            'id_estado_pedido' => ['required', 'integer'],
        ]);

        $orderRepo = new OrderRepository();
        $order = $orderRepo->find((int)$id);
        if (!$order) {
            http_response_code(404);
            echo 'Pedido no encontrado';
            return;
        }

        $statusId = (int)$data['id_estado_pedido'];
        $orderRepo->updateStatus((int)$id, $statusId);

        $user = Auth::userOrFail();
        (new OrderHistoryRepository())->create((int)$id, (int)$user->id_usuario, $statusId, date('Y-m-d H:i:s'));

        Session::flash('success', 'Estado del pedido actualizado.');
        Response::redirect(route('vendor.orders.index'));
    }
}
