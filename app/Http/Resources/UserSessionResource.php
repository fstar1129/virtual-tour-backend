<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\PricingPlan;

class UserSessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $pricing_plan = PricingPlan::find($this->pricing_plan_id);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'fb_id' => $this->fb_id,
            'role' => $this->role,
            'avatar_url' => $this->avatarUrl,
            'token_balance' => $this->token_balance,
            'subscribe_override' => in_array($this->role, ['admin', 'superadmin']) ? true : $this->subscribe_override,
            'created_at' => $this->created_at->timestamp,
            'stripe_cust_id' => $this->stripe_cust_id,
            'plan_id' => $pricing_plan->plan_id,
            'plan_name' => $pricing_plan->plan_name,
            'show_popup' => $this->show_popup,
            'succeeded' => $this->succeeded,
            'email_confirmed_at' => $this->email_confirmed_at
        ];
    }
}
