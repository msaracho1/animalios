<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;
use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\SubcategoryRepository;
use App\Repositories\BrandRepository;

final class StoreController
{
    public function index(Request $req): void
    {
        $page = max(1, (int)($req->query['page'] ?? 1));

        $filters = [
            'id_categoria' => $req->query['id_categoria'] ?? null,
            'id_subcategoria' => $req->query['id_subcategoria'] ?? null,
            'id_marca' => $req->query['id_marca'] ?? null,
            'q' => $req->query['q'] ?? null,
        ];

        $products = (new ProductRepository())->search($filters, $page, 12);
        $categories = (new CategoryRepository())->all();
        $subcategories = (new SubcategoryRepository())->all();
        $brands = (new BrandRepository())->all();

        View::render('store.list', compact('products','categories','subcategories','brands','filters'));
    }

    public function show(Request $req, string $id): void
    {
        $product = (new ProductRepository())->findWithRelations((int)$id);
        if (!$product) {
            http_response_code(404);
            echo 'Producto no encontrado';
            return;
        }
        View::render('store.show', compact('product'));
    }
}
