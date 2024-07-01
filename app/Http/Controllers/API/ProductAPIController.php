<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Product;
use Illuminate\Http\Response;

class ProductAPIController extends Controller
{
    /**
     * @var ProductRepositoryInterface
     *
     */
    private $productRepository;

    /**
     * @var CategoryRepositoryInterface
     *
     */
    private $categoryRepository;

    public function __construct(ProductRepositoryInterface $productRepository, CategoryRepositoryInterface $categoryRepository)
    {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function index(): Response
    {
        return response(["success" => true, "data" => $this->productRepository->getAllProducts(), "error" => []], 200);
    }

    public function show(int $productId): Response
    {
        $product = [];
        $success = false;
        $error = NULL;
        $statusCode = 200;

        if ($productId) {
            try {
                $product = $this->productRepository->getProductById($productId);
                if ($product->resource) {
                    $success = true;
                } else {
                    $success = false;
                    $error = 'Failed to fetch the requested resource.';
                }
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }
        return response(["success" => $success, "data" => $product, "error" => [$error]], $statusCode);
    }

    public function store(Request $request): Response
    {
        $product = [];
        $success = false;
        $error = '';
        $statusCode = 200;

        $input = $request->only([
            'name',
            'price',
            'stock'
        ]);
        try {
            $this->validate($request, [
                'name' => 'required',
                'price' => 'required',
                'stock' => 'required',
            ]);
            $success = true;
            $product = $this->productRepository->createProduct($input);
        } catch (ValidationException $e) {
            $error = array_values($e->errors());
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return response(["success" => $success, "data" => $product, "error" => [$error]], $statusCode);
    }

    public function update(Request $request, int $productId): Response
    {
        $product = [];
        $success = false;
        $error = '';
        $statusCode = 200;

        $input = $request->only([
            'name',
            'price',
            'stock'
        ]);

        try {
            if ($productId) {
                $this->validate($request, [
                    'name' => 'required',
                    'price' => 'required',
                    'stock' => 'required',
                ]);

                $product = $this->productRepository->getProductById($productId);

                if ($product->resource) {
                    $updated = $this->productRepository->updateProduct($productId, $input);
                    if ($updated) {
                        $success = true;
                    }
                } else {
                    $error = 'Failed to fetch the requested resource.';
                }
            } else {
                $error = 'Provide a valid product Id!';
            }
        } catch (ValidationException $e) {
            $error = array_values($e->errors());
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return response(["success" => $success, "data" => $product, "error" => [$error]], $statusCode);
    }

    public function destroy(int $productId): Response
    {
        $product = [];
        $success = false;
        $error = '';
        $statusCode = 200;

        try {
            if ($productId) {

                $product = $this->productRepository->getProductById($productId);

                if ($product->resource) {
                    $deleted = $this->productRepository->deleteProduct($productId);
                    if ($deleted) {
                        $success = true;
                    }
                } else {
                    $error = 'Failed to fetch the requested resource.';
                }
            } else {
                $error = 'Provide a valid product Id!';
            }
        } catch (ValidationException $e) {
            $error = array_values($e->errors());
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return response(["success" => $success, "data" => $product, "error" => [$error]], $statusCode);
    }

    public function saveProductCategories(Request $request): Response
    {
        $product = [];
        $success = false;
        $error = '';
        $statusCode = 200;
        $available = 0;

        try {
            $this->validate($request, [
                'productId' => 'required',
            ]);
            $productId = isset($request->productId) ? $request->productId : 0;
            if ($productId) {
                $product = $this->productRepository->getProductById($productId);

                $categories = isset($request->categories) && is_array($request->categories) ? $request->categories : [];
                if ($product->resource) {
                    $fetched_product = Product::find($productId);
                    foreach ($categories as $category) {
                        $fetched_category = $this->categoryRepository->getCategoryById($category);
                        if ($fetched_category->resource) {
                            $available++;
                        }
                    }
                    if ($available === count($categories)) {
                        $success = true;
                        $this->productRepository->attachCategories($fetched_product, $categories);
                    } else {
                        $error = 'Failed to fetch one or more requested resource.';
                    }
                } else {
                    $error = 'Failed to fetch the requested resource.';
                }
            } else {
                $error = 'Provide a valid product Id!';
            }
        } catch (ValidationException $e) {
            $error = array_values($e->errors());
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return response(["success" => $success, "data" => [], "error" => [$error]], $statusCode);
    }
}
