<?php

namespace App\Services\Appointment;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Hash,
    Validator
};
use App\Models\{
    Appointment,
    Branch,
    Service
};
use App\Jobs\SendAppointmentMail;
use App\Traits\GlobalTrait;

class AppointmentService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * AppointmentService index
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {

        $records = Appointment::select(
            'id',
            'type',
            'service_id',
            'branch_id',
            'is_other_service',
            'appointment_no',
            'full_name',
            'email',
            'created_at'
        )
        ->when(isset($request->keyword), function ($q) use ($request) {
            $q->where('appointment_no', 'LIKE', '%' . strtolower($request->keyword) . '%')
            ->orWhere('full_name', 'LIKE', '%' . strtolower($request->keyword) . '%');
        })
        ->with([
            'service',
            'branch'
        ])
        ->when(isset($request->branch_id), function ($q) use ($request) {
            $q->where('branch_id', $request->branch_id);
        })
        ->when(isset($request->service_id), function ($q) use ($request) {
            $q->where('service_id', $request->service_id);
        })
        ->when(isset($request->type), function ($q) use ($request) {
            $q->where('type', $request->type);
        })
        ->when(isset($request->sort_by), function ($q) use ($request) {
            if ($request->order_type == 'desc') {
                $q->orderByDesc($request->sort_by);
            }
            else {
                $q->orderBy($request->sort_by);
            }
        })
        ->when(isset($request->start), function ($q) use ($request) {
            $q->where('created_at', '>=', $request->start);
            $q->where('created_at', '<=', $request->end . '23:59:59');
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
     * AppointmentService show
     * @param  Appointment $appointment
     * @param  Request $request
     * @return Response
     */
    public function show ($appointment, $request): Response
    {
        $this->generateLog($request->user(), "viewed this appointment ({$appointment->id}).");

        $appointment->load([
            'service',
            'branch',
        ]);

        $appointment->background_question = json_decode($appointment->background_question);
        $appointment->checkup_question = json_decode($appointment->checkup_question);

        return response([
            'record' => $appointment
        ]);
    }

    /**
     * AppointmentService sendAppointment
     * @param  Request $request
     * @return Response
     */
    public function sendAppointment ($request): Response
    {
        $validator = Validator::make($request->all(), [
            'type'             => 'required',
            'service_id'       => 'sometimes',
            'branch_id'        => $request->type == 'home-visit' ? 'sometimes' : 'required',
            'is_other_service' => 'required',
            'first_name'       => 'required',
            'last_name'       => 'required',
            'birthday'         => 'required|date_format:Y-m-d',
            'contact_number'   => 'required',
            'telephone_number' => 'sometimes',
            'email' => 'required',
            'house_no' => $request->type == 'home-visit' ? 'required' : 'sometimes',
            'street' => $request->type == 'home-visit' ? 'required' : 'sometimes',
            'zip_code' => $request->type == 'home-visit' ? 'required' : 'sometimes',
            'region' => $request->type == 'home-visit' ? 'required' : 'sometimes',
            'city' => $request->type == 'home-visit' ? 'required' : 'sometimes',
            'barangay' => $request->type == 'home-visit' ? 'required' : 'sometimes',
            'background_question' => 'required',
            'background_question_answer' => 'required',
            'checkup_question' => 'required',
            'checkup_question_answer' => 'required',
            'others_message' => $request->is_other_service ? 'required' : 'sometimes',
            'companion_details' => 'sometimes'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $branch = null;
        $service  = null;

        if ($request->type == 'in-store-visit') {
            $branch = Branch::find($request->branch_id);
        }

        if (!$request->is_other_service) {
            $service = Service::find($request->service_id);
        }

        $appointment_no = str_pad( date('Y-m', strtotime('now')) . '-' .strtoupper(str_random(6)), 5 , 0, STR_PAD_LEFT);
        $full_name = sprintf('%s %s',
            $request->first_name,
            $request->last_name
        );

        $background_question = [];
        $checkup_question = [];

        if (isset($request->background_question)) {
            foreach ($request->background_question as $key => $value) {
                array_push($background_question, (object) [
                    'question' => $value,
                    'answer' => isset($request->background_question_answer[$key]) ? $request->background_question_answer[$key] : null
                ]);
            }
        }   
        if (isset($request->checkup_question)) {
            foreach ($request->checkup_question as $key => $value) {
                array_push($checkup_question, (object) [
                    'question' => $value,
                    'answer' => isset($request->checkup_question_answer[$key]) ? $request->checkup_question_answer[$key] : null
                ]);
            }
        }   

        $record = Appointment::create([
            'type' => $request->type,
            'service_id' => $service ? $service->id : null,
            'branch_id' => $branch ? $branch->id : null,
            'is_other_service' => $request->is_other_service,
            'appointment_no' => $appointment_no,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'full_name' => $full_name,
            'birthday' => $request->birthday,
            'contact_number' => $request->contact_number,
            'telephone_number' => $request->telephone_number,
            'email' => $request->email,
            'house_no' => $request->type == 'home-visit' ? $request->house_no : null,
            'street' => $request->type == 'home-visit' ? $request->street : null,
            'zip_code' => $request->type == 'home-visit' ? $request->zip_code : null,
            'region' => $request->type == 'home-visit' ? $request->region : null,
            'city' => $request->type == 'home-visit' ? $request->city : null,
            'barangay' => $request->type == 'home-visit' ? $request->barangay : null,
            'background_question' => json_encode($background_question),
            'checkup_question' => json_encode($checkup_question),
            'others_message' => $request->is_other_service ? $request->others_message : null,
            'companion_details' => $request->companion_details ? json_encode($request->companion_details) : null
        ]);

        $record->load(['branch', 'service']);

        $record->background_question = json_decode($record->background_question);
        $record->checkup_question = json_decode($record->checkup_question);

        if ($record->companion_details) $record->companion_details = json_decode($record->companion_details);

        // Mail::to($request->email)->send(new AppointmentMail($record));
        // Mail::to($request->email)->send(new AdminAppointmentMail($record));
        SendAppointmentMail::dispatch($record);

        return response([
            'record' => $record
        ]);
    }
}
