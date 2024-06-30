<?php

namespace App\Repositories;

use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Interfaces\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAllProducts() {
        return ProductResource::collection(Product::all());
    }

    public function getProductById($productId) {
        return new ProductResource(Product::findOrFail($productId));
    }
        
    public function createProduct(array $productDetails) {
        return Product::create($productDetails);
    }
    
    public function updateProduct($productId, array $productDetails) {
        return Product::whereId($productId)->update($productDetails);
    }

    public function deleteProduct($productId) {
        return Product::destroy($productId);
    }
}