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
    private const UPLOAD_DIR = __DIR__ . '/../../../public/uploads/products';

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

        $data['imagen_url'] = $this->uploadImageFromRequest();
        $data['activo'] = 1;

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
        $repo = new ProductRepository();
        $product = $repo->find((int)$id);
        if (!$product) {
            http_response_code(404);
            echo 'Producto no encontrado';
            return;
        }

        $data = $req->validate([
            'nombre' => ['required','string','max:255'],
            'descripcion' => ['nullable','string'],
            'precio' => ['required','numeric','min:0'],
            'stock' => ['required','integer','min:0'],
            'id_marca' => ['required','integer'],
            'id_subcategoria' => ['required','integer'],
        ]);

        $uploadedImageUrl = $this->uploadImageFromRequest();
        $data['imagen_url'] = $uploadedImageUrl ?: (string)($product->imagen_url ?? '');
        $data['activo'] = (int)($product->activo ?? 1);

        if ($uploadedImageUrl && !empty($product->imagen_url)) {
            $oldFile = __DIR__ . '/../../../public' . (string)$product->imagen_url;
            if (is_file($oldFile)) {
                @unlink($oldFile);
            }
        }

        $repo->update((int)$id, $data);
        Session::flash('success', 'Producto actualizado.');
        Response::redirect(route('admin.products.index'));
    }

    private function uploadImageFromRequest(): ?string
    {
        $image = $_FILES['imagen'] ?? null;
        if (!is_array($image) || (int)($image['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ((int)($image['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || empty($image['tmp_name'])) {
            Session::flash('error', 'No se pudo subir la imagen.');
            Response::redirect($_SERVER['HTTP_REFERER'] ?? route('admin.products.index'));
            exit;
        }

        $mime = (string)mime_content_type((string)$image['tmp_name']);
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($allowed[$mime])) {
            Session::flash('error', 'Formato de imagen inválido. Solo JPG, PNG o WEBP.');
            Response::redirect($_SERVER['HTTP_REFERER'] ?? route('admin.products.index'));
            exit;
        }

        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0775, true);
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
        $target = rtrim(self::UPLOAD_DIR, '/') . '/' . $filename;

        if (!move_uploaded_file((string)$image['tmp_name'], $target)) {
            Session::flash('error', 'No se pudo guardar la imagen en el servidor.');
            Response::redirect($_SERVER['HTTP_REFERER'] ?? route('admin.products.index'));
            exit;
        }

        return '/uploads/products/' . $filename;
    }

    public function destroy(Request $req, string $id): void
    {
        (new ProductRepository())->delete((int)$id);
        Session::flash('success', 'Producto eliminado.');
        Response::redirect(route('admin.products.index'));
    }
}
