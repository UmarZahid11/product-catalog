<?php

namespace App\Repositories;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;
use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAllProducts(): ProductCollection
    {
        return new ProductCollection(Product::paginate());
    }

    public function getProductById($productId): ProductResource
    {
        return new ProductResource(Product::find($productId));
    }

    public function getProductByCategoryId($categoryId): ProductCollection
    {
        $product = Product::with(['categories' => function ($query) use ($categoryId) {
            return $query->where('category_id', $categoryId);
        }])->paginate();
        return new ProductCollection($product);
    }

    public function createProduct(array $productDetails): object
    {
        return Product::create($productDetails);
    }

    public function updateProduct($productId, array $productDetails): int
    {
        return Product::whereId($productId)->update($productDetails);
    }

    public function deleteProduct($productId): int
    {
        return Product::destroy($productId);
    }

    public function attachCategories($product, array $categories)
    {
        $product->categories()->sync($categories);
    }
}
