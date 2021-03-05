<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Admin;
use App\User;
use App\Http\Requests\Admin\CreateAdminRequest;
use App\Http\Resources\AdminResource;
use App\Http\Requests\Admin\UpdateAdminRequest;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index()
    {
        return AdminResource::collection(Admin::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateAdminRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateAdminRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (empty($user)) {
            $data['tour_limit'] = 999;
            $data['user_type'] = 1;
            $data['password'] = bcrypt($data['password']);

            $data['email_confirmed_at'] = Carbon::now();
            $data['pricing_plan_id'] = 6;
            $data['show_popup'] = 0;
            if ($admin = Admin::create($data)) {
                return $this->success("{$admin->name} was added successfully.", new AdminResource($admin));
            }

            return $this->fail();
        } else {
            $user->user_type = 1;
            $user->tour_limit = 999;
            $user->email_confirmed_at = Carbon::now();
            $user->password = bcrypt($data['password']);
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->pricing_plan_id = 6;
            $user->show_popup = 0;
            $user->save();

            $admin = new Admin;
            $admin->id = $user->id;
            $admin->save();
            return $this->success("{$admin->name} was added successfully.", new AdminResource($admin));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Admin $admin
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function show(Admin $admin)
    {
        return new AdminResource($admin);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAdminRequest $request
     * @param \App\Admin $admin
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        if ($admin->update($request->validated())) {
            $admin = $admin->fresh();
            return $this->success("{$admin->name} was updated successfully.", new AdminResource($admin));
        }

        return $this->fail();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Admin $admin
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Admin $admin)
    {
        if ($admin->role == 'superadmin') {
            return $this->fail(403, 'You cannot delete the Root user.');
        }

        if ($admin->forceDelete()) {
            return $this->success("{$admin->name} was archived successfully.");
        }

        return $this->fail();
    }
}
