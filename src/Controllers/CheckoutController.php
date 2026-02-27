<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\DB;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\OrderRepository;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderHistoryRepository;

final class CheckoutController
{
    public function store(Request $req): void
    {
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            Session::flash('error', 'El carrito está vacío.');
            Response::redirect(route('cart.index'));
        }

        $user = Auth::userOrFail();

        DB::begin();
        try {
            $total = 0.0;
            foreach ($cart as $item) {
                $total += ((float)$item['price'] * (int)$item['qty']);
            }

            $now = date('Y-m-d H:i:s');
            $orderRepo = new OrderRepository();
            $orderId = $orderRepo->create((int)$user->id_usuario, $now, (float)$total);

            $itemRepo = new OrderItemRepository();
            foreach ($cart as $item) {
                $itemRepo->create($orderId, (int)$item['id_producto'], (int)$item['qty'], (float)$item['price']);
            }

            (new OrderHistoryRepository())->create($orderId,(int)$user->id_usuario, 1, $now);

            Session::forget('cart');
            DB::commit();

            Session::flash('success', 'Pedido realizado correctamente.');
            Response::redirect(route('orders.show', ['id'=>$orderId]));

        } catch (\Throwable $e) {
            DB::rollBack();

            // DEBUG TEMPORAL
            echo '<pre>';
            echo $e->getMessage() . "\n\n";
            echo $e->getTraceAsString();
            echo '</pre>';
            exit;
        }
    }
}
