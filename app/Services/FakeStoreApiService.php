<?php

namespace App\Services;

use App\Interfaces\ProductInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class FakeStoreApiService implements ProductInterface
{
    public function addProduct(array $productData): Response
    {
        $response = Http::post('https://fakestoreapi.com/products', $productData);
        dd($response->body());
        return response($response->body(), $response->status());
    }
}
