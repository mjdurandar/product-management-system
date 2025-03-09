<?php

namespace App\Services;

use App\Interfaces\ProductInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

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
        // Format data according to Platzi API requirements
        $data = [
            'title' => $productData['title'] ?? null,
            'price' => isset($productData['price']) ? (float)$productData['price'] : null,
            'description' => $productData['description'] ?? null
        ];

        // Remove null values as Platzi API allows partial updates
        $data = array_filter($data, function($value) {
            return !is_null($value);
        });

        $response = Http::put("https://api.escuelajs.co/api/v1/products/{$id}", $data);
        return response($response->body(), $response->status());
    }
}
