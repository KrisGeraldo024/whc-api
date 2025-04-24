<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use App\Models\Article;
use App\Services\Article\ArticleService;

class ArticleController extends Controller
{
    /**
     * @var ArticleService
     */
    protected $articleService;

    /**
     * ArticleController constructor
     * @param ArticleService $articleService
     */
    public function __construct (ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    /**
     * ArticleController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->articleService->index($request);
    }

    /**
     * ArticleController store
     * @param  Request $request
     * @return Response
     */
    public function store (Request $request): Response
    {
        return $this->articleService->store($request);
    }

    /**
     * ArticleController show
     * @param  Article $article
     * @param  Request $request
     * @return Response
     */
    public function show (Article $article, Request $request): Response
    {
        return $this->articleService->show($article, $request);
    }

    /**
     * ArticleController update
     * @param  Article $article
     * @param  Request $request
     * @return Response
     */
    public function update (Article $article, Request $request): Response
    {
        return $this->articleService->update($article, $request);
    }

    /**
     * ArticleController destroy
     * @param  Article $article
     * @param  Request $request
     * @return Response
     */
    public function destroy (Article $article, Request $request): Response
    {
        return $this->articleService->destroy($article, $request);
    }

    /**
     * ArticleController show
     * @param  Article $article
     * @param  Request $request
     * @return Response
     */
    public function getArticle (Request $request): Response
    {
        return $this->articleService->getArticle($request);
    }
    
    public function getAll (Request $request): Response
    {
        return $this->articleService->getAll($request);
    }
    
    public function getArticleList (Request $request): Response
    {
        return $this->articleService->getArticleList($request);
    }
}
