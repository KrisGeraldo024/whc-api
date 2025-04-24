<?php

namespace App\Services\Property;

use App\Models\Article;
use App\Models\Button;
use App\Models\Feature;
use App\Models\Image;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Unit;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    DB,
    Storage,
    Validator
};
use App\Models\Property;
use App\Models\Taxonomy;
use App\Models\Video;
use App\Traits\GlobalTrait;
use Carbon\Carbon;
use Exception;

class PropertyService
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
        $query = Property::select('id', 'slug', 'name', 'location_id', 'updated_at', 'featured', 'property_type')->orderBy(isset($request->sortBy) ? $request->sortBy : 'created_at', isset($request->sortDirection) ? $request->sortDirection : 'desc')
        ->orderBy('order')
        ->where('property_type', ($request->propertyType  === 'house-and-lots' ? 'House & Lot' : 'Condominium'))
        ->when(isset($request->keyword), function ($query) use ($request) {
            $query->where('name', 'LIKE', '%' . strtolower($request->keyword).'%');	
        })
        ->when(isset($request->location), function ($query) use ($request) {
            $query->whereHas('locations', function ($q) use ($request){
                $q->where('name', $request->location);
            });
        })
        ->with('locations');
        $records = $request->filled('all') ? $query->get() : $query->paginate(10);

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
            $record = Property::create([
                'name'        => $request->title,
                'slug'          => $this->slugify($request->title, 'Property'),
                'order'        => $request->order ?? Property::where('property_type', ($request->propertyType  === 'house-and-lots' ? 'House & Lot' : 'Condominium'))->count() + 1,
                'property_size' => $request->property_size,
                'towers'      => $request->towers,
                'starts_at'     => $request->starts_at,
                'address'     => $request->address,
                'gmaps_link' => $request->gmap_url,
                'description' => $request->description,
                'location_id' => $request->location_id,
                'status_id' => $request->status_id ?? 'fe838bf4-e3b4-42fb-9a1b-53e4b5d8e041',
                'featured' => $request->featured ?? 0,
                'enabled' => $request->enabled ?? 1,
                'property_type' => $request->propertyType  === 'house-and-lots' ? 'House & Lot' : 'Condominium',
                'tracking_code' => $request->tracking_code ?? ''
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
                        'property_video',
                        str_slug('property_video'),
                        str_slug('property_video', '_')
                    );

                    
                    $video = Video::create([
                        'parent_id'         => $record->id,
                        'category'          => 'property_video',
                        'title'             => $request->yt_title,
                        'keyword'           => $keyword,
                        'yt_id'             => $youtube->youtubeId,
                        'yt_url'            => $request->yt_url,
                        'embed_url'         => $youtube->embedUrl,
                        'yt_title'          => $youtube->title,
                        'yt_thumbnail'      => $youtube->thumbnail,
                        'yt_published_date' => Carbon::parse($youtube->published),
                        'enabled'           => $request->enabled,
                        'featured'          => $request->featured,
                        'slug'              => $request->yt_title ? str_slug($request->yt_title) : $this->slugify($youtube->title, 'Video'),
                    ]);
                }

                if ($request->has('thumbnail')) {
                    $this->addImages('property', $request, $record, 'thumbnail');
                }
            }

            // VICINITY SECTION
            $vicinity = PageSection::create([
                'page_id' => $record->id,
                'name' => 'Vicinity Maps',
                'title' => $request->vicinity_title ?? '',
                'order' => PageSection::where('page_id', $record->id)->count() + 1,
                'has_button' => 0
            ]);

            // UNIT LIST SECTION
            $unitsList = PageSection::create([
                'page_id' => $record->id,
                'name' => 'Units List',
                'title' => $request->unit_list_title ?? '',
                'order' => PageSection::where('page_id', $record->id)->count() + 1,
                'has_button' => 0
            ]);

            // AMENITIES SECTION
            $amenities = PageSection::create([
                'page_id' => $record->id,
                'name' => 'Amenities Gallery',
                'title' => $request->amenities_title ?? '',
                'order' => PageSection::where('page_id', $record->id)->count() + 1,
                'has_button' => 0
            ]);

            // DIGITAL BROCHURE SECTION
            if ($request->digital_brochure['title']) {

                $digitalBrochure = PageSection::create([
                    'page_id' => $record->id,
                    'name' => 'Digital Brochure',
                    'title' => $request->digital_brochure['title'],
                    'order' => PageSection::where('page_id', $record->id)->count() + 1,
                    'has_button' => 1
                ]);

                $btn = Button::create([
                    'parent' => $digitalBrochure->id,
                    'button_name' => $request->digital_brochure_name[0],
                    'is_link_out' => $request->digital_brochure_link_out[0] ? 1 : 0,
                    'link' => $request->digital_brochure_link[0],
                ]);
                $this->addImages('page_section', $request, $digitalBrochure, 'digital_brochure_image');
            }
            $this->updateOrCreateFeature($request, $record);

            $this->addImages('property', $request, $record, 'main_image');
            $this->addImages('page_section', $request, $vicinity, 'vicinity_image');
            if ($request->has('amenity_gallery')) {
                // foreach ($request->amenity_gallery as $key => $img) {
                    $this->addImages('page_section', $request, $amenities, 'amenity_gallery');
                // }
            }

            
            $this->metatags($record, $request);
            
            $this->generateLog($request->user(), "Created", 'Properties', $record);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::info($e);
            return response(['error' => 'Transaction failed: ' . $e->getMessage()], 500);
        }
        
        // $this->generateLog($request->user(), "added this property ({$record->id}).");

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
    public function show ($property, $request): Response
    {
        $property->load(['images', 'locations', 'metadata', 'propertyType', 
            'pageSections' => function ($q) {
                $q->with('images', 'buttons');
            },
            'features' => function ($q) {
                $q->orderBy('order')->with('images');
            },
            'videos' => function ($q) {
                $q->with('images');
            }
        ]);

        // $this->generateLog($request->user(), "viewed this property ({$property->id}).");

        return response([
            'record' => $property
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($property, $request): Response
    {
        DB::beginTransaction();
        try {
            $property->load('images', 'videos', 'pageSections', 'features');

            $property->update([
                'name'        => $request->title,
                'slug'          => $request->title !== $property->name ? $this->slugify($request->title, 'Property', $property->id) : $property->slug,
                'order'        => $request->order ?? $property->order,
                'property_size' => $request->property_size,
                'towers'      => $request->towers,
                'starts_at'     => $request->starts_at,
                'address'     => $request->address,
                'gmaps_link' => $request->gmap_url,
                'description' => $request->description,
                'location_id' => $request->location_id,
                'status_id' => $request->status_id ?? 'fe838bf4-e3b4-42fb-9a1b-53e4b5d8e041',
                'featured' => $request->featured ?? 0,
                'enabled' => $request->enabled ?? 1,
                'property_type' =>$request->propertyType  === 'house-and-lots' ? 'House & Lot' : 'Condominium',
                'tracking_code' => $request->tracking_code ?? ''
            ]);

            if ($request->filled('yt_url')) {
                $youtube = $this->validateYoutubeLink($request->yt_url);
                if (!$youtube->valid) {
                    return response([
                        'errors' => 'Youtube URL value is invalid.'
                    ], 400);
                }
                else {
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
                        'property_video',
                        str_slug('property_video'),
                        str_slug('property_video', '_')
                    );

                    if ($property->videos) {
                        $video = Video::find($property->videos->id);
                    
                        $video->update([
                            'category'          => 'property_video',
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
                            'parent_id'         => $property->id,
                            'category'          => 'property_video',
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
                    
                }

                if ($request->has('thumbnail')) {
                    $this->updateImages('property', $request, $property, 'thumbnail');
                }
            } else {
                if ($property->videos) {
                    $video = Video::find($property->videos->id);
                    
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

            $vicinity = PageSection::where('name', 'Vicinity Maps')->where('page_id', $property->id)->first();
            
            if($vicinity) {
                $vicinity->update([
                    'name' => 'Vicinity Maps',
                    'title' => $request->vicinity_title ?? '',
                    'order' => $vicinity->order,
                    'has_button' => 0
                ]);
            } else {
                // VICINITY SECTION
                $vicinity = PageSection::create([
                    'page_id' => $property->id,
                    'name' => 'Vicinity Maps',
                    'title' => $request->vicinity_title ?? '',
                    'order' => PageSection::where('page_id', $property->id)->count() + 1,
                    'has_button' => 0
                ]);
            }

            $unitsList = PageSection::where('name', 'Units List')->where('page_id', $property->id)->first();
            
            if ($unitsList) {
                $unitsList->update([
                    'name' => 'Units List',
                    'title' => $request->unit_list_title ?? '',
                    'order' => $unitsList->order,
                    'has_button' => 0
                ]);
            } else {
                // UNIT LIST SECTION
                $unitsList = PageSection::create([
                    'page_id' => $property->id,
                    'name' => 'Units List',
                    'title' => $request->unit_list_title ?? '',
                    'order' => PageSection::where('page_id', $property->id)->count() + 1,
                    'has_button' => 0
                ]);
            }

            $amenities = PageSection::where('name', 'Amenities Gallery')->where('page_id', $property->id)->first();
            
            
            if ($amenities) {
                $amenities->update([
                    'name' => 'Amenities Gallery',
                    'title' => $request->amenities_title ?? '',
                    'order' => $amenities->order,
                    'has_button' => 0
                ]);
            } else {
                // AMENITIES SECTION
                $amenities = PageSection::create([
                    'page_id' => $property->id,
                    'name' => 'Amenities Gallery',
                    'title' => $request->amenities_title ?? '',
                    'order' => PageSection::where('page_id', $property->id)->count() + 1,
                    'has_button' => 0
                ]);
            }

            if ($request->has('digital_brochure') ) {

                $digitalBrochure = PageSection::where('name', 'Digital Brochure')->where('page_id', $property->id)->first();

                if ($digitalBrochure) {
                    $digitalBrochure->update([
                        'name' => 'Digital Brochure',
                        'title' => $request->digital_brochure['title'] ?? '',
                        'order' => $digitalBrochure->order,
                        'has_button' => $request->digital_brochure['has_button']
                    ]);

                    if($request->digital_brochure['has_button']) {
                        $btn = Button::where('parent', $digitalBrochure->id)->first();
                        $btn->update(attributes: [
                            'button_name' => $request->digital_brochure_name[0],
                            'is_link_out' => $request->digital_brochure_link_out[0] ? 1 : 0,
                            'link' => $request->digital_brochure_link[0],
                        ]);
                    }
                } else if ($request->digital_brochure['title']) {

                    $digitalBrochure = PageSection::create([
                        'page_id' => $property->id,
                        'name' => 'Digital Brochure',
                        'title' => $request->digital_brochure['title'],
                        'order' => PageSection::where('page_id', $property->id)->count() + 1,
                        'has_button' => 1
                    ]);

                    $btn = Button::create([
                        'parent' => $digitalBrochure->id,
                        'button_name' => $request->digital_brochure_name[0],
                        'is_link_out' => $request->digital_brochure_link_out[0] ? 1 : 0,
                        'link' => $request->digital_brochure_link[0],
                    ]);
                }

                if($request->has('digital_brochure_image_id')) {
                    $this->updateImages('page_section', $request, $digitalBrochure, 'digital_brochure_image');
                }
            }

            if ($request->has('feature_title')) {
                $this->updateOrCreateFeature($request, $property);
            }

            $this->updateImages('property', $request, $property, 'main_image');
            if ($request->has('vicinity_image')) {
                $this->updateImages('page_section', $request, $vicinity, 'vicinity_image');
            }
            if ($request->has('amenity_gallery_id')) {
                // foreach ($request->amenity_gallery as $key => $img) {
                    $this->updateImages('page_section', $request, $amenities, 'amenity_gallery');
                // }
            }
            

            $this->metatags($property, $request);

            $this->generateLog($request->user(), "Changed", 'Properties', $property);

            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            // \Log::info($e);
            return response(['error' => 'Transaction failed: ' . $e->getMessage()], 500);
        }

        $property->load(['images', 'locations', 'metadata', 'propertyType', 
            'pageSections' => function ($q) {
                $q->with('images', 'buttons');
            },
            'features' => function ($q) {
                $q->orderBy('order')->with('images');
            },
            'videos' => function ($q) {
                $q->with('images');
            }
        ]);
        return response([
            'record' => $property
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($property, $request): Response
    {
        DB::beginTransaction();
        try {
            if ($property->order !== Property::max('order')) {
                Property::where('order', '>', $property->order)->decrement('order'); 
            }
    
            $this->generateLog($request->user(), "Deleted", "Properties", $property);
            $property->delete();
            
            $this->reassignOrderValues('Property');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // \Log::info($e);
            return response(['error' => 'Transaction failed: ' . $e->getMessage()], 500);
        }
 
        return response([
            'record' => 'Property deleted'
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
                'type' => 'Property Feature',
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

        // \Log::info($temp_request->feature_icon_id[0] . ' + ' . $temp_request->feature_icon[0]);

        $this->{$model->exists ? 'updateImages' : 'addImages'}($type, $temp_request, $model, $fileType);
    }

    public function getProperty($request) : Response
    {

        $data = Property::where('slug', $request->slug)
            ->with(['images', 'locations', 'videos', 'propertyStatus', 'pageSections' => function ($query) {
                            $query->orderBy('order')->with(['images', 'buttons']);
                        },])
            ->first();

        $page = Page::where('name', 'LIKE', '%' . $data->property_type . '%')->first();

        $data['get-quote'] = PageSection::where('page_id', $page->id)->where('name', 'Get a Quote Banner')->first();
        // Lazy load nested relationships
        // if ($data) {
        //     $data->load([
        //         'features' => function ($query) {
        //             $query->orderBy('order')->with('images');
        //         },
        //         'pageSections'  => function ($query) {
        //             $query->orderBy('order')->with('images');
        //         },
        //         'landmarks' => function ($query) {
        //             $query->with(['images', 'vicinities' => function ($q) { $q->orderBy('order') ; }]);
        //         },
        //         'units'  => function ($query) {
        //             $query->orderBy('order')->with('images');
        //         },
        //         'amenities'  => function ($query) {
        //             $query->orderBy('order')->with('images');
        //         },
        //     ]);
        // }
        // $data = Property::where('slug', $request->slug)
        //     ->with([
        //         'images', 
        //         'locations', 
        //         'features' => function ($query) {
        //             $query->orderBy('order')->with('images');
        //         },
        //         'videos',
        //         'pageSections' => function ($query) {
        //             $query->orderBy('order')->with('images');
        //         },
        //         'landmarks' => function ($query) {
        //             $query->with(['images', 'vicinities' => function ($q) { $q->orderBy('order') ; }]);
        //         },
        //         'units' => function ($query) {
        //             $query->orderBy('order')->with('images');
        //         },
        //         'amenities' => function ($query) {
        //             $query->orderBy('order')->with('images');
        //         },  
        //     ])->first();

        return response([
            'record' => $data
        ]);
    }

    public function getRelateds($request, $property, $related): Response
    {
        $relationships = [];
    
        if ($related === 'landmarks') {
            $relationships[$related] = function ($query) {
                $query->with([
                    'images',
                    'vicinities' => function ($q) {
                        $q->orderBy('order');
                    }
                ]);
            };
        } 
        
        else if ($related === 'units') {
            $relationships[$related] = function ($query) {
                $query->select('id', 'name', 'slug', 'starts_at', 'unit_type', 'location', 'floor_area', 'parent_id', 'enabled')
                ->where('enabled', 1)
                ->orderBy('name')
                ->with(['images','unitType']);
            };
        }

        else if ($related === 'amenities') {
            $relationships[$related] = function ($query) {
                $query->where('enabled', 1)->orderBy('order')->with('images');
            };
        }

        else {
            $relationships[$related] = function ($query) {
                $query->orderBy('order')->with('images');
            };
        }
    
        $dataProp = Property::find($property);
        $data = $dataProp->load($relationships);
    
        return response([
            'record' => $data
        ]);
    }

    public function getAll ($request): Response
    {
        $query = Property::select('id', 'name', 'slug', 'location_id', 'updated_at', 'featured', 'property_type', 'status_id', 'enabled')
        ->where('enabled', 1)
        ->where('property_type', ($request->propertyType  === 'house-and-lots' ? 'House & Lot' : 'Condominium'))
        ->when(isset($request->keyword), function ($query) use ($request) {
            $query->where('name', 'LIKE', '%' . strtolower($request->keyword).'%');	
        })
        ->when(isset($request->location), function ($query) use ($request) {
            $query->whereHas('locations', function ($q) use ($request){
                $q->where('name', $request->location);
            });
        })
        ->with('locations', 'images', 'propertyStatus')
        ->orderByRaw("
            CASE 
                WHEN (SELECT taxonomy.name 
                      FROM taxonomies AS taxonomy 
                      WHERE taxonomy.id = properties.status_id 
                        AND taxonomy.type = '" . Taxonomy::TYPE_PROPERTY_STATUS . "') = 'Sold Out' 
                THEN 1
                ELSE 0
            END
        ") // Ensure "Sold Out" is last
        ->orderByRaw("
            CASE 
                WHEN featured = 1 AND (
                    SELECT taxonomy.name 
                    FROM taxonomies AS taxonomy 
                    WHERE taxonomy.id = properties.status_id 
                      AND taxonomy.type = '" . Taxonomy::TYPE_PROPERTY_STATUS . "') != 'Sold Out' 
                THEN 0
                ELSE 1
            END
        ") // Ensure featured comes first, unless "Sold Out"
        ->orderBy(
            Taxonomy::select('name')
                ->whereColumn('id', 'properties.status_id')
                ->where('type', Taxonomy::TYPE_PROPERTY_STATUS)
        ) 
        // ->orderBy(isset($request->sortBy) ? $request->sortBy : 'created_at', isset($request->sortDirection) ? $request->sortDirection : 'desc')
        ->orderBy('name'); 
        $records = $request->filled('all') ? $query->get() : $query->paginate(6);

        return response([
            'records' => $records
        ]);
    }

    public function getLocationsByType($request) { 
        // Normalize property type to lowercase 
        $propertyType = ucfirst(strtolower($request->type)); // Ensures proper case (e.g., 'Condominium')         
    
        $locations = DB::table('properties')
            ->select('property_type', 'location_id', DB::raw('COUNT(*) as property_count'))
            ->groupBy('property_type', 'location_id')
            ->whereNull('deleted_at')
            ->get()
            ->groupBy('property_type')
            ->map(function ($group) {
                return $group->map(function ($item) {
                    $location = Taxonomy::where('id', $item->location_id)
                        ->whereNull('deleted_at')
                        ->first();
    
                    // Only return the location name
                    $item->location_name = $location ? $location->name : null;
    
                    // Remove the full location object
                    unset($item->location);
    
                    return $item;
                })
                // Sort locations by their name alphabetically
                ->sortBy(function ($item) {
                    return $item->location_name;
                })
                // Reset keys for a cleaner result (this removes the 0, 1, etc.)
                ->values();
            });
    
        // Return the structured response
        return response([
            'locations' => $locations
        ]);
    }

    public function getPropertyList () : Response
    {
        $records = Property::select('name', 'property_type', 'id','slug')
        ->with(['units' => function ($q) {
            $q->select('name', 'parent_id','slug');
        }])
        ->get()
        ->groupBy('property_type');

        return response([
            'records' => $records
        ]);
    }


    // public function search ($request) : Response
    // {
    //     switch ($request->type) {
    //         case 'communities' :
    //             $records = Property::select('id', 'name', 'slug', 'location_id', 'property_type')
    //             ->where('name', 'LIKE', '%' . $request->keyword . '%')
    //             ->with(['locations', 'images'])
    //             ->paginate(6);

    //             break;
    //         case 'units' :
    //             $records = Unit::select('id', 'name', 'slug', 'location', 'unit_type', 'starts_at')
    //             ->where('name', 'LIKE', '%' . $request->keyword . '%')
    //             ->with(['images', 'unitType'])
    //             ->paginate(6);

    //             break;
    //         case 'articles' :
    //             $records = Article::select('id', 'title', 'slug', 'category_id', 'date')
    //             ->where('keyword', 'LIKE', '%' . $request->keyword . '%')
    //             ->with(['images', 'articleCategory'])
    //             ->paginate(6);

    //             break;
    //         default: 
    //             $records = [];
    //     }

    //     $records['communities_count'] = Property::where('name', 'LIKE', '%' . $request->keyword . '%')->whereNull('deleted_at')->count();
    //     $records['units_count'] = Unit::where('name', 'LIKE', '%' . $request->keyword . '%')->whereNull('deleted_at')->count();
    //     $records['articles_count'] = Article::where('keyword', 'LIKE', '%' . $request->keyword . '%')->whereNull('deleted_at')->count();

       
    //     return response([
    //         'records' => $records
    //     ]);
    // }

    public function search($request) : Response
    {
        $communitiesCount = Property::where('name', 'LIKE', '%' . $request->keyword . '%')
        ->orWhere('address', 'LIKE',  '%' . $request->keyword . '%')
        ->whereNull('deleted_at')->count();
        $unitsCount = Unit::where('name', 'LIKE', '%' . $request->keyword . '%')
        ->orWhere('location', 'LIKE',  '%' . $request->keyword . '%')
        ->whereHas('property')->whereNull('deleted_at')->count();
        $articlesCount = Article::where('keyword', 'LIKE', '%' . $request->keyword . '%')->whereNull('deleted_at')->count();
    
        switch ($request->type) {
            case 'communities':
                $records = Property::select('id', 'name', 'slug', 'location_id', 'property_type')
                    ->where('name', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('address', 'LIKE',  '%' . $request->keyword . '%')
                    ->with(['locations', 'images'])
                    ->paginate(6);
                break;
    
            case 'units':
                $records = Unit::select('id', 'name', 'slug', 'location', 'unit_type', 'starts_at', 'parent_id')
                    ->where('name', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('location', 'LIKE',  '%' . $request->keyword . '%')
                    ->whereHas('property')
                    ->with(['property' => function ($q) {
                        $q->select('id', 'name', 'slug', 'property_type');
                    }])
                    ->with(['images', 'unitType'])
                    ->paginate(6);
                break;
    
            case 'articles':
                $records = Article::select('id', 'title', 'slug', 'category_id', 'date')
                    ->where('keyword', 'LIKE', '%' . $request->keyword . '%')
                    ->with(['images', 'articleCategory'])
                    ->paginate(6);
                break;
    
            default: 
                $records = collect([])->paginate(6); 
        }
    
        return response([
            'records' => [
                'data' => $records->items(),
                'current_page' => $records->currentPage(),
                'first_page_url' => $records->url(1),
                'last_page' => $records->lastPage(),
                'last_page_url' => $records->url($records->lastPage()),
                'links' => $records->linkCollection(),
                'next_page_url' => $records->nextPageUrl(),
                'path' => $records->path(),
                'per_page' => $records->perPage(),
                'prev_page_url' => $records->previousPageUrl(),
                'to' => $records->lastItem(),
                'total' => $records->total(),
                // Add counts here
                'communities_count' => $communitiesCount,
                'units_count' => $unitsCount,
                'articles_count' => $articlesCount,
            ]
        ]);
    }

    public function getSuggesteds($request) : Response
    {
        $records['articles'] = Article::select('id', 'title', 'slug', 'category_id', 'keyword')
            ->where('keyword', 'LIKE', '%' . $request->keyword . '%')
            ->with(['articleCategory'])
            ->take(10) // Limit to 5 articles
            ->get();
    
        $records['units'] = Unit::select('id', 'name', 'slug', 'parent_id')
            ->whereHas('property')
            ->with(['property' => function ($q) {
                $q->select('id', 'name', 'slug', 'property_type');
            }])
            ->where('name', 'LIKE', '%' . $request->keyword . '%')
            ->orWhere('location', 'LIKE', '%' . $request->keyword . '%')
            ->take(10) // Limit to 5 units
            ->get();
    
        $records['communities'] = Property::select('id', 'name', 'slug', 'property_type')
            ->where('name', 'LIKE', '%' . $request->keyword . '%')
            ->orWhere('address', 'LIKE', '%' . $request->keyword . '%')
            ->take(10) // Limit to 5 communities
            ->get();
    
        return response([
            'records' => $records
        ]);
    }
    

}
