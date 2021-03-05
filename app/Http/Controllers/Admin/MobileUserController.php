<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\MobileUser;
use App\User;
use Carbon\Carbon;
use App\Http\Requests\Admin\CreateMobileUserRequest;
use App\Http\Resources\MobileUserResource;
use App\Http\Requests\Admin\UpdateMobileUserRequest;

class MobileUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index()
    {
        return MobileUserResource::collection(MobileUser::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateMobileUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateMobileUserRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (empty($user)) {
            if ($data['subscribe_override'] == ''){
                $data['subscribe_override'] = 0;
            }
            $data['email_confirmed_at'] = Carbon::now();
            $data['password'] = bcrypt($data['password']);
            $data['user_type'] = 2;
            if ($user = MobileUser::create($data)) {
                return $this->success("{$user->name} was added successfully.", new MobileUserResource($user));
            }
    
            return $this->fail();
        } else {
            if ($data['subscribe_override'] == ''){
                $user->subscribe_override = 0;
            }
            $user->user_type = 2;
            $user->email_confirmed_at = Carbon::now();
            $user->password = bcrypt($data['password']);
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->save();

            $mobile_user = new MobileUser;
            $mobile_user->id = $user->id;
            $mobile_user->save();
            return $this->success("{$mobile_user->name} was added successfully.", new MobileUserResource($mobile_user));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\MobileUser $user
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function show(MobileUser $user)
    {
        return new MobileUserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateMobileUserRequest $request
     * @param \App\MobileUser $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMobileUserRequest $request, MobileUser $user)
    {
        if ($user->update($request->validated())) {
            $user = $user->fresh();
            return $this->success("{$user->name} was updated successfully.", new MobileUserResource($user));
        }

        return $this->fail();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\MobileUser $user
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(MobileUser $mobile_user)
    {
        if ($mobile_user->forceDelete()) {
            return $this->success("{$mobile_user->name} was archived successfully.");
        }

        return $this->fail();
    }
}
