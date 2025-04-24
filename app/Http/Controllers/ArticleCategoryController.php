<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use App\Models\ArticleCategory;
use App\Services\Article\ArticleCategoryService;

class ArticleCategoryController extends Controller
{
    /**
     * @var ArticleCategoryService
     */
    protected $articleCategoryService;

    /**
     * ArticleCategoryController constructor
     * @param ArticleCategoryService $articleCategoryService
     */
    public function __construct (ArticleCategoryService $articleCategoryService)
    {
        $this->articleCategoryService = $articleCategoryService;
    }

    /**
     * ArticleCategoryController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->articleCategoryService->index($request);
    }

    /**
     * ArticleCategoryController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->articleCategoryService->store($request);
    }

    /**
     * ArticleCategoryController show
     * @param  ArticleCategory $category
     * @param  Request $request
     * @return Response
     */
    public function show (ArticleCategory $category, Request $request): Response
    {
        return $this->articleCategoryService->show($category, $request);
    }

    /**
     * ArticleCategoryController update
     * @param  ArticleCategory $category
     * @param  Request $request
     * @return Response
     */
    public function update (ArticleCategory $category, Request $request): Response
    {
        return $this->articleCategoryService->update($category, $request);
    }

    /**
     * ArticleCategoryController destroy
     * @param  ArticleCategory $category
     * @param  Request $request
     * @return Response
     */
    public function destroy (ArticleCategory $category, Request $request): Response
    {
        return $this->articleCategoryService->destroy($category, $request);
    }
}
