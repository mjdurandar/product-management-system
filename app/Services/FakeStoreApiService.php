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
        $response = Http::put("https://fakestoreapi.com/products/{$id}", [
            'title' => $productData['title'],
            'price' => $productData['price'],
            'description' => $productData['description'],
            'category' => $productData['category'] ?? "electronics",
            'image' => $productData['image'] ?? 'https://i.pravatar.cc'
        ]);

        return response($response->body(), $response->status());
    }

    public function deleteProduct($id): Response
    {
        $response = Http::delete("https://fakestoreapi.com/products/{$id}");
        return response($response->body(), $response->status());
    }
}
