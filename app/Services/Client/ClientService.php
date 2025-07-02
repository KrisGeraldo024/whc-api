<?php

namespace App\Services\Client;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Validator
};
use App\Models\Client;
use App\Traits\GlobalTrait; 

class ClientService
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
        $records = Client::orderBy(isset($request->sortBy) ? $request->sortBy : 'date', isset($request->sortDirection) ? $request->sortDirection : 'desc')
        // ->orderBy('order')
        ->when(isset($request->keyword), function ($query) use ($request) {
            $query->where('name', 'LIKE', '%' . strtolower($request->keyword).'%');	
        })
        ->when($request->filled('all') , function ($query, $request) {
            return $query->get();
        }, function ($query) {
            return $query->orderBy('order')->paginate(20);
        });

        return response([
            'records' => $records
        ]);
    }
    /**
     * FaqService index
     * @param  Request $request
     * @return Response
     */
    public function getClients ($request): Response
    {
        if ($request->query('year')) {
            $records = Client::where('year', $request->query('year'))
                ->orderBy('order', $request->query('order') ? $request->query('order') : 'asc')
                ->with('images')
                ->paginate(8);
        } else {
            $records = Client::orderBy('order', ($request->query('order') ? $request->query('order') : 'asc'))
                ->with('images')
                ->paginate(8);
        }
        

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
        $record = Client::create([
            'name'    => $request->name,
            // 'description'    => $request->description,
            // 'date'    => $request->date,
            'order'    => $request->order ?? Client::count() + 1,
            'enabled'    => $request->enabled ?? 1,
        ]);

        if ($request->hasFile('main_image')) {
            $this->addImages('client', $request, $record, 'main_image');
        }
        $this->metatags($record, $request);
        $this->generateLog($request->user(), "Created", "Client", $record);

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
    public function show ($client, $request): Response
    {
        //$testimonial->load('images');
        $client->load('images', 'metadata');

        // $this->generateLog($request->user(), "viewed this client ({$client->id}).");

        return response([
            'record' => $client
        ]);
    }

    /**
     * FaqService update
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function update ($client, $request): Response
    {
        $client->update([
            'name'    => $request->name,
            // 'description'    => $request->description,
            // 'date'    => $request->date,
            'order'    => $request->order ?? $client->order,
            'enabled'    => $request->enabled ?? 1,
        ]);

        // if($request->has('main_image')) {
            $this->updateImages('client', $request, $client, 'main_image');
        // }
        $this->metatags($client, $request);

        $client->load(['images', 'metadata']);

        $this->generateLog($request->user(), "Changed", "Clients", $client);

        return response([
            'record' => $client
        ]);
    }

    /**
     * FaqService destroy
     * @param  Faq $faq
     * @param  Request $request
     * @return Response
     */
    public function destroy ($client, $request): Response
    {
        if ($client->order !== Client::max('order')) {
            Client::where('order', '>', $client->order)->decrement('order'); 
        }
        $this->generateLog($request->user(), "Deleted", "Clients", $client);
        $client->delete();
        $this->reassignOrderValues('Client');

        return response([
            'record' => 'Client deleted'
        ]);
    }
}
