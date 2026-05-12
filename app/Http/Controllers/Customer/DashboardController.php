<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Repositories\ProductRepository;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private ProductRepository $productRepository
    ) {}

    /**
     * Display the customer dashboard with latest and best-selling products.
     */
    public function index(): View
    {
        $latestProducts = $this->productRepository->getLatestProducts(10);
        $bestSellerProducts = $this->productRepository->getBestSellers(10);

        return view('dashboard', compact('latestProducts', 'bestSellerProducts'));
    }
}
