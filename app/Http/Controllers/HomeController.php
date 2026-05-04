<?php

namespace App\Http\Controllers;

use App\Repositories\ProductRepository;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private ProductRepository $productRepository
    ) {}

    /**
     * Display the homepage with featured brands, latest products, and best sellers.
     * Requirements: 13.1, 14.7
     */
    public function index(): View
    {
        // All data is cached in the repository layer [Req 14.7]
        $featuredBrands = $this->productRepository->getFeaturedBrands();
        $latestProducts = $this->productRepository->getLatestProducts(8);
        $bestSellers = $this->productRepository->getBestSellers(8);

        return view('home', compact('featuredBrands', 'latestProducts', 'bestSellers'));
    }
}
