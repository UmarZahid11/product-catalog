<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Models\Product;
use App\Models\Category;

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

    public function __construct(
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * index
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->apiResponse(true, $this->productRepository->getAllProducts(), "", 200);
    }

    /**
     * show
     *
     * @param integer $productId
     * @return Response
     */
    public function show(int $productId): Response
    {
        $product = NULL;
        $success = false;
        $error = NULL;
        $statusCode = 200;

        if ($productId) {
            try {
                $product = $this->productRepository->getProductById($productId);
                if ($product->resource) {
                    $success = true;
                } else {
                    $statusCode = 404;
                    $error = 'Failed to fetch the requested resource.';
                }
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }
        return $this->apiResponse($success, $product, $error, $statusCode);
    }

    /**
     * stroe
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $product = NULL;
        $success = false;
        $error = '';
        $statusCode = 400;

        $input = $request->only([
            'name',
            'price',
            'stock',
            'description'
        ]);

        try {
            $this->validate($request, [
                'name' => 'required',
                'price' => 'required',
                'stock' => 'required',
            ]);
            $product = $this->productRepository->createProduct($input);
            if($product) {
                $success = true;
                $statusCode = 201;
            }
        } catch (ValidationException $e) {
            $error = [array_key_first($e->errors()) => ($e->errors()[array_key_first($e->errors())])];
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $this->apiResponse($success, $product, $error, $statusCode);
    }

    /**
     * update
     *
     * @param Request $request
     * @param integer $productId
     * @return Response
     */
    public function update(Request $request, int $productId): Response
    {
        $product = NULL;
        $success = false;
        $error = '';
        $statusCode = 400;

        $input = $request->only([
            'name',
            'price',
            'stock',
            'description'
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
                        $product = $this->productRepository->getProductById($productId);
                        $success = true;
                        $statusCode = 200;
                    }
                } else {
                    $error = 'Failed to fetch the requested resource.';
                }
            } else {
                $error = 'Provide a valid product Id!';
            }
        } catch (ValidationException $e) {
            $error = [array_key_first($e->errors()) => ($e->errors()[array_key_first($e->errors())])];
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $this->apiResponse($success, $product, $error, $statusCode);
    }

    /**
     * destory
     *
     * @param integer $productId
     * @return Response
     */
    public function destroy(int $productId): Response
    {
        $product = NULL;
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
                        $statusCode = 200;
                    }
                } else {
                    $error = 'Failed to fetch the requested resource.';
                }
            } else {
                $error = 'Provide a valid product Id!';
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $this->apiResponse($success, NULL, $error, $statusCode);
    }

    /**
     * saveProductCategories
     *
     * @param Request $request
     * @return Response
     */
    public function saveProductCategories(Request $request): Response
    {
        $product = NULL;
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
                        $statusCode = 201;
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
            $error = [array_key_first($e->errors()) => ($e->errors()[array_key_first($e->errors())])];
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $this->apiResponse($success, NULL, $error, $statusCode);
    }

    function filterProducts(Request $request): Response
    {
        $data = NULL;
        $success = false;
        $error = '';
        $statusCode = 400;

        try {
            $this->validate($request, [
                'categoryId' => 'required',
            ]);

            $categoryId = isset($request->categoryId) ? $request->categoryId : 0;
            $category = $this->categoryRepository->getCategoryById($categoryId);

            if ($category->resource) {
                $data = $this->productRepository->getProductByCategoryId($categoryId);
                if($data->resource) {
                    $success = true;
                    $statusCode = 200;
                }
            } else {
                $error = 'Failed to fetch the requested resource.';
            }
        } catch (ValidationException $e) {
            $error = [array_key_first($e->errors()) => ($e->errors()[array_key_first($e->errors())])];
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $this->apiResponse($success, $data, $error, $statusCode);
    }
}
