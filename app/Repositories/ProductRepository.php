<?php

namespace App\Repositories;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\ProductResource;
use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAllProducts(): AnonymousResourceCollection
    {
        return ProductResource::collection(Product::all());
    }

    public function getProductById($productId): ProductResource
    {
        return new ProductResource(Product::find($productId));
    }

    public function createProduct(array $productDetails): int
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
