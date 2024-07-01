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

    public function __construct(
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * index
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->apiResponse(true, $this->categoryRepository->getAllCategories(), "", 200);
    }

    /**
     * show
     *
     * @param integer $categoryId
     * @return Response
     */
    public function show(int $categoryId): Response
    {
        $category = NULL;
        $success = false;
        $error = '';
        $statusCode = 200;

        if ($categoryId) {
            try {
                $category = $this->categoryRepository->getCategoryById($categoryId);
                if ($category->resource) {
                    $success = true;
                } else {
                    $statusCode = 404;
                    $error = 'Failed to fetch the requested resource.';
                }
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        return $this->apiResponse($success, $category, $error, $statusCode);
    }

    /**
     * store
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $category = NULL;
        $success = false;
        $error = '';
        $statusCode = 400;

        $input = $request->only([
            'name',
        ]);
        try {
            $this->validate($request, [
                'name' => 'required',
            ]);
            $category = $this->categoryRepository->createCategory($input);
            if($category) {
                $success = true;
                $statusCode = 201;
            }
        } catch (ValidationException $e) {
            $error = [array_key_first($e->errors()) => ($e->errors()[array_key_first($e->errors())])];
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $this->apiResponse($success, $category, $error, $statusCode);
    }

    /**
     * update
     *
     * @param Request $request
     * @param integer $categoryId
     * @return Response
     */
    public function update(Request $request, int $categoryId): Response
    {
        $category = NULL;
        $success = false;
        $error = '';
        $statusCode = 400;

        $input = $request->only([
            'name',
        ]);

        try {
            if ($categoryId) {
                $this->validate($request, [
                    'name' => 'required',
                ]);

                $category = $this->categoryRepository->getCategoryById($categoryId);

                if ($category->resource) {
                    $updated = $this->categoryRepository->updateCategory($categoryId, $input);
                    if ($updated) {
                        $category = $this->categoryRepository->getCategoryById($categoryId);
                        $success = true;
                        $statusCode = 200;
                    }
                } else {
                    $error = 'Failed to fetch the requested resource.';
                }
            } else {
                $error = 'Provide a valid category Id!';
            }
        } catch (ValidationException $e) {
            $error = [array_key_first($e->errors()) => ($e->errors()[array_key_first($e->errors())])];
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $this->apiResponse($success, $category, $error, $statusCode);
    }

    /**
     * destroy
     *
     * @param integer $categoryId
     * @return Response
     */
    public function destroy(int $categoryId): Response
    {
        $category = NULL;
        $success = false;
        $error = '';
        $statusCode = 200;

        try {
            if ($categoryId) {

                $category = $this->categoryRepository->getCategoryById($categoryId);

                if ($category->resource) {
                    $deleted = $this->categoryRepository->deleteCategory($categoryId);
                    if ($deleted) {
                        $success = true;
                        $statusCode = 200;
                    }
                } else {
                    $error = 'Failed to fetch the requested resource.';
                }
            } else {
                $error = 'Provide a valid category Id!';
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $this->apiResponse($success, NULL, $error, $statusCode);
    }
}
