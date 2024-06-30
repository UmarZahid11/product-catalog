<?php

namespace App\Repositories;

use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Interfaces\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAllCategories() {
        return CategoryResource::collection(Category::all());
    }

    public function getCategoryById($categoryId) {
        return new CategoryResource(Category::find($categoryId));
    }
        
    public function createCategory(array $productDetails) {
        return Category::create($productDetails);
    }
    
    public function updateCategory($categoryId, array $productDetails) {
        return Category::whereId($categoryId)->update($productDetails);
    }

    public function deleteCategory($categoryId) {
        return Category::destroy($categoryId);
    }
}