<?php

namespace App\Services\Article;

use App\Models\Taxonomy;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\{
    Article,
    ArticleCategory
};
use App\Traits\GlobalTrait;

class ArticleService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * ArticleService index
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {
        $records = Article::select('id', 'title', 'category_id', 'date', 'enabled', 'featured', 'order', 'slug')
        ->orderBy(isset($request->sortBy) ? $request->sortBy : 'date', isset($request->sortDirection) ? $request->sortDirection : 'desc')
        ->with(['articleCategory'])

        ->when($request->filled('id'), function ($query) use ($request) {
            $query->where('id', '!=', $request->id);
        })
        
        ->when($request->filled('category'), function ($query) use ($request) {
            $query->whereHas('articleCategory', function ($query) use ($request) {
                $query->where('name', $request->category);
            });
        })

        ->when($request->filled('published'), function ($query) use ($request) {
            $query->where('enabled', $request->published);
        })

        ->when($request->filled('featured'), function ($query) use ($request) {
            $query->where('featured', $request->featured);
        })
        
        ->when(isset($request->keyword), function ($query) use ($request) {
            $query->where('keyword', 'LIKE', '%' . strtolower($request->keyword) . '%');
        })
        
        ->when($request->filled('all'), function ($query) {
            return $query->get();
        }, function ($query) {
            return $query->paginate(20);
        });

        return response([
            'records' => $records
        ]);
    }

    /**
     * ArticleService store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'title'               => 'required',
            'content'           => 'required',
            'date'                => 'required',
            'enabled'             => 'required',
            'featured'            => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value == 1 && Article::where('featured', 1)->count() >= 1) {
                        $fail('You can only have 1 featured items.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 402);
        }

        $category = Taxonomy::find($request->category_id);
        $keyword = sprintf('%s,%s,%s,%s,%s,%s',
            $request->title,
            str_slug($request->title),
            str_slug($request->title, '_'),
            $category->name,
            str_slug($category->name),
            str_slug($category->name, '_')
        );

        $record = Article::create([
            'category_id'       => $request->category_id,
            'title'             => $request->title,
            'content'           => $request->content,
            'keyword'           => $keyword,
            'date'              => $request->date,
            'enabled'           => $request->enabled,
            'featured'          => $request->featured,
            'slug'              => $this->slugify($request->title, 'Article'),
            'order'             => $request->order ?? Article::count() + 1
        ]);


        if ($request->hasFile('main_image')) {
            $this->addImages('article', $request, $record, 'main_image');
        }
        if ($request->hasFile('gallery')) {
            $this->addImages('article', $request, $record, 'gallery');
        }

        // if ($request->hasFile('gallery')) {
        //     $this->addImages('article', $request, $record, 'gallery');
        // }

        $this->metatags($record, $request);
        $this->generateLog($request->user(), "Created", "News & Articles", $record);

        return response([
            'record' => $record
        ]);
    }

    /**
     * ArticleService show
     * @param  Article $article
     * @param  Request $request
     * @return Response
     */
    public function show ($article, $request): Response
    {
        $article->load('articleCategory', 'images', 'metadata');
        $article->relateds = ($article->relateds) ? Article::select('id', 'title')
                            ->whereIn('id', json_decode($article->relateds))
                            ->get() : [];
        // $this->generateLog($request->user(), "viewed this article ({$article->id}).");

        return response([
            'record' => $article
        ]);
    }

    /**
     * ArticleService update
     * @param  Article $article
     * @param  Request $request
     * @return Response
     */
    public function update ($article, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'title'               => 'required',
            'content'             => 'required',
            'date'                => 'required',
            'enabled'             => 'required',
            'featured' => [
                'required',
                function ($attribute, $value, $fail) use ($article){

                    $currentItemId = $article->id;

                    // Get the count of currently featured items, excluding the current item being updated
                    $featuredCount = Article::where('featured', 1)
                        ->where('id', '!=', $currentItemId)
                        ->count();
                    if ($value == 1 && $featuredCount >= 1) {
                        $fail('You can only have 1 featured items.');
                    }
                },
            ],
            // 'main_image'          => 'required',
            // 'main_image.*'        => 'required|mimes:jpeg,png,jpg,webp|max:3000',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        // Check if title is updated
        $isTitleUpdated = $article->title !== $request->title;

        $category = Taxonomy::find($request->category_id);
        $keyword = sprintf('%s,%s,%s,%s,%s,%s',
            $request->title,
            str_slug($request->title),
            str_slug($request->title, '_'),
            $category->name,
            str_slug($category->name),
            str_slug($category->name, '_')
        );

        $article->update([
            'category_id' => $request->category_id,
            'title'               => $request->title,
            'content'             => $request->content,
            'keyword'             => $keyword,
            'date'                => $request->date,
            'enabled'             => $request->enabled,
            'featured'            => $request->featured,
            'order'             => $request->order ?? $article->order
        ]);

        if ($isTitleUpdated) {
            $article->update([
                'slug' => $this->slugify($request->title, 'Article'),
            ]);
        }
        // if ($request->hasFile('main_image')) {
            $this->updateImages('article', $request, $article, 'main_image');
        // }
        
        if ($request->hasFile('gallery')) {
            $this->updateImages('article', $request, $article, 'gallery');
        }

        $this->metatags($article, $request);

        $article->load(['images', 'metadata', 'articleCategory']);  
        $this->generateLog($request->user(), "Changed", "News & Articles", $article);

        return response([
            'record' => $article
        ]);
    }

    /**
     * ArticleService destroy
     * @param  Article $article
     * @param  Request $request
     * @return Response
     */
    public function destroy ($article, $request): Response
    {
        // if ($article->order !== Article::max('order')) {
        //     Article::where('order', '>', $article->order)
        //     ->chunkById(100, function ($articles) {
        //         foreach ($articles as $article) {
        //             $article->decrement('order');
        //         }
        //     });
        // }
        $this->generateLog($request->user(), "Deleted", "News & Articles", $article);
        $article->delete();
        // $this->reassignOrderValues('Article');

        return response([
            'record' => 'Article deleted'
        ]);
    }

    /**
     * ArticleService show
     * @param  Article $article
     * @param  Request $request
     * @return Response
     */
    public function getArticle ($request): Response
    { 
        $article = Article::where('slug', $request->slug)->first();
        $article->load('articleCategory', 'images', 'metadata');
        $article->relateds =  Article::select('id', 'title', 'category_id', 'date', 'enabled', 'featured', 'order', 'slug')
        ->orderBy('date', 'desc')
        ->where('id', '<>', $article->id)
        ->where('category_id', $article->category_id)
        ->where('enabled', 1)
        ->with('images')
        ->with('articleCategory')
        ->limit(3)
        ->get();
        // $this->generateLog($request->user(), "viewed this article ({$article->id}).");

        return response([
            'record' => $article
        ]);
    }


    public function getAll ($request): Response
    {
        $records = Article::select('id', 'title', 'category_id', 'date', 'enabled', 'featured', 'order', 'slug')
        ->orderBy(isset($request->sortBy) ? $request->sortBy : 'date', isset($request->sortDirection) ? $request->sortDirection : 'desc')
        ->with(['articleCategory', 'images'])

        ->when($request->filled('id'), function ($query) use ($request) {
            $query->where('id', '!=', $request->id);
        })
        
        ->when($request->filled('category'), function ($query) use ($request) {
            $query->whereCategoryId($request->category);
        })

        ->where('enabled', 1)
        
        ->when(isset($request->keyword), function ($query) use ($request) {
            $query->where('keyword', 'LIKE', '%' . strtolower($request->keyword) . '%');
        })
        
        ->paginate(6);

        return response([
            'records' => $records
        ]);
    }

    public function getArticleList ($request) :Response
    {
        $records = Article::select('title', 'slug')->get();
        return response([
            'records' => $records
        ]);
    }
}
