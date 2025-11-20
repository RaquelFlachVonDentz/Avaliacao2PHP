<?php

namespace App\Controllers;

use App\Core\View;
use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SiteController
{
    private View $view;
    private ProductRepository $productRepo;
    private CategoryRepository $categoryRepo;

    public function __construct()
    {
        $this->view = new View();
        $this->productRepo = new ProductRepository();
        $this->categoryRepo = new CategoryRepository();
    }

    public function index(Request $request): Response
    {
        $products = $this->productRepo->findAll();
        $categories = $this->categoryRepo->getArray();
        
        // Limita a 6 produtos para a seção de promoções
        $featuredProducts = array_slice($products, 0, 6);
        
        $html = $this->view->render('site/index', [
            'products' => $products,
            'featuredProducts' => $featuredProducts,
            'categories' => $categories
        ]);
        return new Response($html);
    }
}
