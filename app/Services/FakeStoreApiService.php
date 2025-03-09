<?php

namespace App\Services;

use App\Interfaces\ProductInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FakeStoreApiService implements ProductInterface
{
    public function addProduct(array $productData): Response
    {
        $response = Http::post('https://fakestoreapi.com/products', [
            'title' => $productData['name'],
            'price' => $productData['price'],
            'description' => $productData['description'],
            'category' => "electronics", // Set category dynamically if needed
            'image' => $productData['image'] // Pass URL instead of file
        ]);
        return response($response->body(), $response->status());
    }

    public function getCategories(): Response
    {
        $response = Http::get('https://fakestoreapi.com/products/categories');
        return response($response->body(), $response->status());
    }

    public function getProducts(): Response
    {
        $response = Http::get('https://fakestoreapi.com/products');
        return response($response->body(), $response->status());
    }

    public function updateProduct($id, array $productData): Response
    {
        try {
            // Validate required fields
            if (!isset($productData['title']) || !isset($productData['price']) || !isset($productData['description'])) {
                return response([
                    'error' => 'Failed to update product',
                    'message' => 'Missing required fields: title, price, and description are required'
                ], 422);
            }

            // Format data according to Fake Store API requirements
            $data = [
                'title' => $productData['title'],
                'price' => (float)$productData['price'],
                'description' => $productData['description'],
                'category' => $productData['category'] ?? 'electronics'
            ];

            // Add image if provided
            if (isset($productData['image'])) {
                $data['image'] = $productData['image'];
            }

            // Log the request data
            Log::info('Updating product on FakeStore API', [
                'product_id' => $id,
                'request_data' => $data
            ]);

            $response = Http::put("https://fakestoreapi.com/products/{$id}", $data);
            
            // Log the response
            Log::info('FakeStore API update response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                return response($response->body(), $response->status());
            }

            // If update failed, return error response with more details
            return response([
                'error' => 'Failed to update product',
                'message' => $response->body(),
                'status_code' => $response->status(),
                'request_data' => $data
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Error updating product on FakeStore API', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'data' => $productData
            ]);

            return response([
                'error' => 'Failed to update product',
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}
