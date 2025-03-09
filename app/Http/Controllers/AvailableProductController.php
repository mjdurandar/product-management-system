<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\ProductInterface;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class AvailableProductController extends Controller
{
    protected $productService;
    const CACHE_KEY = 'available_products_list';
    const CACHE_TTL = 3600; // Cache for 1 hour

    public function __construct(ProductInterface $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        // Get the selected API from session
        $selectedApi = session('selected_api', 'platzi');
        
        // Create a unique cache key based on the selected API
        $cacheKey = self::CACHE_KEY . '_' . $selectedApi;

        // Get products from cache or API
        $products = Cache::remember($cacheKey, self::CACHE_TTL, function () {
            $response = $this->productService->getProducts();
            return json_decode($response->getContent(), true);
        });
        
        return view('available-product.index', compact('products'));
    }

    public function claimProduct(Request $request, $id)
    {
        try {
            // Here you would implement the logic to assign the product to the user
            // For example, create a user_products table entry
            
            return response()->json([
                'message' => 'Product claimed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to claim product',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
