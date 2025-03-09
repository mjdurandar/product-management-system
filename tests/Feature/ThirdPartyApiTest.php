<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\PlatziApiService;
use App\Services\FakeStoreApiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\WithFaker;

class ThirdPartyApiTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake();
    }

    public function test_platzi_api_add_product()
    {
        $platziService = new PlatziApiService();
        
        $productData = [
            'name' => $this->faker->unique()->productName,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'description' => $this->faker->paragraph,
            'image' => 'https://example.com/image.jpg'
        ];

        Http::fake([
            'https://api.escuelajs.co/api/v1/products' => Http::response([
                'id' => 1,
                'title' => $productData['name'],
                'price' => $productData['price'],
                'description' => $productData['description'],
                'images' => [$productData['image']],
                'createdAt' => now()->toISOString(),
                'updatedAt' => now()->toISOString(),
                'categoryId' => 1
            ], 201)
        ]);

        $response = $platziService->addProduct($productData);
        
        $this->assertEquals(201, $response->status());
        
        Http::assertSent(function ($request) use ($productData) {
            return $request->url() == 'https://api.escuelajs.co/api/v1/products' &&
                   $request['title'] == $productData['name'] &&
                   $request['price'] == $productData['price'] &&
                   $request['description'] == $productData['description'] &&
                   $request['images'] == [$productData['image']];
        });
    }

    public function test_fakestore_api_add_product()
    {
        $fakeStoreService = new FakeStoreApiService();
        
        $productData = [
            'name' => $this->faker->unique()->productName,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'description' => $this->faker->paragraph,
            'image' => 'https://example.com/image.jpg'
        ];

        Http::fake([
            'https://fakestoreapi.com/products' => Http::response([
                'id' => 1,
                'title' => $productData['name'],
                'price' => $productData['price'],
                'description' => $productData['description'],
                'image' => $productData['image'],
                'category' => 'electronics'
            ], 200)
        ]);

        $response = $fakeStoreService->addProduct($productData);
        
        $this->assertEquals(200, $response->status());
        
        Http::assertSent(function ($request) use ($productData) {
            return $request->url() == 'https://fakestoreapi.com/products' &&
                   $request['title'] == $productData['name'] &&
                   $request['price'] == $productData['price'] &&
                   $request['description'] == $productData['description'] &&
                   $request['image'] == $productData['image'];
        });
    }

    public function test_platzi_api_handles_error()
    {
        $platziService = new PlatziApiService();
        
        Http::fake([
            'https://api.escuelajs.co/api/v1/products' => Http::response([
                'error' => 'Invalid data'
            ], 400)
        ]);

        $response = $platziService->addProduct([
            'name' => '',  // Invalid data
            'price' => -1,
            'description' => '',
            'image' => 'invalid-url'
        ]);
        
        $this->assertEquals(400, $response->status());
    }

    public function test_fakestore_api_handles_error()
    {
        $fakeStoreService = new FakeStoreApiService();
        
        Http::fake([
            'https://fakestoreapi.com/products' => Http::response([
                'error' => 'Invalid data'
            ], 400)
        ]);

        $response = $fakeStoreService->addProduct([
            'name' => '',  // Invalid data
            'price' => -1,
            'description' => '',
            'image' => 'invalid-url'
        ]);
        
        $this->assertEquals(400, $response->status());
    }
} 