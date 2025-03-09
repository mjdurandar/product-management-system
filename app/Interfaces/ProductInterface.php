<?php

namespace App\Interfaces;

use Illuminate\Http\Response;

interface ProductInterface
{
    public function getProducts(): Response;
    public function addProduct(array $productData): Response;
    public function getCategories(): Response;
    public function updateProduct($id, array $productData): Response;
    public function deleteProduct($id): Response;
}
