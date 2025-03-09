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
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'description' => 'required',
            'category_id' => 'required',
        ]);

        $product = Product::findOrFail($id);
        
        $productData = $request->only(['name', 'price', 'description', 'category_id']);
        
        if ($request->hasFile('images')) {
            if ($product->images && !str_contains($product->images, 'default-product.jpg')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $product->images));
            }
            
            $imagePath = $request->file('images')->store('products', 'public');
            $productData['images'] = Storage::url($imagePath);
        }

        $product->update($productData);

        // Clear the cache after updating a product
        $this->clearProductCache();

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        if ($product->user_id) {
            return response()->json([
                'error' => 'Cannot delete this product as it is assigned to a user'
            ], 403);
        }

        if ($product->images && !str_contains($product->images, 'default-product.jpg')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $product->images));
        }

        $product->delete();

        // Clear the cache after deleting a product
        $this->clearProductCache();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
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
