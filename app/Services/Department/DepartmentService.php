<?php

namespace App\Services\Department;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Department;
use App\Traits\GlobalTrait;

class DepartmentService
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
        $records = Department::orderBy('name','asc')
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
     * FaqService store
     * @param  Request $request
     * @return Response
     */
    public function store ($request): Response
    {
        $record = Department::create([
            'name'    => $request->name,
        ]);
        $this->generateLog($request->user(), "added this department ({$record->id}).");
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
    public function show ($department, $request): Response
    {
        $this->generateLog($request->user(), "viewed this department ({$department->id}).");
        return response([
            'record' => $department
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($department, $request): Response
    {
        $department->update([
            'name'    => $request->name,
        ]);
        $this->generateLog($request->user(), "updated this department ({$department->id}).");
        return response([
            'record' => $department
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($department, $request): Response
    {
        $department->delete();
        $this->generateLog($request->user(), "deleted this department ({$department->id}).");
        return response([
            'record' => 'Department deleted'
        ]);
    }
}
