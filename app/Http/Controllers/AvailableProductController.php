<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\ProductInterface;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\UserProduct;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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

        // Get user's claimed product IDs
        $userProductIds = UserProduct::where('user_id', Auth::id())
            ->where('api_source', session('selected_api', 'platzi'))
            ->pluck('product_id')
            ->toArray();

        // Filter out claimed products
        $products = array_filter($products, function($product) use ($userProductIds) {
            return !in_array((string)$product['id'], $userProductIds);
        });
        
        return view('available-product.index', compact('products'));
    }

    public function claimProduct(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // Get the selected API from session
            $selectedApi = session('selected_api', 'platzi');
            
            // Get products from cache or API
            $cacheKey = self::CACHE_KEY . '_' . $selectedApi;
            $products = Cache::get($cacheKey);

            if (!$products) {
                // If products are not in cache, fetch them again
                $response = $this->productService->getProducts();
                $products = json_decode($response->getContent(), true);
            }

            // Find the product in the list
            $product = collect($products)->firstWhere('id', (string)$id);

            if (!$product) {
                throw new \Exception('Product not found');
            }

            // Check if user already has this product
            $exists = UserProduct::where('user_id', Auth::id())
                ->where('product_id', (string)$id)
                ->where('api_source', $selectedApi)
                ->exists();

            if ($exists) {
                throw new \Exception('You already have this product');
            }

            // Create user product entry
            UserProduct::create([
                'user_id' => Auth::id(),
                'product_id' => (string)$id,
                'title' => $product['title'],
                'description' => $product['description'],
                'price' => $product['price'],
                'image' => $product['image'] ?? ($product['images'][0] ?? null),
                'api_source' => $selectedApi
            ]);

            DB::commit();
            
            return response()->json([
                'message' => 'Product claimed successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'error' => 'Failed to claim product',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
