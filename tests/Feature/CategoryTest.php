<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Category;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function testGetCategoryEndpoint()
    {
        $category = Category::factory()->create();

        // Act
        $response = $this->get('/api/categories/' . $category->id);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data', 'error']);
    }

    /**
     * Test storing an API category.
     *
     * @return void
     */
    public function testStoreApiCategory()
    {
        // Generate new data for creating the category
        $categoryData = [
            'name' => 'Test Category 1',
        ];

        // Send a POST request to store the category
        $response = $this->post('/api/categories', $categoryData);

        // Assert that the request was successful (status code 201)
        $response->assertStatus(201);

        // Assert that the category was stored in the database with the provided data
        $this->assertDatabaseHas('categories', [
            'name' => $categoryData['name'],
        ]);
    }

    /**
     * Test updating an API category.
     *
     * @return void
     */
    public function testUpdateApiCategory()
    {
        // Create a category
        $category = Category::factory()->create();

        // Generate new data for updating the category
        $newData = [
            'name' => 'Test Category 1',
        ];

        // Send a PUT request to update the category
        $response = $this->put('/api/categories/' . $category->id, $newData);

        // Assert that the request was successful (status code 200)
        $response->assertStatus(200);

        // Assert that the category was updated with the new data
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $newData['name'],
        ]);
    }

    /**
     * Test deleting an API category.
     *
     * @return void
     */
    public function testDeleteApiCategory()
    {
        // Create a category
        $category = Category::factory()->create();

        // Send a DELETE request to delete the category
        $response = $this->delete('/api/categories/' . $category->id);

        // Assert that the request was successful (status code 204)
        $response->assertStatus(204);

        // Assert that the category no longer exists in the database
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }
}
