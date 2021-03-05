<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use App\Events\ConfirmationChangeOfPlan;
use App\PricingPlan;

class ChangePlanController extends Controller
{
    /**
     * Change the user's password.
     *
     * @param Request $request
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(User $user)
    {
        if ($user->update([
            'pricing_plan_id' => request()->pricing_plan_id,
            'tour_limit' => request()->tour_limit
            ])) {
            $pricing_plan = PricingPlan::find(request()->pricing_plan_id);
            $count_additional_junkets = $user->tour_limit - $pricing_plan->tour_limit;
            event(new ConfirmationChangeOfPlan($user, $pricing_plan, $count_additional_junkets));
            return $this->success('Pricing Plan has been changed.');
        }

        return $this->fail();
    }
}
