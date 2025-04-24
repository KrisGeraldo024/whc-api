<?php

namespace App\Services\Property;

use App\Models\Button;
use App\Models\Feature;
use App\Models\Image;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Property;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    DB,
    Storage,
    Validator
};
use App\Models\Unit;
use App\Models\Taxonomy;
use App\Models\Video;
use App\Traits\GlobalTrait;
use Carbon\Carbon;
use Exception;

class UnitService
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
        $records = Unit::select('id', 'order', 'name', 'unit_type', 'enabled', 'updated_at')
        ->orderBy(isset($request->sortBy) ? $request->sortBy : 'created_at', isset($request->sortDirection) ? $request->sortDirection : 'desc')
        ->where('parent_id', $request->property)
        // ->where('property_type', ($request->unitType  === 'house-and-lots' ? 'House & Lot' : 'Condominium'))
        ->when(isset($request->keyword), function ($query) use ($request) {
            $query->where('name', 'LIKE', '%' . strtolower($request->keyword).'%');	
        })
        ->when(isset($request->location), function ($query) use ($request) {
            $query->whereHas('locations', function ($q) use ($request){
                $q->where('name', $request->location);
            });
        })
        ->with('unitType')
        ->when($request->filled('all') , function ($query, $request) {
            return $query->get();
        }, function ($query) {
            return $query->paginate(10);
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
        DB::beginTransaction();
        try{
            $property = Property::find($request->parent_id);
            $record = Unit::create([
                'parent_id'     => $request->parent_id,
                'name'          => $request->title ?? Taxonomy::where('type', Taxonomy::TYPE_UNIT)->where('id', $request->unit_type_id)->first()->name,
                'slug'          => $this->slugify($request->title ?? Taxonomy::where('type', Taxonomy::TYPE_UNIT)->where('id', $request->unit_type_id)->first()->name, 'Unit'),
                'order'         => $request->order ?? Unit::where('parent_id', $request->parent_id)->count() + 1,
                'location'      => $request->address ?? $property->address,
                'starts_at'     => $request->starts_at ?? '',
                'unit_type'     => $request->unit_type_id,
                'floor_area'    => $request->floor_area,
                'lot_area'      => $request->lot_area,
                'bedroom'       => $request->bedrooms,
                't_and_b'       => $request->t_and_b,
                'storeys'       => $request->storeys,
                'powder_room'   => $request->powder_rooms,
                'enabled'       => $request->enabled ?? 1,
                'tracking_code' => $request->tracking_code ?? '',
                'gmap_url'      => $request->gmap_url ?? $property->gmaps_link 
            ]); 

            //VIDEO Walkthrouh
            if ($request->filled('yt_url')) {
                $youtube = $this->validateYoutubeLink($request->yt_url);
                if (!$youtube->valid) {
                    return response([
                        'errors' => ['Youtube URL value is invalid.']
                    ], 400);
                }
                else {
                    $keyword = sprintf('%s,%s,%s,%s,%s,%s,%s,%s,%s',
                        $request->yt_title,
                        str_slug($request->yt_title),
                        str_slug($request->yt_title, '_'),
                        $youtube->title,
                        str_slug($youtube->title),
                        str_slug($youtube->title, '_'),
                        'unit_video',
                        str_slug('unit_video'),
                        str_slug('unit_video', '_')
                    );

                    
                    $video = Video::create([
                        'parent_id'         => $record->id,
                        'category'          => 'unit_video',
                        'title'             => $request->yt_title,
                        'keyword'           => $keyword,
                        'yt_id'             => $youtube->youtubeId,
                        'yt_url'            => $request->yt_url,
                        'embed_url'         => $youtube->embedUrl,
                        'yt_title'          => $youtube->title,
                        'yt_thumbnail'      => $youtube->thumbnail,
                        'yt_published_date' => Carbon::parse($youtube->published),
                        'enabled'           => $request->enabled,
                        'featured'          => $request->featured ?? 0,
                        'slug'              => $request->yt_title ? str_slug($request->yt_title) : $this->slugify($youtube->title, 'Video'),
                    ]);
                }

                if ($request->has('thumbnail')) {
                    $this->addImages('unit', $request, $record, 'thumbnail');
                }
            }

            // FLOOR PLAN SECTION
            $floor_plan = PageSection::create([
                'page_id' => $record->id,
                'name' => 'Floor Plan',
                'title' => $request->floor_plan_title ?? '',
                'order' => PageSection::where('page_id', $record->id)->count() + 1,
                'has_button' => 0
            ]);

            // UNIT GALLERY SECTION
            $unit_gallery = PageSection::create([
                'page_id' => $record->id,
                'name' => 'Unit Gallery',
                'title' => $request->unit_gallery_title ?? '',
                'order' => PageSection::where('page_id', $record->id)->count() + 1,
                'has_button' => 0
            ]);

            // SEE MORE UNITS SECTION
            $see_more = PageSection::create([
                'page_id' => $record->id,
                'name' => 'See more Units Banner',
                'title' => $request->see_more_title ?? '',
                'order' => PageSection::where('page_id', $record->id)->count() + 1,
                'has_button' => 0
            ]);

            if ($property->property_type === 'Condominium') {
                $this->updateOrCreateFeature($request, $record);
            }

            $this->addImages('unit', $request, $record, 'main_image');
            $this->addImages('page_section', $request, $floor_plan, 'floor_plan_image');
            if($request->has('see_more_image')){
                $this->addImages('page_section', $request, $see_more, 'see_more_image');
            }
            if ($request->has('unit_gallery')) {
                // foreach ($request->amenity_gallery as $key => $img) {
                    $this->addImages('page_section', $request, $unit_gallery, 'unit_gallery');
                // }
            }

            
            $this->metatags($record, $request);
            
            $this->generateLog($request->user(), "Created", 'Units', $record);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // \Log::info($e);
            return response(['error' => 'Transaction failed: ' . $e->getMessage()], 500);
        }
        
        // $this->generateLog($request->user(), "added this unit ({$record->id}).");

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
    public function show ($unit, $request): Response
    {
        $unit->load(['images', 'metadata', 'unitType', 
            'pageSections' => function ($q) {
                $q->with('images', 'buttons');
            },
            'videos' => function ($q) {
                $q->with('images');
            },
            'features' => function ($q) {
                $q->orderBy('order')->with('images');
            }
        ]);

        // $this->generateLog($request->user(), "viewed this unit ({$unit->id}).");

        return response([
            'record' => $unit
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($unit, $request): Response
    {
        DB::beginTransaction();
        try {
            $property = Property::find($unit->parent_id);
            $unit_name = '';
            if(empty($request->title)){
                $unit_name = Taxonomy::where('type', Taxonomy::TYPE_UNIT)->where('id', $request->unit_type_id)->first()->name;
            }
            $unit->load('images', 'videos', 'pageSections');

            $unit->update([
                'name'          => $request->title ?? $unit_name,
                'slug'          => ($request->title ?? $unit_name) !== $unit->name ? 
                    $this->slugify($request->title ?? $unit_name, 'Unit', $unit->id) : 
                    $unit->slug,
                'order'         => $request->order ?? $unit->order,
                'location'      => $request->address ?? $property->address,
                'starts_at'     => $request->starts_at ?? '',
                'unit_type'     => $request->unit_type_id,
                'floor_area'    => $request->floor_area,
                'lot_area'      => $request->lot_area,
                'bedroom'       => $request->bedrooms,
                't_and_b'       => $request->t_and_b,
                'storeys'       => $request->storeys,
                'powder_room'   => $request->powder_rooms,
                'enabled'       => $request->enabled ?? 1,
                'tracking_code' => $request->tracking_code ?? '',
                'gmap_url'      => $request->gmap_url ?? $property->gmaps_link 
            ]);

            if ($request->filled('yt_url')) {
                $youtube = $this->validateYoutubeLink($request->yt_url);
                if (!$youtube->valid) {
                    return response([
                        'errors' => ['Youtube URL value is invalid.']
                    ], 400);
                }
                else {
                    $keyword = sprintf('%s,%s,%s,%s,%s,%s,%s,%s,%s',
                        $request->yt_title,
                        str_slug($request->yt_title),
                        str_slug($request->yt_title, '_'),
                        $youtube->title,
                        str_slug($youtube->title),
                        str_slug($youtube->title, '_'),
                        'unit_video',
                        str_slug('unit_video'),
                        str_slug('unit_video', '_')
                    );
                    if ($unit->videos) {
                        $video = Video::find($unit->videos->id);
                    
                        $video->update([
                            'category'          => 'unit_video',
                            'title'             => $request->yt_title,
                            'keyword'           => $keyword,
                            'yt_id'             => $youtube->youtubeId,
                            'yt_url'            => $request->yt_url,
                            'embed_url'         => $youtube->embedUrl,
                            'yt_title'          => $youtube->title,
                            'yt_thumbnail'      => $youtube->thumbnail,
                            'yt_published_date' => Carbon::parse($youtube->published),
                            'enabled'           => $request->enabled,
                            'featured'          => 0,
                            'slug'              => $request->yt_title ? str_slug($request->yt_title) : $this->slugify($youtube->title, 'Video'),
                        ]);
                    } else {
                        $video = Video::create([
                            'parent_id'         => $unit->id,
                            'category'          => 'unit_video',
                            'title'             => $request->yt_title,
                            'keyword'           => $keyword,
                            'yt_id'             => $youtube->youtubeId,
                            'yt_url'            => $request->yt_url,
                            'embed_url'         => $youtube->embedUrl,
                            'yt_title'          => $youtube->title,
                            'yt_thumbnail'      => $youtube->thumbnail,
                            'yt_published_date' => Carbon::parse($youtube->published),
                            'enabled'           => $request->enabled,
                            'featured'          => 0,
                            'slug'              => $request->yt_title ? str_slug($request->yt_title) : $this->slugify($youtube->title, 'Video'),
                        ]);
                    }
                    
                    // $video = Video::create([
                    //     'parent_id'         => $unit->id,
                    //     'category'          => 'unit_video',
                    //     'title'             => $request->yt_title,
                    //     'keyword'           => $keyword,
                    //     'yt_id'             => $youtube->youtubeId,
                    //     'yt_url'            => $request->yt_url,
                    //     'embed_url'         => $youtube->embedUrl,
                    //     'yt_title'          => $youtube->title,
                    //     'yt_thumbnail'      => $youtube->thumbnail,
                    //     'yt_published_date' => Carbon::parse($youtube->published),
                    //     'enabled'           => $request->enabled,
                    //     'featured'          => $request->featured ?? 0,
                    //     'slug'              => $request->yt_title ? str_slug($request->yt_title) : $this->slugify($youtube->title, 'Video'),
                    // ]);
                }

                if ($request->has('thumbnail')) {
                    $this->updateImages('unit', $request, $unit, 'thumbnail');
                }
            } else {
                if ($unit->videos) {
                    $video = Video::find($unit->videos->id);
                    
                    $video->forceDelete();
                    $thumbnails = Image::where('parent_id', $property->id)->where('category', 'thumbnail')->get();
                    foreach($thumbnails as $thumbnail) {
                        $disk = 'public';
                        $path = explode('uploads', $thumbnail->path);
                        $path_resized = explode('uploads', $thumbnail->path_resized);
                        Storage::disk($disk)->delete("uploads$path[1]");
                        Storage::disk($disk)->delete("uploads$path_resized[1]");
        
                        $thumbnail->forceDelete();
                    }
                }
            }

            $floor_plan = PageSection::where('name', 'Floor Plan')->where('page_id', $unit->id)->first();
            
            if($floor_plan) {
                $floor_plan->update([
                    'name' => 'Floor Plan',
                    'title' => $request->floor_plan_title ?? '',
                    'order' => $floor_plan->order,
                    'has_button' => 0
                ]);
            } else {
                $floor_plan = PageSection::create([
                    'page_id' => $unit->id,
                    'name' => 'Floor Plan',
                    'title' => $request->floor_plan_title ?? '',
                    'order' => PageSection::where('page_id', $unit->id)->count() + 1,
                    'has_button' => 0
                ]);
            }

            $unit_gallery = PageSection::where('name', 'Unit Gallery')->where('page_id', $unit->id)->first();
            
            if ($unit_gallery) {
                $unit_gallery->update([
                    'name' => 'Unit Gallery',
                    'title' => $request->unit_gallery_title ?? '',
                    'order' => $unit_gallery->order,
                    'has_button' => 0
                ]);
            } else {
                $unit_gallery = PageSection::create([
                    'page_id' => $unit->id,
                    'name' => 'Unit Gallery',
                    'title' => $request->unit_gallery_title ?? '',
                    'order' => PageSection::where('page_id', $unit->id)->count() + 1,
                    'has_button' => 0
                ]);

            }

            
            $see_more = PageSection::where('name', 'See more Units Banner')->where('page_id', $unit->id)->first();

            if ($see_more) {
                $see_more->update([
                    'name' => 'See more Units Banner',
                    'title' => $request->see_more_title ?? '',
                    'order' => $see_more->order,
                    'has_button' => 0
                ]);
            } else {
                
                // SEE MORE UNITS SECTION
                $see_more = PageSection::create([
                    'page_id' => $unit->id,
                    'name' => 'See more Units Banner',
                    'title' => $request->see_more_title ?? '',
                    'order' => PageSection::where('page_id', $unit->id)->count() + 1,
                    'has_button' => 0
                ]);
            }

            
            if ($request->feature_title) {
                $this->updateOrCreateFeature($request, $unit);
            }

            $this->updateImages('unit', $request, $unit, 'main_image');
            $this->updateImages('page_section', $request, $floor_plan, 'floor_plan_image');
            if($request->has('see_more_image')){
                
                $this->updateImages('page_section', $request, $see_more, 'see_more_image');
            }
            if ($request->has('unit_gallery_id')) {
                // foreach ($request->amenity_gallery as $key => $img) {
                    $this->updateImages('page_section', $request, $unit_gallery, 'unit_gallery');
                // }
            }
            

            $this->metatags($unit, $request);

            $this->generateLog($request->user(), "Changed", 'Units', $unit);

            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            // \Log::info($e);
            return response(['error' => 'Transaction failed: ' . $e->getMessage()], 500);
        }

        $unit->load(['images', 'metadata', 'unitType', 
            'pageSections' => function ($q) {
                $q->with('images', 'buttons');
            },
            'videos' => function ($q) {
                $q->with('images');
            },
            'features' => function ($q) {
                $q->orderBy('order')->with('images');
            }
        ]);
        return response([
            'record' => $unit
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($unit, $request): Response
    {
        DB::beginTransaction();
        try {
            if ($unit->order !== Unit::max('order')) {
                Unit::where('order', '>', $unit->order)->decrement('order'); 
            }
            $this->generateLog($request->user(), "Deleted", "Units", $unit);
            $unit->delete();
            $this->reassignOrderValues('Unit', $unit->parent_id);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // \Log::info($e);
            return response(['error' => 'Transaction failed: ' . $e->getMessage()], 500);
        }
 
        return response([
            'record' => 'unit deleted'
        ]);
    }

    protected function updateOrCreateFeature($request, $record)
    {
        foreach ($request->feature_title as $key => $feat) {
            if ($request->feature_id[$key]) {
                $feature = Feature::find($request->feature_id[$key]);
            } else {
                $feature = new Feature(['parent_id' => $record->id]);
            }

            $feature->fill([
                'content' => $request->feature_title[$key],
                'description' => $request->feature_description[$key],
                'type' => 'unit Feature',
                'order' => $request->feature_order[$key] ?? Feature::where('parent_id', $record->id)->count() + 1
            ])->save();

            if ($request->has('feature_icon'.$key.'_id')) {
                $this->handleFileUpdate('feature', $request, $feature, 'feature_icon'.$key, 0);
            }
        }
    }

    protected function handleFileUpdate($type, $request, $model, $fileType, $index)
    {
        $temp_request = (object) [
            "{$fileType}" => [
                $request->{"{$fileType}_id"}[$index] === null ? 
                $request->file($fileType)[($index - (count($request->{"{$fileType}_id"}) - count($request->{"{$fileType}"}))) > 0 ?? 0] :
                null
            ],
            "{$fileType}_id" => [$request->{"{$fileType}_id"}[$index] ?? null],
            "{$fileType}_alt" => [$request->{"{$fileType}_alt"}[$index] ?? null],
            "{$fileType}_category" => [$request->{"{$fileType}_category"}[$index] ?? null],
        ];


        $this->{$model->exists ? 'updateImages' : 'addImages'}($type, $temp_request, $model, $fileType);
    }

    public function getUnit($request) : Response
    {
        $data = Unit::where('slug', $request->slug)
            ->with([
                'images', 
                'unitType', 
                'features' => function ($query) {
                    $query->orderBy('order')->with('images');
                },
                'videos',
                'pageSections' => function ($query) {
                    $query->orderBy('order')->with(['images', 'buttons']);
                },
            ])->first();

        $property = Property::find($data->parent_id);
        $page = Page::where('name', 'LIKE', '%' . $property->propert_type . '%')->first();

        $data['get-quote'] = PageSection::where('page_id', $page->id)->where('name', 'Get a Quote Banner')->first();

        $data['units'] = Unit::select('id', 'name', 'slug', 'starts_at', 'unit_type', 'location', 'floor_area', 'parent_id', 'enabled')
        ->orderBy('name')
        ->where('id', '<>', $data->id)
        ->where('enabled', 1)
        ->where('parent_id', $data->parent_id)
        ->with('images')
        ->get();

        return response([
            'record' => $data
        ]);
    }
}
