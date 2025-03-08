<?php

namespace App\Services;

use App\Interfaces\ProductInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class PlatziApiService implements ProductInterface
{
    public function addProduct(array $productData): Response
    {   
      // dd($productData);
        $response = Http::post('https://api.escuelajs.co/api/v1/products/', $productData); 
        dd($response->body());
        return response($response->body(), $response->status());
    }
}
