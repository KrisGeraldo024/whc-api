<?php

namespace App\Services\Inquiry;

use Illuminate\Support\Facades\{
    Http,
    Validator
};
use Illuminate\Http\Response;
use App\Models\{
    Inquiry
};
use App\Traits\GlobalTrait;
use App\Jobs\SendInquiryMail;
use App\Jobs\SendApplicationMail;
use App\Jobs\SendBrokerApplicationMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class InquiryService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * InquiryService index
     * @param  Request  $request
     * @return Response
     */
    public function index($request): Response
    {
        $startDate = $request->startDate 
        ? Carbon::parse($request->startDate)->startOfDay()
        : (Inquiry::min('created_at') ? Carbon::parse(Inquiry::min('created_at'))->startOfDay() : Carbon::create(2000, 1, 1));
    
        $endDate = $request->endDate 
        ? Carbon::parse($request->endDate)->endOfDay() 
        : Carbon::now()->endOfDay();
        $inquiries = Inquiry::orderBy('created_at', $request->sortDirection ?? 'desc')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($request->filled('keyword'), function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('first_name', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('inquiry_number', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('contact_number', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('email_address', 'LIKE', '%' . $request->keyword . '%');
                });
            })
            ->when($request->filled('property_type'), function ($q) use ($request) {
                $q->where('property_type', $request->property_type);
            })
            ->when($request->filled('community'), function ($q) use ($request) {
                $q->where('property_name', $request->community);
            })
            ->when($request->filled('platform'), function ($q) use ($request) {
                $q->where('platform', $request->platform);
            })
            ->when($request->filled('type'), function ($q) use ($request) {
                $q->where('type', $request->type);
            })
            ->when($request->filled('position'), function ($q) use ($request) {
                $q->where('job_title', $request->position);
            })
            ->when($request->filled('partnership'), function ($q) use ($request) {
                $q->where('partnership', $request->partnership);
            })
            ->when($request->filled('inquiry_type'), function ($q) use ($request) {
                $q->where('inquiry_type', $request->inquiry_type);
            })
            ->when($request->filled('all'), function ($query) {
                return $query->get();
            }, function ($query) {
                return $query->paginate(20);
            });
        return response([
            'records' => $inquiries
        ]);
    }

    /**
     * InquiryController update
     * @param  Inquiry  $inquiry
     * @param  Request  $request
     * @return Response
     */
    public function update($inquiry, $request): Response
    {
        $inquiry->update([
            'status' => 0
        ]);

        return response([
            'record' => $inquiry
        ]);
    }

    /**
     * InquiryService inquiry
     * @param  Request  $request
     * @return Response
     */

    public function application($request): Response
    {

        // $formData = collect([
        //     'first_name'     => $request->firstName,
        //     'last_name'      => $request->lastName,
        //     'email_address'  => $request->email_address,
        //     'contact_number' => $request->contactNumber,
        //     'message'        => $request->message,
        //     'job_title'      => $request->jobTitle,
        //     'message'        => $request->message,
        //     'platform'       => $request->platform,
        //     'type'           => $request->type
        // ]);

        $formData = Inquiry::create([
            'inquiry_number' => $this->generateUniqueCode(),
            'inquiry_type'   => $request->type === 'Careers' ? 'Corporate Careers' : 'In House Sales Group',
            'type'           => $request->type,
            'first_name'     => $request->firstName,
            'last_name'      => $request->lastName,
            'email_address'  => $request->email_address,
            'contact_number' => $request->contactNumber,
            'message'        => $request->message,
            'job_title'      => $request->jobTitle,
            'message'        => $request->message,
            'platform'       => $request->platform,
        ]);

        // Append resume and transcript data to the collection

        $disk = 'public';
        $timestamp = now()->timestamp;
        if ($request->hasFile('resume')) {
            try {
                $resumeFilename = $request->file('resume')->getClientOriginalName();
                $lastName = $formData->last_name;
                $resumeFilename = $lastName . '_resume_' . $timestamp . '.' . $request->file('resume')->extension();
                $resumePath = 'resumes/' . $resumeFilename;
                Storage::disk($disk)->put($resumePath, file_get_contents($request->file('resume')->path()));
                $formData->update(['resume_path' => $resumePath]);
            } catch (\Exception $e) {
                Log::error('Error storing resume file: ' . $e->getMessage());
            }
        }

        // Handling transcript file
        if ($request->hasFile('transcript')) {
            try {
                $transcriptFilename = $request->file('transcript')->getClientOriginalName();
                $lastName = $formData->get('last_name');
                $transcriptFilename = $lastName . '_transcript_' . $timestamp . '.' . $request->file('transcript')->extension();
                $transcriptPath = 'transcripts/' . $transcriptFilename;
                Storage::disk($disk)->put($transcriptPath, file_get_contents($request->file('transcript')->path()));
                $formData->put('transcript_path', $transcriptPath);
            } catch (\Exception $e) {
                Log::error('Error storing transcript file: ' . $e->getMessage());
            }
        }
        SendApplicationMail::dispatch($formData);

        return response([
            'record' => $formData
        ]);
    }

    public function inquiry($request): Response
    {
        // $inquiry = (object) [
        //     'first_name'     => $request->firstName,
        //     'last_name'      => $request->lastName,
        //     'email_address'  => $request->email_address,
        //     'contact_number' => $request->contactNumber,
        //     'message'        => $request->message,
        //     'type'           => $request->type, 
        //     'user_ip'        => $request->ip(),
        //     'platform'       => $request->priority,
        //     'subject'        => $request->type,
        //     'created_at'     => Carbon::now(),
        //     'contact_type'   => 'inquiry'
        // ];


        $inquiry = Inquiry::create([
            'inquiry_number' => $this->generateUniqueCode(),
            'inquiry_type'   => 'Contact Us',
            'first_name'     => $request->firstName,
            'last_name'      => $request->lastName,
            'email_address'  => $request->email_address,
            'contact_number' => $request->contactNumber,
            'message'        => $request->message,
            'type'           => $request->type,
            'platform'       => $request->priority,
            'contact_type'   => 'inquiry',
            'subject'        => $request->type
        ]);

        try {
            SendInquiryMail::dispatch($inquiry);

            return response([
                'message' => 'success',
                'record' => $inquiry
            ], 200);
        } catch (\Exception $e) {
            return response([
                'message' => $e
            ], 500);
        }
    }

    public function salesInquiry($request): Response
    {
        $inquiry = [
            'first_name'     => $request->firstName,
            'last_name'      => $request->lastName,
            'email'          => $request->email,
            'contact_number' => $request->contactNumber,
            'message'        => $request->message,
            'type_of_inquiry' => $request->type ?? 'Sales Inquiry',
            'property_type'  => $request->property,
            'property_name'  => $request->community,
            'unit_type'      => $request->unit,
            'platform'       => $request->platform,
            'contact_type'   => "lead"
        ];

        $email_data = Inquiry::create([
            'inquiry_number' => $this->generateUniqueCode(),
            'inquiry_type'   => 'Get a Quote',
            'first_name'     => $request->firstName,
            'last_name'      => $request->lastName,
            'email_address'  => $request->email,
            'contact_number' => $request->contactNumber,
            'message'        => $request->message,
            'type'           => $request->type ?? 'Get Quote',
            'property_type'  => $request->property,
            'property_name'  => $request->community,
            'unit_type'      => $request->unit,
            'platform'       => $request->platform,
            'contact_type'   => "lead",
            'subject'        => $request->type ?? 'Sales Inquiry',
        ]);

        // $email_data = (object) [
        //     'first_name'     => $request->firstName,
        //     'last_name'      => $request->lastName,
        //     'email_address'  => $request->email,
        //     'contact_number' => $request->contactNumber,
        //     'message'        => $request->message,
        //     'type'           => $request->type ?? 'Get Quote', 
        //     'user_ip'        => $request->ip(),
        //     'property_type'  => $request->property,
        //     'property_name'  => $request->community,
        //     'unit_type'      => $request->unit,
        //     'platform'       => $request->platform,
        //     'contact_type'   => "lead",
        //     'subject'        => $request->type ?? 'Sales Inquiry',
        //     'created_at'     => Carbon::now()
        // ];

        try {
            $response = Http::post(config('app.crm_webhook'), $inquiry);

            if ($response->json()['status'] === "success") {
                SendInquiryMail::dispatch($email_data);

                return response([
                    'message' => "success",
                    'record' => $email_data
                ], 200);
            } else {
                return response([
                    $response
                ], 500);
            }
        } catch (\Exception $e) {
            return response([
                'message' => $e
            ], 500);
        }
    }

    public function brokerForm($request): Response
    {
        // $formData = collect([
        //     'first_name'     => $request->firstName,
        //     'last_name'      => $request->lastName,
        //     'email_address'  => $request->email,
        //     'contact_number' => $request->contactNumber,
        //     'message'        => $request->message,
        //     'job_title'      => '',
        //     'message'        => $request->message,
        //     // 'platform'       => $request->platform,
        //     'type'           => $request->type ?? 'Business Partner',
        //     'subject'        => $request->type ?? 'Broker Accreditation',
        //     'partnership'    => $request->partnership,
        //     'location'       => $request->location,
        //     'company'        => $request->company,
        //     'created_at'     => Carbon::now()
        // ]);

        $formData = Inquiry::create([
            'inquiry_number' => $this->generateUniqueCode(),
            'inquiry_type'   => 'Business Partner Network',
            'first_name'     => $request->firstName,
            'last_name'      => $request->lastName,
            'email_address'  => $request->email,
            'contact_number' => $request->contactNumber,
            'message'        => $request->message,
            'job_title'      => '',
            'message'        => $request->message,
            'type'           => $request->type ?? 'Business Partner',
            'subject'        => $request->type ?? 'Broker Accreditation',
            'partnership'    => $request->partnership,
            'location'       => $request->location,
            'company'        => $request->company,
        ]);

        SendBrokerApplicationMail::dispatch($formData);

        return response([
            'record' => $formData
        ]);
    }

    public function exportInquiries($request): Response
    {
        $startDate = $request->startDate 
        ? Carbon::parse($request->startDate)->startOfDay()
        : (Inquiry::min('created_at') ? Carbon::parse(Inquiry::min('created_at'))->startOfDay() : Carbon::create(2000, 1, 1));
    
        $endDate = $request->endDate 
        ? Carbon::parse($request->endDate)->endOfDay() 
        : Carbon::now()->endOfDay();
        $inquiries = Inquiry::orderBy('created_at', $request->sortDirection ?? 'desc')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($request->filled('keyword'), function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('first_name', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('inquiry_number', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('contact_number', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('email_address', 'LIKE', '%' . $request->keyword . '%');
                });
            })
            ->when($request->filled('property_type'), function ($q) use ($request) {
                $q->where('property_type', $request->property_type);
            })
            ->when($request->filled('community'), function ($q) use ($request) {
                $q->where('property_name', $request->community);
            })
            ->when($request->filled('platform'), function ($q) use ($request) {
                $q->where('platform', $request->platform);
            })
            ->when($request->filled('type'), function ($q) use ($request) {
                $q->where('type', $request->type);
            })
            ->when($request->filled('position'), function ($q) use ($request) {
                $q->where('job_title', $request->position);
            })
            ->when($request->filled('partnership'), function ($q) use ($request) {
                $q->where('partnership', $request->partnership);
            })
            ->when($request->filled('inquiry_type'), function ($q) use ($request) {
                $q->where('inquiry_type', $request->inquiry_type);
            });

        // Fetch all records without pagination
        $inquiries = $inquiries->get();

        return response([
            'records' => $inquiries
        ]);
    }
}
