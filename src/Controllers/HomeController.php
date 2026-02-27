<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Core\Request;
use App\Repositories\BrandRepository;
use App\Repositories\ProductRepository;

final class HomeController
{
    public function index(Request $req): void
    {
        $featured = (new ProductRepository())->featured(8);
        $brands = (new BrandRepository())->all();
        View::render('home', compact('featured','brands'));
    }

    public function about(Request $req): void
    {
        View::render('about');
    }
}
