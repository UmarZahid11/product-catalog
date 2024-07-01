<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Product;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected Product $product;

    protected  function setUp(): void
    { 
        parent::setUp();
        $this->product = Product::factory()->create();
    }

    public function testGetProductEndpoint()
    {
        // Act
        $response = $this->get('/api/products/' . $this->product->id);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data', 'error']);
    }

     /**
     * Test storing an API product.
     *
     * @return void
     */
    public function testStoreApiProduct()
    {
        // Generate new data for creating the product
        $productData = [
            'name' => 'Test Product 1',
            'description' => 'test description',
            'price' => 9.99,
            'stock' => 10,
        ];

        // Send a POST request to store the product
        $response = $this->post('/api/products', $productData);

        // Assert that the request was successful (status code 201)
        $response->assertStatus(201);

        // Assert that the product was stored in the database with the provided data
        $this->assertDatabaseHas('products', [
            'name' => $productData['name'],
            'description' => $productData['description'],
            'price' => $productData['price'],
            'stock' => $productData['stock'],
        ]);
    }

     /**
     * Test updating an API product.
     *
     * @return void
     */
    public function testUpdateApiProduct()
    {
        // Create a product
        $product = Product::factory()->create();

        // Generate new data for updating the product
        $newData = [
            'name' => 'Test Product 1',
            'description' => 'test description',
            'price' => 9.99,
            'stock' => 10,
        ];

        // Send a PUT request to update the product
        $response = $this->put('/api/products/' . $product->id, $newData);

        // Assert that the request was successful (status code 200)
        $response->assertStatus(200);

        // Assert that the product was updated with the new data
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $newData['name'],
            'description' => $newData['description'],
            'price' => $newData['price'],
            'stock' => $newData['stock'],
        ]);
    }

    /**
     * Test deleting an API product.
     *
     * @return void
     */
    public function testDeleteApiProduct()
    {
        // Create a product
        $product = Product::factory()->create();

        // Send a DELETE request to delete the product
        $response = $this->delete('/api/products/' . $product->id);

        // Assert that the request was successful (status code 204)
        $response->assertStatus(204);

        // Assert that the product no longer exists in the database
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
}