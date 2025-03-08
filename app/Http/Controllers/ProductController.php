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

    public function store(Request $request)
    {   
        dd($request->all());
        $request->validate([
            'name' => 'required|unique:products,name',
            'price' => 'required|numeric',
            'description' => 'required',
            'images' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $productData = $request->only(['name', 'price', 'description', 'images']);
    
        return $this->productService->addProduct($productData);
    }
}
