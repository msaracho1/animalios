<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Repositories\ProductRepository;

final class CartController
{
    public function index(Request $req): void
    {
        $cart = Session::get('cart', []);
        $total = 0.0;
        foreach ($cart as $item) {
            $total += ((float)$item['price'] * (int)$item['qty']);
        }
        View::render('cart.index', compact('cart','total'));
    }

    public function add(Request $req): void
    {
        $data = $req->validate([
            'id_producto' => ['required','integer'],
            'cantidad' => ['nullable','integer','min:1','max:99'],
        ]);

        $id = (int)$data['id_producto'];
        $qty = (int)($data['cantidad'] ?? 1);

        $product = (new ProductRepository())->find($id);
        if (!$product) {
            Session::flash('error', 'Producto inexistente.');
            Response::back();
        }

        $cart = Session::get('cart', []);
        if (!isset($cart[$id])) {
            $cart[$id] = [
                'id_producto' => $product->id_producto,
                'name' => $product->nombre,
                'price' => (float)$product->precio,
                'qty' => 0,
            ];
        }
        $cart[$id]['qty'] += $qty;
        Session::put('cart', $cart);
        Session::flash('success', 'Producto agregado al carrito.');
        Response::back();
    }

    public function update(Request $req): void
    {
        $items = $req->post['items'] ?? null;
        if (!is_array($items)) {
            Session::flash('error', 'Formato inválido.');
            Response::redirect(route('cart.index'));
        }

        $cart = Session::get('cart', []);
        foreach ($items as $item) {
            $id = (int)($item['id_producto'] ?? 0);
            $qty = (int)($item['qty'] ?? 0);
            if (!$id || !isset($cart[$id])) continue;
            if ($qty <= 0) unset($cart[$id]);
            else $cart[$id]['qty'] = min(99, $qty);
        }
        Session::put('cart', $cart);
        Session::flash('success', 'Carrito actualizado.');
        Response::redirect(route('cart.index'));
    }

    public function remove(Request $req): void
    {
        $data = $req->validate([
            'id_producto' => ['required','integer'],
        ]);
        $id = (int)$data['id_producto'];
        $cart = Session::get('cart', []);
        unset($cart[$id]);
        Session::put('cart', $cart);
        Session::flash('success', 'Producto quitado del carrito.');
        Response::redirect(route('cart.index'));
    }
}
