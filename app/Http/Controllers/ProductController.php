<?php

namespace App\Http\Controllers;

use App\Interfaces\ProductInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductInterface $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
      return view('product.index');
    }

    public function addProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:products,name',
            'price' => 'required|numeric',
            'description' => 'required',
            'images' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $productData = $request->only(['name', 'price', 'description', 'images']);
        // ... other product data ...

      //     "title" => "New Prsssoduct",
      //     "price" => 99,
      //     "description" => "An awesome product!",
      //     "images" => ["https://via.placeholder.com/150"], // Must be an array
      //     "categoryId" => 1 // Required category ID
      // ];
    

        return $this->productService->addProduct($productData);
    }
}
