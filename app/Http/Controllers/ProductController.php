<?php

namespace App\Http\Controllers;

use App\Interfaces\ProductInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Services\PlatziApiService;
use App\Services\FakeStoreApiService;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $productService;
    const CACHE_KEY = 'products_list';
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
            $products = json_decode($response->getContent(), true);
            
            // Sort products by created_at in descending order (newest first)
            if (is_array($products)) {
                usort($products, function($a, $b) {
                    $dateA = isset($a['created_at']) ? strtotime($a['created_at']) : time();
                    $dateB = isset($b['created_at']) ? strtotime($b['created_at']) : time();
                    return $dateB - $dateA;
                });
            }
            
            return $products;
        });
        
        return view('product.index', compact('products'));
    }

    public function store(Request $request)
    {   
        $request->validate([
            'name' => 'required|unique:products,name',
            'price' => 'required|numeric',
            'description' => 'required',
            'images' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $productData = $request->only(['name', 'price', 'description']);
        
        try {
            if ($request->hasFile('images')) {
                $imagePath = $request->file('images')->store('products', 'public');
                $productData['image'] = Storage::url($imagePath);
            }
        } catch (\Exception $e) {
            // Handle the exception (e.g., log it, notify, etc.)
            return response()->json([
                'error' => 'An error occurred while processing the image'
            ], 500);
        }
        
        $response = $this->productService->addProduct($productData);
        $newProduct = json_decode($response->getContent(), true);
        
        if (!isset($newProduct['created_at'])) {
            $newProduct['created_at'] = now()->toISOString();
        }

        // Clear the cache after adding a new product
        $this->clearProductCache();

        return response()->json([
            'message' => 'Product added successfully',
            'product' => $newProduct
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'title' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'required|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $updateData = $request->only(['title', 'price', 'description', 'categoryId']);
            
            // Handle image upload if provided
            if ($request->hasFile('images')) {
                // Store the image locally
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    // For now, use a placeholder image URL that the API will accept
                    $updateData['images'] = [
                        'https://i.imgur.com/ZANVnHE.jpeg',
                        'https://i.imgur.com/QkIa5tT.jpeg',
                        'https://i.imgur.com/YyUxEjY.jpeg'
                    ];
                }
            }

            // Get the selected API
            $selectedApi = session('selected_api', 'platzi');

            // Add category if it's FakeStore API
            if ($selectedApi === 'fakestore' && $request->has('category')) {
                $updateData['category'] = $request->input('category');
            }

            // Log the request data
            Log::info('Updating product with data', [
                'product_id' => $id,
                'request_data' => $updateData
            ]);

            // Make the API call
            $response = $this->productService->updateProduct($id, $updateData);
            
            // Clear the cache
            $this->clearProductCache();

            // Return the response
            return response()->json(json_decode($response->getContent(), true));

        } catch (\Exception $e) {
            Log::error('Error updating product', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'error' => 'Failed to update product',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Make the API call to delete the product
            $response = $this->productService->deleteProduct($id);
            
            // Clear the cache after deleting a product
            $this->clearProductCache();

            // Return the response
            return response()->json([
                'message' => 'Product deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting product', [
                'error' => $e->getMessage(),
                'product_id' => $id
            ]);

            return response()->json([
                'error' => 'Failed to delete product',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function switchApi(Request $request)
    {
        $api = $request->input('api');
        
        session(['selected_api' => $api]);
        
        if ($api === 'platzi') {
            app()->bind(ProductInterface::class, PlatziApiService::class);
        } else {
            app()->bind(ProductInterface::class, FakeStoreApiService::class);
        }

        // Clear the cache when switching APIs
        $this->clearProductCache();

        // Get fresh products from the newly selected API
        $response = app(ProductInterface::class)->getProducts();
        $products = json_decode($response->getContent(), true);

        // Sort products by created_at in descending order
        if (is_array($products)) {
            usort($products, function($a, $b) {
                $dateA = isset($a['created_at']) ? strtotime($a['created_at']) : time();
                $dateB = isset($b['created_at']) ? strtotime($b['created_at']) : time();
                return $dateB - $dateA;
            });
        }

        return response()->json([
            'message' => 'API switched successfully',
            'products' => $products
        ]);
    }

    /**
     * Clear all product-related caches
     */
    private function clearProductCache()
    {
        Cache::forget(self::CACHE_KEY . '_platzi');
        Cache::forget(self::CACHE_KEY . '_fakestore');
    }
}
