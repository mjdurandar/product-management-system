<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create([
            'role' => 'admin'
        ]);
    }

    public function test_admin_can_view_products()
    {
        $this->actingAs($this->admin);

        $response = $this->get('/product');
        $response->assertStatus(200);
        $response->assertViewIs('product.index');
    }

    public function test_admin_can_create_product()
    {
        $this->actingAs($this->admin);

        Storage::fake('public');
        
        $productData = [
            'name' => $this->faker->unique()->productName,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'description' => $this->faker->paragraph,
            'images' => UploadedFile::fake()->image('product.jpg')
        ];

        $response = $this->post('/product', $productData);
        
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Product added successfully'
                 ]);

        // Verify the image was stored
        Storage::disk('public')->assertExists('products/' . $productData['images']->hashName());
    }

    public function test_cannot_create_product_with_duplicate_name()
    {
        $this->actingAs($this->admin);

        $productName = $this->faker->unique()->productName;
        
        // Create first product
        $this->post('/product', [
            'name' => $productName,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'description' => $this->faker->paragraph,
        ]);

        // Try to create second product with same name
        $response = $this->post('/product', [
            'name' => $productName,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'description' => $this->faker->paragraph,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name']);
    }

    public function test_admin_can_update_product()
    {
        $this->actingAs($this->admin);

        // Create a product first
        $response = $this->post('/product', [
            'name' => $this->faker->unique()->productName,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'description' => $this->faker->paragraph,
        ]);

        $product = json_decode($response->getContent())->product;

        // Update the product
        $updateData = [
            'title' => $this->faker->unique()->productName,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'description' => $this->faker->paragraph,
        ];

        $response = $this->put("/product/{$product->id}", $updateData);
        
        $response->assertStatus(200);
    }

    public function test_admin_can_delete_product()
    {
        $this->actingAs($this->admin);

        // Create a product first
        $response = $this->post('/product', [
            'name' => $this->faker->unique()->productName,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'description' => $this->faker->paragraph,
        ]);

        $product = json_decode($response->getContent())->product;

        // Delete the product
        $response = $this->delete("/product/{$product->id}");
        
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Product deleted successfully'
                 ]);
    }

    public function test_non_admin_cannot_access_product_management()
    {
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        $this->actingAs($user);

        $response = $this->get('/product');
        $response->assertStatus(403);

        $response = $this->post('/product', [
            'name' => $this->faker->unique()->productName,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'description' => $this->faker->paragraph,
        ]);
        $response->assertStatus(403);
    }
} 