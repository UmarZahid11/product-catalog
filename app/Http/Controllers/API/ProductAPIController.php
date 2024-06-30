<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Interfaces\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;

class ProductAPIController extends Controller
{
    /**
     * @var ProductRepositoryInterface
     *
     */
    private $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index()
    {
        return response(["success" => true, "data" => $this->productRepository->getAllProducts(), "error" => []], 200);
    }

    public function show($productId)
    {
        $product = [];
        $success = false;
        $error = '';
        $statusCode = 200;

        if($productId) {
            $product = $this->productRepository->getProductById($productId);
            $success = true;
        }
        return response(["success" => $success, "data" => $product, "error" => [$error]], $statusCode);
    }

    public function store(Request $request)
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

    public function update(Request $request, $productId)
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
            if($productId) {
                $this->validate($request, [
                    'name' => 'required',
                    'price' => 'required',
                    'stock' => 'required',
                ]);

                $product = $this->productRepository->getProductById($productId);

                if($product) {
                    $updated = $this->productRepository->updateProduct($productId, $input);
                    if($updated) {
                        $success = true;
                    }
                } else {
                    $error = 'An error occurred while trying to process your request!';
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

    // ?_method=DELETE
    public function destroy($productId)
    {
        $product = [];
        $success = false;
        $error = '';
        $statusCode = 200;

        try {
            if($productId) {

                $product = $this->productRepository->getProductById($productId);

                if($product) {
                    $deleted = $this->productRepository->deleteProduct($productId);
                    if($deleted) {
                        $success = true;
                    }
                } else {
                    $error = 'An error occurred while trying to process your request!';
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
}
