<?php

namespace App\Services\Story;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Story;
use App\Traits\GlobalTrait;
use Carbon\Carbon;


class StoryService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * FaqService index
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {
        $records = Story::orderBy('date','DESC')
        ->when(isset($request->keyword), function ($query) use ($request) {
            $query->where('content', 'LIKE', '%' . strtolower($request->keyword).'%');	
        })
        ->when($request->filled('all') , function ($query, $request) {
            return $query->get();
        }, function ($query) {
            return $query->paginate(20);
        });

        return response([
            'records' => $records
        ]);
    }

    /**
     * FaqService store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {

        $columns = [];
        //validate youtube link
        if ($request->filled('link')) {
            $youtube = $this->validateYoutubeLink($request->link);
            if (!$youtube->valid) {
                return response([
                    'errors' => ['Youtube URL value is invalid.']
                ], 400);
            } else {
                $keyword = sprintf('%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s',
                    $request->title,
                    str_slug($request->title),
                    str_slug($request->title, '_'),
                    $request->title,
                    str_slug($request->title),
                    str_slug($request->title, '_'),
                    $youtube->title,
                    str_slug($youtube->title),
                    str_slug($youtube->title, '_'),
                    $request->title,
                    str_slug($request->title),
                    str_slug($request->title, '_'),
                );
                $columns = [
                    'title'             => $request->title,
                    'date'              => $request->date,
                    'enabled'           => $request->enabled,
                    'link'              => $request->link,
                    'featured'          => $request->featured,
                    'keyword'           => $keyword,
                    'yt_id'             => $youtube->youtubeId,
                    'yt_url'             => $request->link,
                    'yt_title'          => $youtube->title,
                    'yt_thumbnail'      => $youtube->thumbnail,
                    'yt_published_date' => Carbon::parse($youtube->published),
                ];
            }
        }

        //store
        $record = Story::create($columns);
        //add meta tag
        // $this->metatags($record, $request);
        //add thumbnail
        if ($request->hasFile('thumbnail')) {
            $this->addImages('story', $request, $record, 'thumbnail');
        }

        $this->generateLog($request->user(), "added this story ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * FaqService show
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function show ($story, $request): Response
    {

        //$testimonial->load('images');
        $story->load('images', 'metadata');

        $this->generateLog($request->user(), "viewed this story ({$story->id}).");

        return response([
            'record' => $story
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($story, $request): Response
    {
        $old = true;
        $columns = [];


        if ($request->filled('link')) {
            if ($story->yt_url != $request->link) {
                $youtube = $this->validateYoutubeLink($request->link);
                if (!$youtube->valid) {
                    return response([
                        'errors' => ['Youtube URL value is invalid.']
                    ], 400);
                } else {
                    $keyword = sprintf('%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s',
                        $request->title,
                        str_slug($request->title),
                        str_slug($request->title, '_'),
                        $request->title,
                        str_slug($request->title),
                        str_slug($request->title, '_'),
                        $youtube->title,
                        str_slug($youtube->title),
                        str_slug($youtube->title, '_'),
                        $request->title,
                        str_slug($request->title),
                        str_slug($request->title, '_'),
                    );
                    $columns = [
                        'title'             => $request->title,
                        'date'              => $request->date,
                        'enabled'           => $request->enabled,
                        'link'              => $request->link,
                        'featured'          => $request->featured,
                        'keyword'           => $keyword,
                        'yt_id'             => $youtube->youtubeId,
                        'yt_url'             => $request->link,
                        'yt_title'          => $youtube->title,
                        'yt_thumbnail'      => $youtube->thumbnail,
                        'yt_published_date' => Carbon::parse($youtube->published),
                    ];
                    $old = false;
                }
            }
        }
        if ($old) {
            $keyword = sprintf('%s,%s,%s,%s,%s,%s,%s,%s,%s',
                $request->title,
                str_slug($request->title),
                str_slug($request->title, '_'),
                $request->title,
                str_slug($request->title),
                str_slug($request->title, '_'),
                $request->title,
                str_slug($request->title),
                str_slug($request->title, '_'),
            );
            $columns = [
                'title'             => $request->title,
                'date'              => $request->date,
                'enabled'           => $request->enabled,

                'keyword'           => $keyword,
                
                'enabled'           => $request->enabled,
                'featured'          => $request->featured,
            ];
        }


        $story->update($columns);

        $this->updateImages('story', $request, $story, 'thumbnail');
        $this->metatags($story, $request);

        $this->generateLog($request->user(), "updated this story ({$story->id}).");

        return response([
            'record' => $story
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($story, $request): Response
    {
        $story->delete();
        $this->generateLog($request->user(), "deleted this story ({$story->id}).");

        return response([
            'record' => 'Story deleted'
        ]);
    }
}
