<?php

namespace App\Services\HearingAid;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\{
    Video,
    HearingAid,
    HearingAidCategory
};
use App\Traits\GlobalTrait;

class HearingAidService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * HearingAidService index
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {
        $records = HearingAid::orderBy('order')
        ->with(['hearingAidCategory', 'formDetails'])
        ->when($request->filled('id'), function ($query) use ($request) {
            $query->where('id', '!=', $request->id);
        })
        ->when($request->filled('category'), function ($query) use ($request) {
            $query->whereHearingAidCategoryId($request->category);
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
     * HearingAidService store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {
        $validator = Validator::make($request->all(), [
            'hearing_aid_category_id' => 'required',
            'title'                   => 'required',
            'summary'                 => 'required',
            'about_title'             => 'required',
            'about_description'       => 'required',
            'uvp_title'               => 'required',
            'videos'                  => 'sometimes',
            'similars'                => 'sometimes',
            'order'                   => 'required|integer',
            'enabled'                 => 'required',
            'featured'                => 'required',
            'best_seller'             => 'required',
            'pay_later'               => 'required',
            'main_image'              => 'required',
            'gallery'                 => 'required',
            'main_image.*'            => 'required|mimes:jpeg,png,jpg,webp|max:3000',
            'gallery.*'               => 'required|mimes:jpeg,png,jpg,webp',
            'bluetooth'               => 'required|integer',
            'rechargeable'            => 'required|integer',
            'fit'                     => 'required',
            'battery'                 => 'required',
            'signia'                  => 'required|integer',
            'button_label'            => 'sometimes'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $category = HearingAidCategory::find($request->hearing_aid_category_id);
        $keyword = sprintf('%s,%s,%s,%s,%s,%s',
            $request->title,
            str_slug($request->title),
            str_slug($request->title, '_'),
            $category->title,
            str_slug($category->title),
            str_slug($category->title, '_')
        );

        $record = HearingAid::create([
            'hearing_aid_category_id' => $request->hearing_aid_category_id,
            'title'                   => $request->title,
            'summary'                 => $request->summary,
            'about_title'             => $request->about_title,
            'about_description'       => $request->about_description,
            'uvp_title'               => $request->uvp_title,
            'videos'                  => $request->videos,
            'similars'                => $request->similars,
            'keyword'                 => $keyword,
            'order'                   => $request->order,
            'enabled'                 => $request->enabled,
            'featured'                => $request->featured,
            'best_seller'             => $request->best_seller,
            'pay_later'               => $request->pay_later,
            'slug'                    => $this->slugify($request->title, 'HearingAid'),
            'bluetooth'               => $request->bluetooth,
            'rechargeable'            => $request->rechargeable,
            'fit'                     => $request->fit,
            'battery'                 => $request->battery,
            'signia'                  => $request->signia,
            'button_label'            => $request->button_label
        ]);

        if ($request->hasFile('main_image')) {
            $this->addImages('hearing_aid', $request, $record, 'main_image');
        }
        if ($request->hasFile('gallery')) {
            $this->addImages('hearing_aid', $request, $record, 'gallery');
        }

        $this->metatags($record, $request);
        $this->generateLog($request->user(), "added this hearing aid ({$record->id}).");

        return response([
            'record' => $record
        ]);
    }

    /**
     * HearingAidService show
     * @param  HearingAid $hearing_aid
     * @param  Request $request
     * @return Response
     */
    public function show ($hearing_aid, $request): Response
    {
        $hearing_aid->load('images', 'metadata');
        $hearing_aid->similars = ($hearing_aid->similars) ? HearingAid::select('id', 'title')
                            ->whereIn('id', json_decode($hearing_aid->similars))
                            ->get() : [];
        $hearing_aid->videos = ($hearing_aid->videos) ? Video::select('id', 'title')
                            ->whereIn('id', json_decode($hearing_aid->videos))
                            ->get() : [];
        $this->generateLog($request->user(), "viewed this hearing aid ({$hearing_aid->id}).");

        return response([
            'record' => $hearing_aid
        ]);
    }

    /**
     * HearingAidService update
     * @param  HearingAid $hearing_aid
     * @param  Request $request
     * @return Response
     */
    public function update ($hearing_aid, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'hearing_aid_category_id' => 'required',
            'title'                   => 'required',
            'summary'                 => 'required',
            'about_title'             => 'required',
            'about_description'       => 'required',
            'uvp_title'               => 'required',
            'videos'                  => 'sometimes',
            'similars'                => 'sometimes',
            'order'                   => 'required|integer',
            'enabled'                 => 'required',
            'featured'                => 'required',
            'best_seller'             => 'required',
            'pay_later'               => 'required',
            'main_image'              => 'sometimes',
            'gallery'                 => 'sometimes',
            'main_image.*'            => 'sometimes|mimes:jpeg,png,jpg,webp|max:3000',
            'gallery.*'               => 'sometimes|mimes:jpeg,png,jpg,webp',
            'bluetooth'               => 'required|integer',
            'rechargeable'            => 'required|integer',
            'fit'                     => 'required',
            'battery'                 => 'required',
            'signia'                  => 'required|integer',
            'button_label'            => 'sometimes'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }
        
        $category = HearingAidCategory::find($request->hearing_aid_category_id);
        $keyword = sprintf('%s,%s,%s,%s,%s,%s',
            $request->title,
            str_slug($request->title),
            str_slug($request->title, '_'),
            $category->title,
            str_slug($category->title),
            str_slug($category->title, '_')
        );

        $hearing_aid->update([
            'hearing_aid_category_id' => $request->hearing_aid_category_id,
            'title'                   => $request->title,
            'summary'                 => $request->summary,
            'about_title'             => $request->about_title,
            'about_description'       => $request->about_description,
            'uvp_title'               => $request->uvp_title,
            'videos'                  => $request->videos,
            'similars'                => $request->similars,
            'keyword'                 => $keyword,
            'order'                   => $request->order,
            'enabled'                 => $request->enabled,
            'featured'                => $request->featured,
            'best_seller'             => $request->best_seller,
            'pay_later'               => $request->pay_later,
            'slug'                    => $this->slugify($request->title, 'HearingAid', $hearing_aid->id),
            'bluetooth'               => $request->bluetooth,
            'rechargeable'            => $request->rechargeable,
            'fit'                     => $request->fit,
            'battery'                 => $request->battery,
            'signia'                  => $request->signia,
            'button_label'            => $request->button_label
        ]);        

        $this->updateImages('hearing_aid', $request, $hearing_aid, 'main_image');
        $this->updateImages('hearing_aid', $request, $hearing_aid, 'gallery');

        $this->metatags($hearing_aid, $request);
        $this->generateLog($request->user(), "updated this hearing aid ({$hearing_aid->id}).");

        return response([
            'record' => $hearing_aid
        ]);
    }

    /**
     * HearingAidService destroy
     * @param  HearingAid $hearing_aid
     * @param  Request $request
     * @return Response
     */
    public function destroy ($hearing_aid, $request): Response
    {
        $hearing_aid->delete();
        $this->generateLog($request->user(), "deleted this hearing aid ({$hearing_aid->id}).");

        return response([
            'record' => 'Hearing aid deleted'
        ]);
    }
}
