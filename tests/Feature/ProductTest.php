<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_product()
    {
        $response = $this->post('/add-product', [
            'name' => 'Test Product',
            'price' => 100,
            'description' => 'This is a test product.',
        ]);

        $response->assertStatus(200);
    }

    public function test_duplicate_product_name()
    {
        $this->post('/add-product', [
            'name' => 'Test Product',
            'price' => 100,
            'description' => 'This is a test product.',
        ]);

        $response = $this->post('/add-product', [
            'name' => 'Test Product',
            'price' => 200,
            'description' => 'This is another test product.',
        ]);

        $response->assertSessionHasErrors('name');
    }
}
