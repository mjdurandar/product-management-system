<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\ProductInterface;

class ApiCategoriesController extends Controller
{
  protected $productService;

  public function __construct(ProductInterface $productService)
  {
      $this->productService = $productService;
  }

  public function index()
  {
      return $this->productService->getCategories();
  }
}
