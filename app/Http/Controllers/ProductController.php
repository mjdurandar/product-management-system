<?php

namespace App\Http\Controllers;

use App\Interfaces\ProductInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
      $products = json_decode($this->productService->getProducts()->getContent()); 
      return view('product.index', compact('products'));
    }

    public function store(Request $request)
    {   
      // Test FTP connection by uploading a simple text file
    $success = Storage::disk('ftp')->put('test-upload.txt', 'Hello FTP!');

    // Check if the file was uploaded successfully
    if ($success) {
        return response()->json(['message' => 'FTP upload successful!']);
    } else {
        return response()->json(['error' => 'FTP upload failed!'], 500);
    }
      $request->validate([
          'name' => 'required|unique:products,name',
          'price' => 'required|numeric',
          'description' => 'required',
          'images' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
      ]);

      $productData = $request->only(['name', 'price', 'description']);
      // 🖼️ Upload Image to FTP and get URL
      if ($request->hasFile('images')) {
          $imagePath = $request->file('images')->store('products', 'ftp'); 
          $productData['image'] = Storage::disk('ftp')->url($imagePath);  // Get FTP URL
      }
      
      return $this->productService->addProduct($productData);
    }
}
