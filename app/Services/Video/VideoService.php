<?php

namespace App\Services\Video;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\{
    Video,
    VideoCategory
};
use App\Traits\GlobalTrait;
use Carbon\Carbon;

class VideoService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * VideoService index
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {
        $records = Video::orderBy('created_at')
        ->with('videoCategory')
        ->when($request->filled('hearing_id'), function ($query) {
            $query->whereNotNull('yt_url');
        })
        ->when($request->filled('category'), function ($query) use ($request) {
            $query->whereVideoCategoryId($request->category);
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
     * VideoService store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {
        $validator = Validator::make($request->all(), [
            'video_category_id' => 'required',
            'enabled'           => 'required',
            'main_image'        => 'sometimes',
            'main_image.*'      => 'sometimes|mimes:jpg,jpeg,png,webp|max:3000'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $columns = [];
        $category = VideoCategory::find($request->video_category_id);

        if ($request->filled('youtube_url')) {
            $youtube = $this->validateYoutubeLink($request->youtube_url);
            if (!$youtube->valid) {
                return response([
                    'errors' => ['Youtube URL value is invalid.']
                ], 400);
            } else {
                $keyword = sprintf('%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s',
                    $request->title,
                    str_slug($request->title),
                    str_slug($request->title, '_'),
                    $request->subtitle,
                    str_slug($request->subtitle),
                    str_slug($request->subtitle, '_'),
                    $youtube->title,
                    str_slug($youtube->title),
                    str_slug($youtube->title, '_'),
                    $category->title,
                    str_slug($category->title),
                    str_slug($category->title, '_')
                );
                $columns = [
                    'video_category_id' => $request->video_category_id,
                    'title'             => $request->title,
                    'subtitle'          => $request->subtitle,
                    'description'       => $request->description,
                    'keyword'           => $keyword,
                    'yt_id'             => $youtube->youtubeId,
                    'yt_url'            => $request->youtube_url,
                    'yt_title'          => $youtube->title,
                    'yt_thumbnail'      => $youtube->thumbnail,
                    'yt_published_date' => Carbon::parse($youtube->published),
                    'enabled'           => $request->enabled,
                    'featured'          => $request->featured,
                    'slug'              => $this->slugify($youtube->title, 'Video')
                ];
            }
        } 
        
        // else {
        //     $keyword = sprintf('%s,%s,%s,%s,%s,%s,%s,%s,%s',
        //         $request->title,
        //         str_slug($request->title),
        //         str_slug($request->title, '_'),
        //         $request->subtitle,
        //         str_slug($request->subtitle),
        //         str_slug($request->subtitle, '_'),
        //         $category->title,
        //         str_slug($category->title),
        //         str_slug($category->title, '_')
        //     );
        //     $columns = [
        //         'video_category_id' => $request->video_category_id,
        //         'title'             => $request->title,
        //         'subtitle'          => $request->subtitle,
        //         'description'       => $request->description,
        //         'keyword'           => $keyword,
        //         'enabled'           => $request->enabled,
        //         'featured'          => $request->featured,
        //         'slug'              => $this->slugify($request->title, 'Video')
        //     ];
        // }

        $record = Video::create($columns);

        if ($request->hasFile('main_image')) {
            $this->addImages('video', $request, $record, 'main_image');
        }
    
        $this->generateLog($request->user(), "added this video ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * VideoService show
     * @param  Video $video
     * @param  Request $request
     * @return object
     */
    public function show ($video, $request): object
    {
        $video->load('images');
        $this->generateLog($request->user(), "viewed this video ({$video->id}).");

        return $this->recordExist($video);
    }

    /**
     * VideoService update
     * @param  Video $video
     * @param  Request $request
     * @return Response
     */
    public function update ($video, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'video_category_id' => 'required',
            'enabled'           => 'required',
            'main_image'        => 'sometimes',
            'main_image.*'      => 'sometimes|mimes:jpg,jpeg,png,webp|max:3000'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $old = true;
        $columns = [];
        $category = VideoCategory::find($request->video_category_id);

        if ($request->filled('youtube_url')) {
            if ($video->yt_url != $request->youtube_url) {
                $youtube = $this->validateYoutubeLink($request->youtube_url);
                if (!$youtube->valid) {
                    return response([
                        'errors' => ['Youtube URL value is invalid.']
                    ], 400);
                } else {
                    $keyword = sprintf('%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s',
                        $request->title,
                        str_slug($request->title),
                        str_slug($request->title, '_'),
                        $request->subtitle,
                        str_slug($request->subtitle),
                        str_slug($request->subtitle, '_'),
                        $youtube->title,
                        str_slug($youtube->title),
                        str_slug($youtube->title, '_'),
                        $category->title,
                        str_slug($category->title),
                        str_slug($category->title, '_')
                    );
                    $columns = [
                        'video_category_id' => $request->video_category_id,
                        'title'             => $request->title,
                        'subtitle'          => $request->subtitle,
                        'description'       => $request->description,
                        'keyword'           => $keyword,
                        'yt_id'             => $youtube->youtubeId,
                        'yt_url'            => $request->youtube_url,
                        'yt_title'          => $youtube->title,
                        'yt_thumbnail'      => $youtube->thumbnail,
                        'yt_published_date' => Carbon::parse($youtube->published),
                        'enabled'           => $request->enabled,
                        'featured'          => $request->featured,
                        'slug'              => $this->slugify($youtube->title, 'Video')
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
                $request->subtitle,
                str_slug($request->subtitle),
                str_slug($request->subtitle, '_'),
                $category->title,
                str_slug($category->title),
                str_slug($category->title, '_')
            );
            $columns = [
                'video_category_id' => $request->video_category_id,
                'title'             => $request->title,
                'subtitle'          => $request->subtitle,
                'description'       => $request->description,
                'keyword'           => $keyword,
                'enabled'           => $request->enabled,
                'featured'          => $request->featured,
                'slug'              => $this->slugify($request->title, 'Video')
            ];
        }

        $video->update($columns);

        if ($request->has('main_image_alt')) {
            $this->updateImages('video', $request, $video, 'main_image');
        }
    
        $this->generateLog($request->user(), "updated this video ({$video->id}).");

        return response([
            'record' => $video
        ]);
    }

    /**
     * VideoService destroy
     * @param  Video $video
     * @param  Request $request
     * @return Response
     */
    public function destroy ($video, $request): Response
    {
        $video->delete();
        $this->generateLog($request->user(), "deleted this video ({$video->id}).");

        return response([
            'record' => 'Video deleted'
        ]);
    }
}
