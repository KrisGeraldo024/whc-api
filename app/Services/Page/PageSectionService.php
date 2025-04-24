<?php

namespace App\Services\Page;

use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\{
    DB,
    Validator,
    Facade,
};
use App\Models\{
    Accordion,
    Board,
    BusinessUnit,
    Button,
    Page,
    PageSection
};
use App\Traits\GlobalTrait;

class PageSectionService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * PageSectionService index
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function index($request, $identifier): Response
    {
        $page = Page::whereIdentifier($identifier)->first();
        $page_sections = PageSection::orderBy('sequence')
        ->where('page_id', $page->id)
        ->when( $request->filled('all') , function ($q, $request) {
            return $q->get();
        }, function ($q) {
            return $q->paginate(20);
        });

        return response([
            'records' => $page_sections
        ]);
    }

    /**
     * PageSectionService store
     * @param Request $request
     * @param Page $page
     * @return Response
     */
    public function store($request, $pageIdentifier): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('Section', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'sometimes',
            'has_button' => 'sometimes',
            'button_name' =>  'sometimes',
            'is_link_out' => 'sometimes',
            'link' =>  'sometimes',
            'sequence' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all(),
            ], 403);
        }

        $record = PageSection::create([
            'page_id' => $page->id,
            'title' => $request->title,
            'description' => $request->description,
            'has_button' => $request->has_button,
            'button_name' => $request->button_name ? $request->button_name : null,
            'is_link_out' => $request->is_link_out ? $request->is_link_out : 0,
            'link' => $request->link ? $request->link : '',
            'sequence' => $request->sequence,
            'primary_color' => $request->primary_color,  
        ]);

       
        if ($request->has('main_image')) {
            $this->addImages('page_section', $request, $record, 'main_image');
        }
        if ($request->has('mobile_image')) {
            $this->addImages('page_section', $request, $record, 'mobile_image');
        }
     
        
        $this->generateLog($request->user(), "created this page Section ({$record->id})");
       

        //$page_section->load('images');

        return response([
            'record' => $record
        ]);
    }

    /**
     * PageSectionService show
     * @param Request $request
     * @param Page $page
     * @param PageSection $page_section
     * @return Response
     */
    public function show($request,  PageSection $page_section): Response
    {
        // $page = Page::whereIdentifier($pageIdentifier)->first();
        // if (!in_array('Section', ($page->modules ? json_decode($page->modules) : [] ))) {
        //     return response([
        //         'errors' => ['This module is not available for this page.']
        //     ], 403);
        // }
        
        // $this->generateLog($request->user(), "viewed this page Section ({$page_section->id})");
        if ($page_section->name === "Suntrust Officers"){
            $page_section['boards'] = Board::orderBy('order')->with('images')->get();
        }

        $page_section->load(['files','images', 
        'buttons'  => function ($q) {
            $q->orderBy('order')->with('images');
        }, 'accordions' => function ($q) {
            $q->orderBy('order')->with('images');
        }]);
        $page_section->load([
            'logs' => function ($q) {
                $q->orderBy('updated_at', 'desc') // Order logs by updated_at in descending order
                  ->with([
                      'user' => function ($q) {
                          $q->with(['images', 'userDetail']); // Eager load user's images
                      },
                  ]);
            }
        ]);
        
        return response([
            'record' => $page_section
        ]);
    }

    /**
     * PageSectionService update
     * @param Request $request
     * @param Page $page
     * @param PageSection $page_section
     * @return Response
     */
    public function update($request, PageSection $page_section): Response
    { 
        // return response(['record' => $request->all()]);
        $page = Page::find($page_section->page_id);
        DB::beginTransaction();

        try {
            // Update PageSection basic fields
            $page_section->update([
                'title' => $request->title,
                'description' => $request->description,
                'has_button' => $request->has_button ?? 0,
            ]);

            // Load related models
            $this->loadRelatedModels($page_section);

            // Handle button updates or creation
            if ($request->has_button) {
                $this->updateOrCreateButtons($request, $page_section);
            }

            // Handle accordion updates or creation
            if ($request->has('accordion_title')) {
                $this->updateOrCreateAccordions($request, $page_section);
            }

            // Handle board updates for "Suntrust Officers" section
            if ($page_section->name === "Suntrust Officers") {
                $this->updateOrCreateBoards($request, $page_section);
            }

            // Update or add images and files
            $this->updateImagesAndFiles($request, $page_section);

            // Generate and load logs
            $this->generateLog($request->user(), "Changed", $page->name, $page_section);
            $this->loadRelatedModels($page_section);
            $this->loadRelatedLogs($page_section);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // \Log::info($e);
            return response(['error' => 'Transaction failed: ' . $e->getMessage()], 500);
        }

        return response(['record' => $page_section]);
    }

    /**
     * PageSectionService destroy
     * @param Request $request
     * @param Page $page
     * @param PageSection $page_section
     * @return Response
     */
    public function destroy($request, $pageIdentifier, PageSection $page_section): Response
    {
        $page = Page::whereIdentifier($pageIdentifier)->first();
        if (!in_array('Section', ($page->modules ? json_decode($page->modules) : [] ))) {
            return response([
                'errors' => ['This module is not available for this page.']
            ], 403);
        }
        
        $this->generateLog($request->user(), "deleted this page Section ({$page_section->id})");

        $page_section->delete();

        return response([
            'record' => 'Page Section deleted successfully!'
        ]);
    }


    // Helper Methods
    protected function loadRelatedModels(PageSection $page_section)
    {
        $page_section->load([
            'accordions' => function($q) {
                $q->orderBy('order')->with('images');
            },
            'buttons'  => function($q) {
                $q->orderBy('order')->with('images');
            },
            'images',
            'files',
        ]);
    }

    protected function updateOrCreateButtons($request, $page_section)
    {
        foreach ($request->button_name as $index => $btn_name) {
            $button = Button::find($request->button_id[$index]) ?? new Button(['parent' => $page_section->id]);
            $button->fill([
                'button_name' => $btn_name,
                'is_link_out' => $request->button_link_out[$index] ? 1 : 0,
                'link' => $request->button_link[$index],
            ])->save();

            if ($request->has('logo'.$index.'_id')) {
                $this->handleFileUpdate('button', $request, $button, 'logo'.$index, 0);
            }
        }
    }

    protected function updateOrCreateAccordions($request, $page_section)
    {
        foreach ($request->accordion_title as $key => $title) {
            $accordion = Accordion::find($request->accordion_id[$key]) ?? new Accordion(['parent' => $page_section->id]);
            $accordion->fill([
                'title' => $title,
                'description' => $request->accordion_description[$key],
                'order' => $request->accordion_order[$key],
            ])->save();

            if ($request->has('icon'.$key.'_id')) {
                $this->handleFileUpdate('accordion', $request, $accordion, 'icon'.$key, 0);
            }
        }
    }

    protected function updateOrCreateBoards($request, $page_section)
    {
        foreach ($request->board_name as $key => $name) {
            $board = Board::find($request->board_id[$key]) ?? new Board();
            $board->fill([
                'name' => $name,
                'position' => $request->board_position[$key],
                'order' => $request->board_order[$key],
            ])->save();

            if ($request->has('officer_image'.$key.'_id')) {
                $this->handleFileUpdate('board', $request, $board, 'officer_image'.$key, 0);
            }
        }

        $page_section['boards'] = Board::orderBy('order')->with('images')->get();
    }

    protected function updateImagesAndFiles($request, $page_section)
    {
        // if ($request->has('main_image')) {
            $this->updateImages('page_section', $request, $page_section, 'main_image');
        // }
        // if ($request->has('mobile_image')) {
            $this->updateImages('page_section', $request, $page_section, 'mobile_image');
        // }
        if ($request->has('pdf')) {
            $this->updateImages('page_section', $request, $page_section, 'pdf', 'file');
        }
    }

    protected function loadRelatedLogs(PageSection $page_section)
    {
        $page_section->load([
            'logs' => fn($q) => $q->orderBy('updated_at', 'desc')
                ->with(['user.images', 'user.userDetail'])
        ]);
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
}
