<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ChangePasswordRequest;
use Auth;
use App\User;
use App\PricingPlan;

class AccountController extends Controller
{
    /**
     * Get the user's profile information.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return response()->json(auth()->user());
    }

    /**
     * Update the user's profile information.
     *
     * @param UpdateProfileRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProfileRequest $request)
    {
        if (auth()->user()->type->update($request->validated())) {
            return $this->success('Your profile has been updated.', auth()->user()->fresh()->type);
        }

        return $this->error(500, 'An unexpected error occurred while saving your profile information.  Please try again.');
    }

    /**
     * Update the user's profile information.
     *
     * @param ChangePasswordRequest $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $data = $request->validated();

        $credentials = [
            'email' => auth()->user()->email,
            'password' => $data['old_password'],
        ];

        if (! Auth::attempt($credentials)) {
            return $this->fail(401, 'Your current password was invalid.');
        }

        if (auth()->user()->update([
            'password' => bcrypt($data['password']),
        ])) {
            return $this->success('Your password was changed.');
        }

        return $this->error(500, 'An unexpected error occurred while trying to change your password.  Please try again.');
    }

    /**
     * Update the user's subscription information.
     *
     * @return \Illuminate\Http\Response
     */
    public function subscribe()
    {
        if (empty(request()->stripe_cust_id)) {
            return $this->fail(401, 'stripe_cust_id must be a string');
        }
        if (empty(request()->plan_id)) {
            return $this->fail(401, 'plan_id must be a string');
        }
        
        $user = auth()->user();
        $user->stripe_cust_id = request()->stripe_cust_id;
        $user->pricing_plan_id = PricingPlan::where('plan_id', request()->plan_id)->first()->id;
        $user->save();
        return $this->success('The subscription is added successfully!');
    }
    
    /**
     * Update the user's charge status information. succeeded will be updated by stripe.charge.succeeded or failed.
     *
     * @return \Illuminate\Http\Response
     */
    public function charge()
    {
        if (empty(request()->stripe_cust_id)) {
            return $this->fail(401, 'stripe_cust_id must be a string');
        }
        if (empty(request()->succeeded)) {
            return $this->fail(401, 'succeeded must be a boolean');
        }
        
        if (request()->succeeded == 'true') {
            $succeeded = TRUE;
        } else if(request()->succeeded == 'false') {
            $succeeded = FALSE;
        } else {
            return $this->fail(401, 'succeeded must be a boolean');
        }

        $stripe_cust_id = request()->stripe_cust_id;
        $user = User::where('stripe_cust_id', $stripe_cust_id)->first();
        if (empty($user)) {
            return $this->fail(401, 'stripe_cust_id is invalid!');
        }
        $user->succeeded = $succeeded;
        $user->save();

        return $this->success('The subscription is added successfully!');
    }

    /**
     * Update the show_popup in users table so do not show the subscription notification popup any longer after sign up.
     *
     * @return \Illuminate\Http\Response
     */
    public function disablePopup() {
        $user = auth()->user();
        $user->show_popup = 0;
        $user->save();
        return $this->success('Disabled the subscription popup.');
    }
}
