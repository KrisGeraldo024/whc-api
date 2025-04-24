<?php

namespace App\Services\Extra;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Carbon\Carbon;
use App\Models\{
    Appointment,
    Inquiry,
    Subscribe,
    Image, 
    Promo
};
use App\Traits\GlobalTrait;
use \stdClass;
use GuzzleHttp\Client;

class ExtraService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;


    /**
     * ExtraService dashboard
     * @return Response
     */
    public function dashboard (): Response
    {
        $appointments = Appointment::get()->count();
        $contact_inquiries = Inquiry::where('type', 'contact-us')->get()->count();
        $branch_inquiries = Inquiry::where('type', 'branch-inquiry')->get()->count();
        $products_inquiries = Inquiry::where('type', 'product-inquiry')->get()->count();
        $payment_inquiries = Inquiry::where('type', 'flexible-payment-plan-inquiry')->get()->count();
        $online_hearings = Inquiry::where('type', 'online-hearing-aid-inquiry')->get()->count();
        $promos_inquiries = Inquiry::where('type', 'promo-inquiry')->get()->count();
        $subscribes = Subscribe::get()->count();
        $promos = Promo::where('enabled', 1)->get()->count();
        $records = [
            (object) [
                'title' => 'Appointments',
                'link'  => '/leads/appointments',
                'count' => $appointments
            ],
            (object) [
                'title' => 'Subscribes',
                'link'  => '/leads/subscribes',
                'count' => $subscribes
            ],
            (object) [
                'title' => 'Online Hearing Inquiries',
                'link'  => '/leads/online-hearing',
                'count' => $online_hearings
            ],
            (object) [
                'title' => 'Contact Us Inquiries',
                'link'  => '/inquiries/contact-us',
                'count' => $contact_inquiries
            ],
            (object) [
                'title' => 'Branch Inquiries',
                'link'  => '/inquiries/branches',
                'count' => $branch_inquiries
            ],
            (object) [
                'title' => 'Product Inquiries',
                'link'  => '/inquiries/products',
                'count' => $products_inquiries
            ],
            (object) [
                'title' => 'Flexible Payment Inquiries',
                'link'  => '/inquiries/payment-plan',
                'count' => $payment_inquiries
            ],
            (object) [
                'title' => 'Promo Inquiries',
                'link'  => '/inquiries/promos',
                'count' => $promos_inquiries
            ],
            (object) [
                'title' => 'Promos',
                'link'  => '/promos',
                'count' => $promos
            ]
        ];

        return response([
            'records' => $records
        ]);
    }

    /**
     * ExtraService verifyCaptcha
     * @param  Request $request
     * @return Object
     */
    public function verifyCaptcha ($request): Object
    {
        $captcha = $request->response;

        $client = new Client([
            'base_uri' => 'https://google.com/recaptcha/api/',
            'timeout' => 5.0
        ]);

        $response = $client->request('POST', 'siteverify', [
            'query' => [
                'secret' => "6LfKypwiAAAAANnzdHoMpbK0kVf0aaK3O-k7Fk9H",
                'response' => $captcha
            ]
        ]);

        return $response->getBody();
    }


    /**
     * ExtraService deleteImage
     * @param  Image $image
     * @param  Request $request
     * @return Response
     */
    public function deleteImage ($image, $request): Response
    {
        $disk = 'public';
        $path = explode('uploads', $image->path);
        $path_resized = explode('uploads', $image->path_resized);
        Storage::disk($disk)->delete("uploads$path[1]");
        Storage::disk($disk)->delete("uploads$path_resized[1]");

        $image->forceDelete();

        return response([
            'record' => $path
        ]);
    }

    /**
     * ExtraService copyAllImages
     * @param  Request $request
     * @param  string $type
     */
    public function copyAllImages ($request, $type)
    {
		$images = Image::get();

		$path = [];

		foreach ($images as $key => $value) {
			array_push($path, $this->copyImage($value->id, $value->$type, $value->path, 'public'));
		}

        return $path;

        // return response([
        //     'records' => $path
        // ]);
    }

    public function deleteItem ($request) {
        $_ModelName = '\App\Models\\' . $request->model;
        $record = $_ModelName::find($request->id);
        $record_images = Image::where('parent_id', $record->id)->get();

        if ($record->delete()) {
            foreach($record_images as $image) {
                $this->deleteImage($image, $request);
            }
            return response([
                'records' => "record succesfully deleted"
            ]);
        } else {
            return response([
                'records' => "record deletion failed"
            ]);
        }
    }

    public function reOrder($request): Response
    {
        // Build the model's namespace dynamically
        $modelClass = '\App\Models\\' . $request->model;

        // Validate if the model class exists
        if (!class_exists($modelClass)) {
            return response([
                'success' => false,
                'message' => 'Model not found.'
            ], 404);
        }

        // Validate the data structure
        $data = $request->data;
        if (!is_array($data)) {
            return response([
                'success' => false,
                'message' => 'Invalid data format.'
            ], 400);
        }

        // Iterate over the data and update the records
        foreach ($data as $item) {
            // Validate required fields in each item
            if (!isset($item['id'], $item['order'])) {
                return response([
                    'success' => false,
                    'message' => 'Invalid item structure.',
                    'item' => $item,
                ], 400);
            }

            // Find the record by ID and update the order
            $record = $modelClass::find($item['id']);
            if ($record) {
                $record->update(['order' => $item['order']]);
            } else {
                return response([
                    'success' => false,
                    'message' => 'Record not found for ID: ' . $item['id'],
                ], 404);
            }
        }

        return response([
            'success' => true,
            'message' => 'Records reordered successfully.',
        ]);
    }
}