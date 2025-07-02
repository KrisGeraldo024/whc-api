<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\{
    Request,
    Response
};
use App\Http\Requests\ClientsRequest;
use App\Services\Client\ClientService;

class ClientController extends Controller
{
    protected $clientService;

    public function __construct (ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index (Request $request): Response
    {
        return $this->clientService->index($request);
    }

    public function store (ClientsRequest $request): Response
    {
        return $this->clientService->store($request);
    }
  
    public function show (Client $client, Request $request): Response
    {
        return $this->clientService->show($client, $request);
    }
    
    public function update (Client $client, ClientsRequest $request): Response
    {
        return $this->clientService->update($client, $request);
    }
    
    public function destroy (Client $client, Request $request): Response
    {
        return $this->clientService->destroy($client, $request);
    }

    public function getClients (Request $request): Response
    {
        return $this->clientService->getClients($request);
    }
}