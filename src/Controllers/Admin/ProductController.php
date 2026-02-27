<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Repositories\BrandRepository;
use App\Repositories\SubcategoryRepository;
use App\Repositories\ProductRepository;

final class ProductController
{
    public function index(Request $req): void
    {
        $page = max(1, (int)($req->query['page'] ?? 1));
        // reuse search without filters
        $products = (new ProductRepository())->search([], $page, 15);
        View::render('admin.products.index', compact('products'));
    }

    public function create(Request $req): void
    {
        $brands = (new BrandRepository())->all();
        $subcategories = (new SubcategoryRepository())->all();
        View::render('admin.products.create', compact('brands','subcategories'));
    }

    public function store(Request $req): void
    {
        $data = $req->validate([
            'nombre' => ['required','string','max:255'],
            'descripcion' => ['nullable','string'],
            'precio' => ['required','numeric','min:0'],
            'stock' => ['required','integer','min:0'],
            'id_marca' => ['required','integer'],
            'id_subcategoria' => ['required','integer'],
        ]);

        (new ProductRepository())->create($data);
        Session::flash('success', 'Producto creado.');
        Response::redirect(route('admin.products.index'));
    }

    public function edit(Request $req, string $id): void
    {
        $repo = new ProductRepository();
        $product = $repo->find((int)$id);
        if (!$product) {
            http_response_code(404);
            echo 'Producto no encontrado';
            return;
        }
        $brands = (new BrandRepository())->all();
        $subcategories = (new SubcategoryRepository())->all();
        View::render('admin.products.edit', compact('product','brands','subcategories'));
    }

    public function update(Request $req, string $id): void
    {
        $data = $req->validate([
            'nombre' => ['required','string','max:255'],
            'descripcion' => ['nullable','string'],
            'precio' => ['required','numeric','min:0'],
            'stock' => ['required','integer','min:0'],
            'id_marca' => ['required','integer'],
            'id_subcategoria' => ['required','integer'],
        ]);

        (new ProductRepository())->update((int)$id, $data);
        Session::flash('success', 'Producto actualizado.');
        Response::redirect(route('admin.products.index'));
    }

    public function destroy(Request $req, string $id): void
    {
        (new ProductRepository())->delete((int)$id);
        Session::flash('success', 'Producto eliminado.');
        Response::redirect(route('admin.products.index'));
    }
}
