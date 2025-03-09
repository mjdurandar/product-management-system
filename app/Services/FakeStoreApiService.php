<?php

namespace App\Services;

use App\Interfaces\ProductInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

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
        // Format data according to Fake Store API requirements
        $data = [
            'title' => $productData['title'] ?? null,
            'price' => isset($productData['price']) ? (float)$productData['price'] : null,
            'description' => $productData['description'] ?? null,
            'category' => $productData['category'] ?? 'electronics'
        ];

        // Remove null values
        $data = array_filter($data, function($value) {
            return !is_null($value);
        });

        $response = Http::put("https://fakestoreapi.com/products/{$id}", $data);
        return response($response->body(), $response->status());
    }
}
