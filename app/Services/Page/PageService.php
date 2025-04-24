<?php

namespace App\Services\Page;

use Illuminate\Http\Response;

use Illuminate\Support\Facades\{
    DB as FacadesDB,
    Validator
};
use App\Models\{
    Page,
    ServiceCategory,
    Accessories,
    Service,
    Video,
    Promo,
    HearingAid,
    HearingAidCategory,
    Province,
    Municipality,
    Barangay,
    Branch,
    BranchRegion,
    BranchVicinity,
    Faq,
    History,
    Article,
    ArticleCategory,
    VideoCategory,


    Email,
    Office,
    Stat,
    Award,
    Testimonial,
    Property,
    Story,
    Board,
    Executive,
    Advantage,
    Philosophy,
    Project,
    Vicinity,
    Architect,
    Amenity,
    Floorplan,
    MissionVision,
    ConstructionUpdate,
    Location,
    ProjectStatus,
    PropertySubcategory,
    ProjectAward,
    WebsiteSetting,
    Career,
    File,
    PageFile,
    Biller,
    BusinessUnit,
    PaymentType,
    PaymentMethod,
    PaymentOption,
    PaymentChannel,
    PaymentPlatform,
    Taxonomy
};
use App\Traits\GlobalTrait;
use DB;

class PageService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * PageService index
     * @param  Request $request
     * @return Response
     */
    public function index($request): Response
    {
        $records = Page::orderBy('order')
            ->where('order','<>', '')
            ->with('page_sections', function ($q) {
                $q->orderBy('order');
            })
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . strtolower($request->keyword) . '%');
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('category', 'LIKE', '%' . strtolower($request->category) . '%');
            })
            ->when($request->filled('all'), function ($query) {
                return $query->get();
            }, function ($query) {
                return $query->paginate(20);
            });
            // foreach ($records as $key => $value) {
            //     $value->modules = json_decode($value->modules);
            // }
    

        return response([
            'records' => $records
        ]);
    }

    /**
     * PageService store
     * @param  Request $request
     * @return Response
     */
    public function store($request): Response
    {
       

        $record = Page::create([
            'name' => $request->name,
            'identifier' => str_slug($request->name),
            'slug' => $this->slugify($request->name, 'Page'),
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'order' => $request->order,
            'date_published' => $request->date_published,
        ]);

        // metadata
        $this->metatags($record, $request);

        $this->generateLog($request->user(), "created this page ({$record->id})");


        return response([
            'record' => $record
        ]);
    }

    /**
     * PageService show
     * @param  Page $page
     * @param  Request $request
     * @return Response
     */
    public function show($request, Page $page): Response
    {
        // $this->generateLog($request->user(), "viewed this page ({$page->id})");
       

        $page->modules = json_decode($page->modules);
        $page->load('metadata');
        return response([
            'record' => $page
        ]);
    }

    /**
     * PageService update
     * @param  Page $page
     * @param  Request $request
     * @return Response
     */
    public function update($page, $request): Response
    {
        // $page->update([
        //     'name' => $request->name ?? $page->name,
        //     'slug' => $this->slugify($request->name ?? $page->name, 'Page'),
        //     'subtitle' => $request->subtitle ?? $page->subtitle,
        //     'description' => $request->description ?? $page->description,
        //     'order' => $request->sequence ?? $page->order,
        //     'date_published' => $request->date_published ?? $page->date_published,
        // ]);

        $this->metatags($page, $request);

        $page->load('metadata');

        $this->generateLog($request->user(), "Changed", $page->name, $page->metadata);

        return response([
            'record' => $page
        ]);
    }

    /**
     * PageService destroy
     * @param  Page $page
     * @param  Request $request
     * @return Response
     */
    public function destroy($page, $request): Response
    {
        $page->delete();
        $this->generateLog($request->user(), "deleted this page ({$page->id}).");

        return response([
            'record' => 'Page deleted'
        ]);
    }

    /**
     * PageService getPageCategoriesData
     * @param  Request $request
     * @return Response
     */
    public function getCategories () : Response
    {
        $categories = Page::select('category')
            ->groupBy('category') // Group by category to ensure uniqueness
            ->orderByRaw('MIN(`order`) ASC') // Order by the lowest order value for each category
            ->get();

        return response( [
            'record' => $categories
        ]);
    }


    /**
     * PageService pageData
     * @param  string $identifier
     * @param  Request $request
     * @return Response
     */
    public function pageData(string $identifier, $request): Response
    {
        $data = [];

        $data = Page::whereIdentifier($identifier)
        ->with(['metadata', 'page_sections' => function ($q) {
            $q->orderBy('order')
            ->with([
                'images', 
                'buttons' => function ($q) {
                    $q->orderBy('order')->with('images');
                },
                'accordions' => function ($q) {
                    $q->orderBy('order')->with('images');
                }
            ]);
        }])
        ->first();

        if ($identifier === 'homepage') {
            $data['featured_house'] = Property::select('id', 'name', 'property_type', 'location_id', 'enabled', 'featured', 'slug')
                ->where('property_type', 'House & Lot')
                ->where('enabled', 1)
                ->where('featured', 1)
                ->with('images', 'locations')
                ->get();

            $data['featured_condo'] = Property::select('id', 'name', 'property_type', 'location_id', 'enabled', 'featured', 'slug')
                ->where('property_type', 'Condominium')
                ->where('enabled', 1)
                ->where('featured', 1)
                ->with('images', 'locations')
                ->get();

            $data['locations'] = FacadesDB::table('properties')
                ->select('property_type', 'location_id', FacadesDB::raw('COUNT(*) as property_count'))
                ->groupBy('property_type', 'location_id')
                ->whereNull('deleted_at' )
                ->get()
                ->groupBy('property_type')
                ->map(function ($group) {
                    return $group->map(function ($item) {
                        $item->location = Taxonomy::where('id', $item->location_id)
                            ->whereNull('deleted_at' )
                            ->with('images') // Assuming 'images' is a relationship on the Taxonomy model
                            ->first();
                        return $item;
                    });
                });
        }

        if ($identifier === 'about-us') {
            $data['boards'] = Board::orderBy('order')->with('images')->get();
            $data['awards'] = Award::orderBy('date')->where('enabled', 1)->with('images')->get();
        }

        if ($identifier === 'news-and-articles') {
            $data['latest_article'] = Article::select('id', 'title', 'category_id', 'date', 'enabled', 'featured', 'order', 'slug')
            ->where('featured', 1)->with('images')->first() ?? 
            Article::select('id', 'title', 'category_id', 'date', 'enabled', 'featured', 'order', 'slug')
            ->orderBy('date', 'desc')->with('images')->first();
        }
        if (!$data) {
            return response([
                'errors' => ['Page not found!']
            ], 404);
        }

        
        // switch ($request->page_type) {
        //     case 'landing':
        //         switch ($identifier) {
        //             case 'about-us':
        //                 $data = $this->getAboutUsData($request);
        //                 break;
        //             case 'search':
        //                 $data = $this->getSearchData($request);
        //                 break;
        //             case 'homepage':
        //                 $data = $this->getHomepageData($request, $identifier);
        //                 break;
        //             case 'privacy-policy':
        //                 $data = $this->getPrivacyPolicy($request);
        //                 break;
        //             case 'terms-and-conditions':
        //                 $data = $this->getTermsAndCondition($request);
        //                 break;
        //             case 'contact-us':
        //                 $data = $this->getContactUsData($request);
        //                 break;
        //             case 'our-leaders':
        //                 $data = $this->getLeadersData($request);
        //                 break;
        //             case 'house-and-lots':
        //             case 'condominiums':
        //                 $data = $this->getPropertiesData($request, $identifier);
        //                 break;
        //             case 'construction-updates':
        //                 $data = $this->getConstructionUpdateData($request);
        //                 break;
        //             case 'residences':
        //                 $data = $this->getResidencesData($request);
        //                 break;
        //             case 'sustainability':
        //                 $data = $this->getSustainabilityData($request);
        //                 break;
        //             case 'careers':
        //                 $data = $this->getCareersData($request);
        //                 break;
        //             case 'buyers-guide':
        //                 $data = $this->getBuyersGuideData($request);
        //                 break;
        //             case 'media-room':
        //                 $data = $this->getMediaRoomData($request,$identifier);
        //                 break;
        //             case 'virtual-tours':
        //                 $data = $this->getVirtualTourData($request,$identifier);
        //                 break;

        //             //IR
        //             case 'investor-relations':
        //                 $data = $this->getInvestorRelationsData($request);
        //                 break;
        //             case 'corporate-governance':
        //                 $data = $this->getCorporateGovernanceData($request,$identifier);
        //                 break;



        //             case 'investor-presentations':
        //             case 'stockholders-meeting':
        //             case 'financial-information':
        //             case 'disclosures':
        //             case 'maestro-e-newsletter':
        //                 $data = $this->getStockholdersMeetingData($request,$identifier);
        //                 break;
        //             case 'disclosures':
        //                 $data = $this->getDisclosuresData($request,$identifier);
        //                 break;

        //             //static contents (from euroland)
        //             case 'company-announcements':
        //             case 'fact-sheet':
        //             case 'dividend-information':
        //             case 'financial-calendar':
        //             case 'share-information':    
        //                 $data = $this->getStaticIRData($identifier);
        //                 break;

        //             case 'faqs':    
        //                 $data = $this->getFaqsData($identifier);
        //                 break;

        //             case 'videos':
        //                 $data = $this->getVideosData($request);
        //                 break;  

        //             case 'ir-search':
        //                 $data = $this->getIRSearch($request);
        //                 break;  

        //             case 'socmed-data': 
        //                 $data = $this->getSocmedData($request);
        //                 break;

        //             case 'buyers-journey': 
        //                 $data = $this->getBuyersJourneyData($request,$identifier);
        //                 break;

                        
        //         }
        //         break;
        //     case 'inner':
        //         switch ($identifier) {
        //             case 'properties':
        //                 $data = $this->getPropertiesInnerData($request);
        //                 break;
        //             case 'construction-updates':
        //                 $data = $this->getConstructionUpdateInnerData($request);
        //                 break;
        //             case 'careers';
        //                 $data = $this->getCareersInnerData($request);
        //                 break;
        //             case 'media-room';
        //                 $data = $this->getMediaRoomInnerData($request);
        //                 break;
        //             case 'payment-channels';
        //                 $data = $this->getPaymentChannelInnerData($request,$identifier);
            
        //         }
        //         break;
        // }

        return response([
            'record' => $data
        ]);
    }

    public function getSearchData($request) {
        $data = [];
        $query = $request->id;
        
        //get project details
        $data = Project::where('id', $query)
        ->select('id','name','phases','project_status_id','location_id','property_id','slug')
        ->with(['images' => function ($q) {
            $q->select( 'parent_id', 'alt', 'title', 'path', 'path_resized', 'category');
        }])
        ->with(['project_status' => function ($q) {
            $q->select('id', 'name');   
        }])
        ->with(['property' => function ($q) {
            $q->select('id', 'title', 'slug');   
        }])
        ->with(['location' => function ($q) {
            $q->select('id', 'name');   
        }])
        ->with(['construction_update' => function ($q) {
             $q->select('id', 'date', 'enabled', 'project_id')
            ->where(['enabled' => 1])
            ->limit(3)
            ->orderBy('date', 'desc')
            ->with(['images' => function ($q) {
                $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'category','sequence');
            }]);   
        }])
        ->with(['project_award' => function ($q) {
            $q->select('id', 'name', 'content', 'project_id')
           ->where(['enabled' => 1])
           ->with(['images' => function ($q) {
               $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'category','sequence');
           }]);   
       }])
        ->first();


        $data['articles'] = Article::where('enabled', 1)
            ->orderBy('date','desc')
            ->select('id','title','article_category_id','slug','date','keyword')
            ->with(['articleCategory','images'])
            ->where('keyword', 'LIKE', '%' . strtolower($data->name) . '%')
            ->get();

        
        

        return $data;
    }

    public function getIRSearch($request) {

            $query = $request->keyword;
            $data = [];

            if (empty($query)) {
                return response([
                    'records' => 'No Results Found'
                ]);
            }
    
            $ir_pages = ['Company Announcements', 'Corporate Governance', 'Disclosures',
                        'Dividend Information', 'Fact Sheet', 'Financial Calendar',
                        'Financial Information', 'Investor Presentations',
                        'Maestro E-Newsletter', 'Share Information', 'Stockholders Meeting',
                        'Videos', 'FAQs'];
            //get page
            $data['pages'] = Page::where('name', 'LIKE', '%' . strtolower($query) . '%')
                        ->whereIn('name', $ir_pages)
                        ->select('name','identifier','slug')
                        ->get();
            //get files
            $data['files'] = PageFile::where('title', 'LIKE', '%' . strtolower($query) . '%')
                        ->select('title','tag_id','id')
                        ->with(['File' => function ($query) {
                            $query->select('parent_id','path','path_resized');
                        }])
                        ->with(['tag' => function ($query) {
                            $query->select('id','title')
                                ->where('status', 1);
                        }])->get();
            //get videos
            $data['videos'] = Video::where('yt_title', 'LIKE', '%' . strtolower($query) . '%')
                        ->get();
    
            return $data;
    }


    public function getBuyersJourneyData($request, $identifier) {
        $data = [];
        
        $data = Page::where('identifier', $identifier)
        ->with(['metadata'])
        ->first();

        // $data->modules = ($data->modules ? json_decode($data->modules) : []);
        // if (in_array('banner', $data->modules)) {
        //     $data->load([
        //         'banners' => function ($q) {
        //             $q->orderBy('sequence')
        //             ->with('images');
        //     }]);
        // }

        // if (in_array('card', $data->modules)) {
        //     $data->load([
        //         'cards' => function ($q) {
        //             $q->orderBy('sequence');
        //     }]);
        // }

        // if (in_array('cta', $data->modules)) {
        //     $data->load([
        //         'ctas' => function ($q) {
        //             $q->orderBy('sequence')
        //             ->with('images');
        //     }]);
        // }


        return $data;
    }

    public function getVirtualTourData($request,$identifier) {
        $data = [];

        $data = Page::where('identifier', $identifier)
        ->with(['metadata'])
        ->first();

        $data->modules = ($data->modules ? json_decode($data->modules) : []);
        if (in_array('banner', $data->modules)) {
            $data->load([
                'banners' => function ($q) {
                    $q->orderBy('sequence')
                    ->with('images');
            }]);
        }
        //get projects with virtual tour links
        $data['projects'] = Project::select('id','name','virtual_tour_link','property_id')
            ->with(['images' => function ($q) {
                $q->where(['category' => 'project_logo']);
            }])
            // ->with('images')
            ->with(['property' => function ($q) {
                $q->select('id', 'title');
            }])
            ->where('virtual_tour_link','!=','null')
            ->where('virtual_tour_link','!=','#')
            ->whereNotNull('virtual_tour_link')
            ->get();
        
        $project_types= Project::select('property_id')
            ->with(['property' => function ($q) {
                $q->select('id', 'title');
            }])
            ->where('virtual_tour_link','!=','null')
            ->where('virtual_tour_link','!=','#')
            ->whereNotNull('virtual_tour_link')
            ->get();

        $temp = [];    
        
        
        foreach ($project_types as $item) {
            // Extract the property title and add it to the $propertyTitles array if it's not already present
            if (!in_array($item['property']['title'], $temp)) {
                $temp[] = $item['property']['title'];
            }
        }

        $data['project_types'] = $temp;

        
        return $data;
    }

    public function getMediaRoomInnerData($request) {
        $data = [];
        $data['article'] = Article::where('slug',$request->slug)
                            ->select('id','title','content','slug','date')
                            ->with(['metadata','images'])
                            ->get();        


        //get latest articles
        $data['latest_articles'] = Article::where('enabled', 1)
            ->orderBy('date','desc')
            ->select('id','title','article_category_id','slug','date')
            ->with(['articleCategory','images'])
            ->limit(3)
            ->get();

        return $data;
    }

    public function getMediaRoomData($request,$identifier) {
        $data = [];

        $data = Page::where('identifier', $identifier)
            ->with(['metadata'])
            ->first();

        //get latest articles
        $data['latest_articles'] = Article::where('enabled', 1)
            ->orderBy('date','desc')
            ->select('id','title','content','article_category_id','slug','date')
            ->with(['articleCategory','images'])
            ->limit(4)
            ->get();
        
        
        $data['articles'] = Article::where('enabled', 1)
            //->orderBy('date','desc')
            ->select('id','title','content','article_category_id','slug','date','keyword')
            ->with(['articleCategory','images'])
            ->when($request->filled('filter'), function ($query) use ($request) {
                $query->whereYear('date', '=', $request->filter);
            })
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $query->where('keyword', 'LIKE', '%' . strtolower($request->keyword) . '%');
            })
            ->when($request->filled('sort'), function ($query) use ($request) {
                $query->orderBy('date', $request->sort);
            }, 
            function ($query) {
                $query->orderBy('date', 'desc');
            })
            ->paginate(9);
            // ->onEachSide(3);

        
        $years = Article::where('enabled', 1)
            ->orderBy('date','desc')
            ->select('date')
            ->get()
            ->pluck('date');

         $data['years'] = collect($years)->map(function($date) {
                return substr($date, 0, 4); // Extracting only the year part
            })->unique()->values()->toArray();

        return $data;
    }

    //IR PAGES
    public function getInvestorRelationsData($request) {
        $data = [];
        $data = Page::where('identifier', 'investor-relations')
            ->with(['metadata'])
            ->first();
        $data->modules = ($data->modules ? json_decode($data->modules) : []);
        if (in_array('banner', $data->modules)) {
            $data->load([
                'banners' => function ($q) {
                    $q->orderBy('sequence')
                    ->with('images');
            }]);
        }
        return $data;
    }

    public function getDisclosuresData($request,$identifier) {
        $data = [];
        $data = Page::where('identifier', $identifier)
            ->with(['metadata'])
            ->first();

        $data->modules = ($data->modules ? json_decode($data->modules) : []);

        if (in_array('tag', $data->modules)) {
            $data->load([
                'tags' => function ($q) {
                    $q->orderBy('sequence');
                }
            ]);
        }

        if (in_array('file', $data->modules)) {
            $data->load([
                'files' => function ($q) {
                    $q->orderBy('date_published','desc')
                    ->with('files');
            }]);
        }

        return $data;
    }

    
    public function getStockholdersMeetingData($request,$identifier) {
        $data = [];
        $data = Page::where('identifier', $identifier)
            ->with(['metadata'])
            ->first();

        $data->modules = ($data->modules ? json_decode($data->modules) : []);

        if (in_array('tag', $data->modules)) {
            $data->load([
                'tags' => function ($q) {
                    $q->orderBy('sequence');
                }
            ]);
        }

        if (in_array('banner', $data->modules)) {
            $data->load([
                'banners' => function ($q) {
                    $q->orderBy('sequence')
                    ->with('images');
                }]);
        }

        

        if (in_array('file', $data->modules)) {
            $data->load([
                'files' => function ($q) {
                    $q->orderBy('date_published','desc')
                    ->with('files');
            }]);
        }

        return $data;
    }


    public function getCorporateGovernanceData($request,$identifier) {
        $data = [];
        $data = Page::where('identifier', $identifier)
            ->with(['metadata'])
            ->first();

        $data->modules = ($data->modules ? json_decode($data->modules) : []);

        if (in_array('tag', $data->modules)) {
            $data->load([
                'tags' => function ($q) {
                    $q->orderBy('sequence');
                }
            ]);
        }

        if (in_array('file', $data->modules)) {
            $data->load([
                'files' => function ($q) {
                    $q->orderBy('title','asc')
                    ->with('files');
            }]);
        }

        return $data;
    }

    public function getStaticIRData($request) {
        $data = [];
        $data = Page::where('identifier', $request)
            ->with(['metadata'])
            ->first();
        return $data;
    }

    public function getFaqsData($request) {
        $data = [];
        $data = Page::where('identifier', $request)
            ->with(['metadata'])
            ->first();

        $data->modules = ($data->modules ? json_decode($data->modules) : []);
        // faqs
        if (in_array('faq', $data->modules)) {
            $data->load([
                'faqs' => function ($q) {
                    $q->orderBy('sequence');
                }
            ]);
        }

        return $data;
    }


    public function getVideosData($request) {
        $data = [];
        $data = Page::where('identifier', 'videos')
        ->with(['metadata'])
        ->first();
        //get investor briefing tab
        $data['investor_briefing'] = Video::where([
            'enabled'   => 1,
        ])
        ->orderBy('yt_published_date','desc')
        ->whereHas('videoCategory', function ($query) {
            $query->where('slug', 'investor-and-analyst-briefings');
        })
        ->get();
        //get live news tab
        $data['live_news'] = Video::where([
            'enabled'   => 1,
        ])
        ->orderBy('yt_published_date','desc')
        ->whereHas('videoCategory', function ($query) {
            $query->where('slug', 'live-news-and-interviews');
        })
        ->get();
        //get asm videos tab
        $data['asm_videos'] = Video::where([
            'enabled'   => 1,
        ])
        ->orderBy('yt_published_date','desc')
        ->whereHas('videoCategory', function ($query) {
            $query->where('slug', 'asm-videos');
        })
        ->get();
        return $data;
    }    

    public function getCareersData($request) {
        $data = [];
        $data = Page::where('identifier', 'careers')
            ->with(['metadata'])
            ->first();
        $data->modules = ($data->modules ? json_decode($data->modules) : []);
         // banners
         if (in_array('banner', $data->modules)) {
            $data->load([
                'banners' => function ($q) {
                    $q->orderBy('sequence')
                    ->with('images');
                }]);
        }
         // cards
         if (in_array('card', $data->modules)) {
            $data->load([
                'cards' => function ($q) {
                    $q->orderBy('sequence');
                }]);
                
        }
        $data['careers'] = Career::where('enabled', true)
        ->with('department')
        ->when($request->filled('filter'), function ($query) use ($request) {
            $query->whereHas('department', function ($subQuery) use ($request) {
                $subQuery->where('name', $request->filter);
            });
        })
        ->when($request->filled('sort'), function ($query) use ($request) {
            $query->orderBy('date_published', $request->sort);
        }, 
        function ($query) {
            $query->orderBy('date_published', 'desc');
        })
        ->get();

            // ->paginate(9)
            // ->onEachSide(3);        
        
        // get departments
        $data['department'] = Career::join('departments', 'careers.department_id', '=', 'departments.id')
        ->select('departments.id', 'departments.name')
        ->distinct()
        ->get();
        

        $data['testimonials'] = Testimonial::where(['enabled' => 1,])
        ->orderBy('order')
        ->where('category','career')
        ->with('images', function ($q) {
            $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'model', 'category');
        })
        ->get();
        
        return $data;
    }

    public function getPaymentChannelInnerData($request,$identifier) {
        $data = [];


        $data = Page::where('identifier', $identifier)
            ->with(['metadata'])
            ->first();

        $data->modules = ($data->modules ? json_decode($data->modules) : []);

        if (in_array('tag', $data->modules)) {
            $data->load([
                'tags' => function ($q) {
                    $q->orderBy('sequence');
                }
            ]);
        }

        if (in_array('file', $data->modules)) {
            $data->load([
                'files' => function ($q) {
                    $q->orderBy('created_at')
                    ->with('files');
            }]);
        }

        $data['payment_type_data'] = PaymentType::where('slug', $request->slug)
        ->with(['metadata'])
        ->get();

        return $data;
    }

    public function getBuyersGuideData($request) {
        $data = [];
        $data = Page::where('identifier', 'buyers-guide')
        ->with(['metadata'])
        ->first();
        $data->modules = ($data->modules ? json_decode($data->modules) : []);
        // banners
        if (in_array('banner', $data->modules)) {
            $data->load([
                'banners' => function ($q) {
                    $q->orderBy('sequence')
                    ->with('images');
                }]);
        }
        // cards
        if (in_array('card', $data->modules)) {
            $data->load([
                'cards' => function ($q) {
                    $q->orderBy('sequence');
                }]);
        }
        // uvps
        if (in_array('uvp', $data->modules)) {
            $data->load([
                'uvps' => function ($q) {
                    $q->orderBy('sequence')
                    ->with('images');
                }]);
        }
        // faqs
        if (in_array('faq', $data->modules)) {
            $data->load([
                'faqs' => function ($q) {
                    $q->orderBy('sequence')
                    ->with('images');
                }]);
        }
        //tags
        if (in_array('tag', $data->modules)) {
            $data->load([
                'tags' => function ($q) {
                    $q->orderBy('sequence');
                }
            ]);
        }
        //files
        if (in_array('file', $data->modules)) {
            $data->load([
                'files' => function ($q) {
                    $q->orderBy('date_published','desc')
                    ->with('files');
            }]);
        }

        //get buyers guide video
        $data['video'] = Video::where([
            'enabled'   => 1,
        ])
        ->whereHas('videoCategory', function ($query) {
            $query->where('slug', 'buyers-guide');
        })
        ->get();

        $data['payment_types'] = PaymentType::where([
            'enabled'   => 1,
        ])
        ->orderBy('order')
        ->get();

        return $data;
    }


    public function getSustainabilityData($request) {
        $data = [];
        $data = Page::where('identifier', 'sustainability')
        ->with(['metadata'])
        ->first();
        $data->modules = ($data->modules ? json_decode($data->modules) : []);
        // banners
        if (in_array('banner', $data->modules)) {
            $data->load([
                'banners' => function ($q) {
                    $q->orderBy('sequence')
                    ->with('images');
                }
            ]);
        }
        // cards
        if (in_array('card', $data->modules)) {
            $data->load([
                'cards' => function ($q) {
                    $q->orderBy('sequence');
                }
            ]);
        }
        // uvps
        if (in_array('uvp', $data->modules)) {
            $data->load([
                'uvps' => function ($q) {
                    $q->orderBy('sequence')
                    ->with('images');
                }
            ]);
        }
        return $data;
    }

    public function getSocmedData($request) {
        $data = [];
        $data['link'] = WebsiteSetting::first();
        return $data;
    }

    public function getResidencesData($request) {
        $data = [];  

        $data = Page::where('identifier', 'residences')
        ->with(['metadata'])
        ->first();

        $residence = Property::where('slug', 'residences')->first();
        
        $data['property_types'] = Property::where([
            'enabled' => 1,
            'parent' => $residence->id
            ])
            ->orderBy('order')
            ->with(['images' => function ($q) {
                $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'category')
                ->where(['category' => 'main_image']);
            }])
            ->get();

        $data['banner'] = Property::where('slug', 'residences')
            ->select('id','title','slug','tagline')
            ->with(['images' => function ($q) {
                    $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'category');
                }])
            ->get();

        
        $data->modules = ($data->modules ? json_decode($data->modules) : []);

        // cta's
        if (in_array('cta', $data->modules)) {
            $data->load([
                'ctas' => function ($q) {
                    $q->orderBy('sequence')
                        ->with('images');
                }
            ]);
        }

        // cards
        if (in_array('card', $data->modules)) {
            $data->load([
                'cards' => function ($q) {
                    $q->orderBy('sequence')
                        ->with('images');
                }
            ]);
        }

         //get featured projects
         $data['projects'] = Project::where([
            'enabled' => 1,
            'featured' => 1,
        ])
        ->orderBy('name')
        ->select('id','name','slug','location_id','project_status_id','property_id')
        ->with('images', function ($q) {
            $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'model', 'category');
        })
        ->with(['project_status' => function ($q) {
            $q->select('id', 'name');   
        }])
        ->with(['property' => function ($q) {
            $q->select('id', 'title','slug');   
        }])
        ->with(['location' => function ($q) {
            $q->select('id', 'name');   
        }])
        ->get();
        
        return $data;
      
    }
    
    public function getConstructionUpdateInnerData($request)
    {
        $data = [];

        $data['projects_data'] = Project::where('slug', $request->slug)
            ->select('id','name')
            ->with(['construction_update' => function ($q) {
                $q->orderBy('date', 'desc')
                ->with(['images' => function ($q) {
                    $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'category','sequence')
                    ->orderBy('sequence');
                }]);
            }])
            ->with(['metadata'])
            ->get();
        
        $data['years'] = Project::where('slug', $request->slug)
            ->select('id')
            ->with(['construction_update' => function ($q) {
                $q->orderBy('date', 'desc');
            }])
            ->get();
        
        return $data;
    }

    public function getConstructionUpdateData($request)
    {
        $data = [];

        $data = Page::where('identifier', 'construction-updates')
        ->with(['metadata'])
        ->first();

        $data['construction_updates'] = Project::where('enabled',1)
        ->select('id','name','slug','property_id','location_id',)
        ->orderBy('name','asc')
        ->with(['images' => function ($q) {
            $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'category')
            ->where(['category' => 'project_logo']);
        }])
        ->with(['property' => function ($q) {
            $q->select('id', 'title','slug');   
        }])
        ->with(['location' => function ($q) {
            $q->select('id', 'name');   
        }])
        ->whereHas('construction_update', function ($q) {
            $q->where('enabled', 1);
        })
        ->with(['construction_update' => function ($q) {
            $q->select('id', 'date', 'enabled', 'project_id')
           ->where(['enabled' => 1])
           ->orderBy('date', 'desc')
           ->with(['images' => function ($q) {
               $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'category','sequence');
           }]);   
       }])
       ->get();

        $data['locations'] = Location::select('id', 'name')
            ->distinct() // Use the distinct method to retrieve unique years
            ->pluck('name');
           
        $data['property_types'] = Property::select('id', 'title')
            ->where('enabled', true)
            ->distinct() // Use the distinct method to retrieve unique years
            ->pluck('title');

        return $data;

    }

    // inner pages
    public function getCareersInnerData($request) {
        $data=[];
        $data['careers'] = Career::orderBy('date_published','desc')
                            ->where('enabled', true)
                            ->where('slug',$request->slug)
                            ->with('department')
                            ->with(['metadata'])
                            ->get();
        return $data;
    }

    public function getPropertiesInnerData($request) {
        $data=[];
        //if full details
        if($request->details == true) {
            
            $data['projects_data'] = Project::where('slug', $request->slug)
            ->with(['metadata'])
            //->select('id','name','architect as architect_name','land_area')
            ->select('id','name','architect as architect_name','land_area','full_address','partners','contact_person','position','contact_details',
                     'phases','virtual_tour_link','title','content_col_1','content_col_2','property_id','location_id','project_status_id')
            ->with(['images' => function ($q) {
                $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'category');
            }])
            ->with(['project_status' => function ($q) {
                $q->select('id', 'name');   
            }])
            ->with(['property' => function ($q) {
                $q->select('id', 'title','slug');   
            }])
            ->with(['location' => function ($q) {
                $q->select('id', 'name');   
            }])
            ->with(['vicinity' => function ($q) {
                $q->orderBy('order')
                ->select('id', 'content', 'order', 'project_id');   
            }])
            ->with(['amenity' => function ($q) {
                $q->orderBy('order')
                ->select('id', 'content', 'order', 'project_id')
                ->with(['images' => function ($q) {
                    $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'category');
                }]);
            }])
            ->with(['floorplan' => function ($q) {
                $q->orderBy('order')
                ->select('id', 'description', 'order', 'project_id')
                ->with(['images' => function ($q) {
                    $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'category');
                }]);  
            }])
            ->with(['architect' => function ($q) {
                $q->orderBy('order')
                ->select('id', 'title', 'order', 'project_id')
                ->with(['images' => function ($q) {
                    $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'category');
                }]);   
            }])
            ->with(['construction_update' => function ($q) {
                 $q->select('id', 'date', 'enabled', 'project_id')
                ->where(['enabled' => 1])
                ->limit(3)
                ->orderBy('date', 'desc')
                ->with(['images' => function ($q) {
                    $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'category','sequence');
                }]);   
            }])
            ->with(['project_award' => function ($q) {
                $q->select('id', 'name', 'content', 'project_id')
               ->where(['enabled' => 1])
               ->with(['images' => function ($q) {
                   $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'category','sequence');
               }]);   
           }])
            ->get();
           
        }
        //if short details
        //get data outside
        else {

            $data['property'] = Property::where('slug', $request->slug)
            ->with(['metadata'])
            ->select('id','title','slug','tagline')
            ->with(['images' => function ($q) {
                    $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'category');
                }])
            ->get();
    
            $data['projects_data'] = Property::where('slug', $request->slug)
            ->with([('projects') => function ($q) {
                $q->orderBy('name','asc')
                ->where('enabled',1)
                ->select('id', 'name','slug','order','phases','location_id','project_status_id','property_id')
                    ->with('images', function ($q) {
                        $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'model', 'category');
                    })
                    ->with(['location' => function ($q) {
                        $q->select('id', 'name');
                    }])
                    ->with(['project_status' => function ($q) {
                        $q->select('id', 'name');
                    }]);
            }])
            ->get();

            $data['property_type'] = Property::where([
                'enabled' => 1,
                'featured' => 1,
                'parent' => 'root'
            ])
            ->orderBy('order')
            ->with('images', function ($q) {
                $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'model', 'category');
            })
            ->get();

            $data['locations'] = Location::select('id', 'name')
            ->distinct() // Use the distinct method to retrieve unique years
            ->pluck('name');
           
            $data['project_statuses'] = ProjectStatus::select('id', 'name')
            ->distinct() // Use the distinct method to retrieve unique years
            ->pluck('name');
        }

        return $data;
    }

    // Privacy Policy
    public function getPrivacyPolicy($request)
    {
        $data = Page::where('identifier', 'privacy-policy')
            ->with(['metadata'])
            ->first();

        return $data;
    }

    // Terms and Conditions
    public function getTermsAndCondition($request)
    {
        $data = Page::where('identifier', 'terms-and-conditions')
            ->with(['metadata'])
            ->first();

        return $data;
    }


    // Sitemap
    public function getSiteMap($request)
    {
        $data = Page::where('identifier', 'sitemap')
            ->with(['metadata'])
            ->first();

        return $data;
    }


    // landing pages
    public function getHomepageData($request, string $identifier)
    {
        $data = [];

        // $data = Page::where('identifier', 'home')
        //     ->with(['metadata'])
        //     ->first();

        $data = Page::whereIdentifier($identifier)
        ->with(['metadata', 'page_sections' => function ($q) {
            $q->orderBy('order')->with('images', 'buttons');
        }])
        ->first();

        if (!$data) {
            return response([
                'errors' => ['Page not found!']
            ], 404);
        }

        
        return $data;
    }
    public function getOurCompanyData($request, string $identifier)
    {
        $data = [];

        // $data = Page::where('identifier', 'home')
        //     ->with(['metadata'])
        //     ->first();

        $data = Page::whereIdentifier($identifier)
        ->with('metadata')
        ->first();

        if (!$data) {
            return response([
                'errors' => ['Page not found!']
            ], 404);
        }

        $data->modules = ($data->modules ? json_decode($data->modules) : []);
        
        // banners
        if (in_array('banner', $data->modules)) {
            $data->load([
                'banners' => function ($q) {
                    $q->orderBy('sequence')
                        ->with('images');
                }
            ]);
        }
        
        // cta's
        if (in_array('cta', $data->modules)) {
            $data->load([
                'ctas' => function ($q) {
                    $q->orderBy('sequence')
                        ->with('images');
                }
            ]);
        }

        // cards
        if (in_array('card', $data->modules)) {
            $data->load([
                'cards' => function ($q) {
                    $q->orderBy('sequence')
                        ->with('images');
                },
                'cards.pageTemplate' => function ($q) {
                    $q->orderBy('created_at', 'ASC')
                    ->with('images');
                },
                // 'cards.pageTemplate.taxServices' => function ($q) {
                //     $q->orderBy('created_at', 'ASC')
                //     ->with('images');
                // },
            ]);
        }

        
        // uvps
        if (in_array('uvp', $data->modules)) {
            $data->load([
                'uvps' => function ($q) {
                    $q->orderBy('sequence')
                        ->with('images');
                },
                'uvps.uvpDetails' => function ($q) {
                    $q->orderBy('created_at', 'ASC')
                    ->with('images');
                },
            ]);
        }

        $data['values'] = Philosophy::orderBy('order')
                        ->with('images')
                        ->get();

        $data['history'] = History::orderBy('order')
                        ->with('images')
                        ->get();

        $data['awards'] = Award::where('featured', 1)->count() !== 0 ? 
                        Award::where('featured', 1)
                        ->orderBy('order')
                        ->with('images')
                        ->limit(4)
                        ->get() : 
                        Award::orderBy('year', 'desc')
                        ->orderBy('order')
                        ->with('images')
                        ->limit(4)
                        ->get();


        return $data;
    }

    public function getOurBusinessData($request, string $identifier)
    {
        $data = [];

        // $data = Page::where('identifier', 'home')
        //     ->with(['metadata'])
        //     ->first();

        $data = Page::whereIdentifier($identifier)
        ->with('metadata')
        ->first();

        if (!$data) {
            return response([
                'errors' => ['Page not found!']
            ], 404);
        }

        $data->modules = ($data->modules ? json_decode($data->modules) : []);
        
        // banners
        if (in_array('banner', $data->modules)) {
            $data->load([
                'banners' => function ($q) {
                    $q->orderBy('sequence')
                        ->with('images');
                }
            ]);
        }
        
        // cta's
        if (in_array('cta', $data->modules)) {
            $data->load([
                'ctas' => function ($q) {
                    $q->orderBy('sequence')
                        ->with('images');
                }
            ]);
        }

        // cards
        if (in_array('card', $data->modules)) {
            $data->load([
                'cards' => function ($q) {
                    $q->orderBy('sequence')
                        ->with('images');
                },
                'cards.pageTemplate' => function ($q) {
                    $q->orderBy('created_at', 'ASC')
                    ->with('images');
                },
                // 'cards.pageTemplate.taxServices' => function ($q) {
                //     $q->orderBy('created_at', 'ASC')
                //     ->with('images');
                // },
            ]);
        }

        
        // uvps
        if (in_array('uvp', $data->modules)) {
            $data->load([
                'uvps' => function ($q) {
                    $q->orderBy('sequence')
                        ->with('images');
                },
                'uvps.uvpDetails' => function ($q) {
                    $q->orderBy('created_at', 'ASC')
                    ->with('images');
                },
            ]);
        }

        $data['business_units'] = BusinessUnit::orderBy('order')
                                ->with('images')
                                ->get();


        return $data;
    }

    
    public function getLeadersData($request)
    {
            $data = [];
    
            $data = Page::where('identifier', 'our-leaders')
            ->with(['metadata'])
            ->first();

            $data->modules = ($data->modules ? json_decode($data->modules) : []);
        
            // banners
            if (in_array('banner', $data->modules)) {
                $data->load([
                    'banners' => function ($q) {
                        $q->orderBy('sequence')
                            ->with('images');
                    }
                ]);
            }
            // cta's
            if (in_array('cta', $data->modules)) {
                $data->load([
                    'ctas' => function ($q) {
                        $q->orderBy('sequence')
                            ->with('images');
                    }
                ]);
            }

            // cards
            if (in_array('card', $data->modules)) {
                $data->load([
                    'cards' => function ($q) {
                        $q->orderBy('sequence')
                            ->with('images');
                    }
                ]);
            }

            $data['boards'] = Board::orderBy('order')
            ->where('enabled',1)
            ->with('images', function ($q) {
                $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'model', 'category');
            })
            ->get();

            $data['executives'] = Executive::orderBy('order')
            ->where('enabled',1)
            ->with('images', function ($q) {
                $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'model', 'category');
            })
            ->with('position')
            ->get();
    
            return $data;
    }

    public function getPropertiesData($request, $identifier) {
        $data = [];
        $data = Page::whereIdentifier($identifier)
        ->with(['metadata', 'page_sections' => function ($q) {
            $q->orderBy('order')->with('images', 'buttons');
        }])
        ->first();

        if (!$data) {
            return response([
                'errors' => ['Page not found!']
            ], 404);
        }
        return $data;
    }


    public function getCliStoriesData($request) {
        $data = [];
        $data = Page::where('identifier', 'cli-stories')
            ->with(['metadata'])
            ->first();
        $data->modules = ($data->modules ? json_decode($data->modules) : []);
        // banners
        if (in_array('banner', $data->modules)) {
            $data->load([
                'banners' => function ($q) {
                    $q->orderBy('sequence')
                        ->with('images');
                }
            ]);
        }
        // cta's
        if (in_array('cta', $data->modules)) {
            $data->load([
                'ctas' => function ($q) {
                    $q->orderBy('sequence')
                        ->with('images');
                }
            ]);
        }
        // cards
        if (in_array('card', $data->modules)) {
            $data->load([
                'cards' => function ($q) {
                    $q->orderBy('sequence')
                        ->with('images');
                }
            ]);
        }
        //get stories
        // $data['stories'] = Story::where([
        //     'enabled'   => 1,
        // ])
        // ->orderBy('date','DESC')
        // ->with('images', function ($q) {
        //     $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'model', 'category');
        // })
        // ->get();

        //get investor briefing tab
        $data['stories'] = Video::where([
            'enabled'   => 1,
        ])
        ->orderBy('yt_published_date','desc')
        ->whereHas('videoCategory', function ($query) {
            $query->where('slug', 'cli-stories');
        })
        ->get();

        //get testimonials
        $data['testimonials'] = Testimonial::where([
            'enabled'   => 1,
        ])
        ->orderBy('order')
        ->where('category','customer')
        ->with('images', function ($q) {
            $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'model', 'category');
        })
        ->get();

        return $data;
    }

    public function getAwardData($request) {
        $data = [];
        $data = Page::where('identifier', 'awards-and-recognitions')
            ->with(['metadata'])
            ->first();
        $data->modules = ($data->modules ? json_decode($data->modules) : []);
        // banners
        if (in_array('banner', $data->modules)) {
            $data->load([
                'banners' => function ($q) {
                    $q->orderBy('sequence')
                        ->with('images');
                }
            ]);
        }
        // cta's
        if (in_array('cta', $data->modules)) {
            $data->load([
                'ctas' => function ($q) {
                    $q->orderBy('sequence')
                        ->with('images');
                }
            ]);
        }
        // cards
        if (in_array('card', $data->modules)) {
            $data->load([
                'cards' => function ($q) {
                    $q->orderBy('sequence')
                        ->with('images');
                }
            ]);
        }
        // get awards
        $data['awards'] = Award::select(
            'id', 'name', 'content', 'enabled', 'featured', 'year', 'sector'
        )
        ->where([
            'enabled'   => 1,    
        ])
        ->orderBy('order')
        ->with('images', function ($q) {
            $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'model', 'category');
        })
        ->with(['metadata'])
        ->get();
        // get years
        $data['years'] = Award::where('enabled', 1)
            ->orderBy('year', 'DESC')
            ->distinct() // Use the distinct method to retrieve unique years
            ->pluck('year');
  
        return $data;
    }


    public function getContactUsData($request) {
        $data = [];
        $data = Page::where('identifier', 'contact-us')
            ->with(['metadata'])
            ->first();

        $data->modules = ($data->modules ? json_decode($data->modules) : []);
        // banners
        if (in_array('banner', $data->modules)) {
            $data->load([
                'banners' => function ($q) {
                    $q->orderBy('sequence')
                        ->with('images');
                }
            ]);
        }
        // cta's
        if (in_array('cta', $data->modules)) {
            $data->load([
                'ctas' => function ($q) {
                    $q->orderBy('sequence')
                        ->with('images');
                }
            ]);
        }
        // cards
        if (in_array('card', $data->modules)) {
            $data->load([
                'cards' => function ($q) {
                    $q->orderBy('sequence')
                        ->with('images');
                }
            ]);
        }
        //get emails
        $data['email'] = Email::where([
            'enabled' => 1,
        ])
        ->orderBy('order')
        ->get();

        $data['office'] = Office::where([
            'enabled' => 1,
        ])
        ->orderBy('order')
        ->get();

        foreach ($data['office'] as $key => $value) {
            $value->emails = json_decode($value->emails);
        }
        //get departments
        $data['departments'] = Email::where([
            'enabled' => 1,
        ])
        ->distinct() 
        ->pluck('department');

        return $data;
    }

    //get about us data
    public function getAboutUsData($request)
    {
        $data = [];
        $data = Page::where('identifier', 'about-us')
            ->with(['metadata'])
            ->first();
        $data->modules = ($data->modules ? json_decode($data->modules) : []);
        // banners
        if (in_array('banner', $data->modules)) {
            $data->load([
                'banners' => function ($q) {
                    $q->orderBy('sequence')
                        ->with('images');
                }
            ]);
        }
        // cta's
        if (in_array('cta', $data->modules)) {
            $data->load([
                'ctas' => function ($q) {
                    $q->orderBy('sequence')
                        ->with('images');
                }
            ]);
        }
        // cards
        if (in_array('card', $data->modules)) {
            $data->load([
                'cards' => function ($q) {
                    $q->orderBy('sequence')
                        ->with('images');
                }
            ]);
        }
        //get mission
        $data['mission'] = MissionVision::where([
            'title' => 'mission'
        ])
        ->with('images', function ($q) {
            $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'model', 'category');
        })
        ->first();
        //get vision
        $data['vision'] = MissionVision::where([
            'title' => 'vision'
        ])
        ->with('images', function ($q) {
            $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'model', 'category');
        })
        ->first();
        //get awards
        $data['awards'] = Award::where([
            'enabled'   => 1,
            'featured' => 1    
        ])
        ->orderBy('order')
        ->with('images', function ($q) {
            $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'model', 'category');
        })
        ->get();
        //get cli advantage
        $data['advantage'] = Advantage::orderBy('order')
        ->with('images', function ($q) {
            $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'model', 'category');
        })
        ->get();
        //get core values
        $data['core_values'] = Philosophy::orderBy('order')
        ->with('images', function ($q) {
            $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'model', 'category');
        })
        ->get();
        //get history
        $data['history'] = History::select(
            'id', 'title', 'subtitle', 'year', 'order')
        ->orderBy('order')
        ->with('images', function ($q) {
            $q->select('id', 'parent_id', 'alt', 'title', 'path', 'path_resized', 'model', 'category');
        })
        ->get();
        //get history years
        $data['history_years'] = History::orderBy('year', 'DESC')
            ->distinct() // Use the distinct method to retrieve unique years
            ->pluck('year');

        return $data;
    }

    
    //sitemap
    public function sitemap(): Response
    {
        $sitemap = [];

        array_push($sitemap, (object) ['url' => '']);

        $excludedSlugs = ['our-leaders', 'cli-stories', 'awards'];
        $pages = Page::select('id','name','identifier',)
            ->orderBy('order')
            ->whereNotIn('identifier', $excludedSlugs)
            ->get();
    
        foreach ($pages as $row) {
            $toPush = (object) ['url' => "/$row->identifier"];
            array_push($sitemap, $toPush);
        }

        //inner page
        $inner_pages = Page::select('id','name','identifier',)
            ->orderBy('order')
            ->whereIn('identifier', $excludedSlugs)
            ->get();

        foreach ($inner_pages as $row) {
            $toPush = (object) ['url' => "/about-us/$row->identifier"];
            array_push($sitemap, $toPush);
        }

        //Properties
        $properties = Property::select('id', 'title', 'slug', 'enabled')
            ->where([
                'enabled' => 1,
            ])
            ->orderBy('order')
            ->get();

        foreach ($properties as $row) {
            $toPush = (object) ['url' => "/properties/$row->slug"];
            array_push($sitemap, $toPush);
        }

        //construction updates
        $construction_updates = Project::where('enabled',1)
            ->select('id','name','slug','property_id','location_id',)
            ->orderBy('order')
            ->whereHas('construction_update', function ($q) {
                $q->where('enabled', 1);
            })
            ->with('construction_update')
            ->get();
        
        foreach ($construction_updates as $row) {
            $toPush = (object) ['url' => "/construction-updates/$row->slug"];
            array_push($sitemap, $toPush);
        }

        // projects
        $projects = Project::where('enabled', 1)
            ->select('id', 'name', 'slug', 'property_id')
            ->with(['property' => function ($q) {
                $q->select('id', 'title', 'slug');
            }])
            ->get();

        foreach ($projects as $row) {
            $toPush = (object) ['url' => "/" . $row->property->slug . "/" . $row->slug];
            array_push($sitemap, $toPush);
        }

        return response([
            'sitemap' => $sitemap
        ]);
    }
}
