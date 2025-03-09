<?php

namespace App\Services;

use App\Interfaces\ProductInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlatziApiService implements ProductInterface
{   
    public function getProducts(): Response
    {
        $response = Http::get('https://api.escuelajs.co/api/v1/products');
        $products = json_decode($response->body(), true);
        
        // Remove the limit, return all products
        return response($products, $response->status());
    }

    public function addProduct(array $productData): Response
    {   
      $response = Http::post('https://api.escuelajs.co/api/v1/products/', [
          'title' => $productData['name'],
          'price' => $productData['price'],
          'description' => $productData['description'],
          'categoryId' => 1, // Set category dynamically if needed
          'images' => [$productData['image']] // Pass URL instead of file
      ]); 

      return response($response->body(), $response->status());
    }

    public function getCategories(): Response
    {
        $response = Http::get('https://api.escuelajs.co/api/v1/categories');
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

            // Format and sanitize data according to Platzi API requirements
            $data = [
                'title' => substr(trim($productData['title']), 0, 100), // Limit title length
                'price' => (float)$productData['price'],
                'description' => substr(trim($productData['description']), 0, 250), // Limit description length
                'images' => ['https://placeimg.com/640/480/any'] // Provide default image if none provided
            ];

            // Add categoryId if provided, default to 1 if not provided
            $data['categoryId'] = isset($productData['categoryId']) ? (int)$productData['categoryId'] : 1;

            // Add images if provided
            if (isset($productData['images']) && !empty($productData['images'])) {
                $data['images'] = is_array($productData['images']) ? $productData['images'] : [$productData['images']];
            }

            // Log the request data
            Log::info('Updating product on Platzi API', [
                'product_id' => $id,
                'request_data' => $data
            ]);

            $response = Http::put("https://api.escuelajs.co/api/v1/products/{$id}", $data);
            
            // Log the response
            Log::info('Platzi API update response', [
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
            Log::error('Error updating product on Platzi API', [
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
