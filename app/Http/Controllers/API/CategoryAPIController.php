<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Interfaces\CategoryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;

class CategoryAPIController extends Controller
{
    /**
     * @var CategoryRepositoryInterface
     *
     */
    private $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index()
    {
        return response(["success" => true, "data" => $this->categoryRepository->getAllCategories(), "error" => []], 200);
    }

    public function show($categoryId)
    {
        $category = [];
        $success = false;
        $error = '';
        $statusCode = 200;

        if($categoryId) {
            $category = $this->categoryRepository->getCategoryById($categoryId);
            if($category->resource) {
                $success = true;
            } else {
                $success = false;
                $error = 'Failed to fetch the requested resource.';
            }
        }
        return response(["success" => $success, "data" => $category, "error" => [$error]], $statusCode);
    }

    public function store(Request $request)
    {
        $category = [];
        $success = false;
        $error = '';
        $statusCode = 200;

        $input = $request->only([
            'name',
        ]);
        try {
            $this->validate($request, [
                'name' => 'required',
            ]);
            $success = true;
            $category = $this->categoryRepository->createCategory($input);
        } catch (ValidationException $e) {
            $error = array_values($e->errors());
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return response(["success" => $success, "data" => $category, "error" => [$error]], $statusCode);
    }

    public function update(Request $request, $categoryId)
    {
        $category = [];
        $success = false;
        $error = '';
        $statusCode = 200;

        $input = $request->only([
            'name',
        ]);

        try {
            if($categoryId) {
                $this->validate($request, [
                    'name' => 'required',
                ]);

                $category = $this->categoryRepository->getCategoryById($categoryId);

                if($category->resource) {
                    $updated = $this->categoryRepository->updateCategory($categoryId, $input);
                    if($updated) {
                        $success = true;
                    }
                } else {
                    $error = 'Failed to fetch the requested resource.';
                }
            } else {
                $error = 'Provide a valid category Id!';
            }
        } catch (ValidationException $e) {
            $error = array_values($e->errors());
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return response(["success" => $success, "data" => $category, "error" => [$error]], $statusCode);
    }

    public function destroy($categoryId)
    {
        $category = [];
        $success = false;
        $error = '';
        $statusCode = 200;

        try {
            if($categoryId) {

                $category = $this->categoryRepository->getCategoryById($categoryId);

                if($category->resource) {
                    $deleted = $this->categoryRepository->deleteCategory($categoryId);
                    if($deleted) {
                        $success = true;
                    }
                } else {
                    $error = 'Failed to fetch the requested resource.';
                }
            } else {
                $error = 'Provide a valid category Id!';
            }
        } catch (ValidationException $e) {
            $error = array_values($e->errors());
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return response(["success" => $success, "data" => $category, "error" => [$error]], $statusCode);
    }
}
