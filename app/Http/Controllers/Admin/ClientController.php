<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Client;
use Carbon\Carbon;
use App\User;
use App\Http\Requests\Admin\CreateClientRequest;
use App\Http\Resources\ClientResource;
use App\Http\Requests\Admin\UpdateClientRequest;
use App\Http\Resources\UserDropdownResource;
use App\Http\Resources\FinancialReportResource;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index()
    {
        $clients = Client::withCount('tours')->get();

        if (request()->has('dropdown')) {
            return UserDropdownResource::collection($clients->sortBy('name'));
        }

        return ClientResource::collection($clients);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateClientRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateClientRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();
        if (empty($user)) {
            $data['user_type'] = 1;
            if ($data['subscribe_override'] == ''){
                $data['subscribe_override'] = 0;
            }
            $data['email_confirmed_at'] = Carbon::now();
            $data['password'] = bcrypt($data['password']);
            if ($client = Client::create($data)) {
                return $this->success("{$client->name} was added successfully.", new ClientResource($client));
            }
    
            return $this->fail();
        } else {
            if ($data['subscribe_override'] == ''){
                $user->subscribe_override = 0;
            }
            $user->user_type = 1;
            $user->email_confirmed_at = Carbon::now();
            $user->password = bcrypt($data['password']);
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->tour_limit = $data['tour_limit'];
            $user->save();

            $client = new Client;
            $client->id = $user->id;
            if (!empty($data['company_name'])) {
                $client->company_name =  $data['company_name'];
            }
            $client->save();
            return $this->success("{$client->name} was added successfully.", new ClientResource($client));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Client $client
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function show(Client $client)
    {
        return new ClientResource($client);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateClientRequest $request
     * @param \App\Client $client
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        if ($client->update($request->validated())) {
            $client = $client->fresh();
            return $this->success("{$client->name} was updated successfully.", new ClientResource($client));
        }

        return $this->fail();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Client $client
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Client $client)
    {
        if ($client->forceDelete()) {
            return $this->success("{$client->name} was archived successfully.");
        }

        return $this->fail();
    }

    /**
     * Get financial report for all clients over a given period
     *
     * @param Request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function getFinancialReport() {
        $clients = Client::withCount('tours')->get();

        return FinancialReportResource::collection($clients);
    }
}
