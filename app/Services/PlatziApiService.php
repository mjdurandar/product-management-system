<?php

namespace App\Services;

use App\Interfaces\ProductInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class PlatziApiService implements ProductInterface
{
    public function addProduct(array $productData): Response
    {   
        $response = Http::post('https://api.escuelajs.co/api/v1/products/', $productData); 

        return response($response->body(), $response->status());
    }

    public function getCategories(): Response
    {
        $response = Http::get('https://api.escuelajs.co/api/v1/categories');
        return response($response->body(), $response->status());
    }
}
