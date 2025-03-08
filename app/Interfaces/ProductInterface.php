<?php

namespace App\Interfaces;

use Illuminate\Http\Response;

interface ProductInterface
{
    public function addProduct(array $productData): Response;
}
