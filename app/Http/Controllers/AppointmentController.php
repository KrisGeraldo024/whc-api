<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use Illuminate\Support\Facades\Http;
use App\Models\Appointment;
use App\Services\Appointment\AppointmentService;

class AppointmentController extends Controller
{
    /**
     * @var AppointmentService
     */
    protected $appointmentService;

    /**
     * AppointmentController constructor
     * @param AppointmentService $appointmentService
     */
    public function __construct (AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    /**
     * AppointmentController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->appointmentService->index($request);
    }

    /**
     * AppointmentController show
     * @param  Appointment $appointment
     * @param  Request $request
     * @return Response
     */
    public function show (Appointment $appointment, Request $request): Response
    {
        return $this->appointmentService->show($appointment, $request);
    }

    /**
     * AppointmentController sendAppointment
     * @param  Request $request
     * @return Response
     */
    public function sendAppointment (Request $request): Response
    {
        return $this->appointmentService->sendAppointment($request);
    }
}
