<?php

namespace App\Repositories;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Interfaces\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAllCategories(): AnonymousResourceCollection
    {
        return CategoryResource::collection(Category::all());
    }

    public function getCategoryById($categoryId): CategoryResource
    {
        return new CategoryResource(Category::find($categoryId));
    }

    public function createCategory(array $productDetails): int
    {
        return Category::create($productDetails);
    }

    public function updateCategory($categoryId, array $productDetails): int
    {
        return Category::whereId($categoryId)->update($productDetails);
    }

    public function deleteCategory($categoryId): int
    {
        return Category::destroy($categoryId);
    }
}
