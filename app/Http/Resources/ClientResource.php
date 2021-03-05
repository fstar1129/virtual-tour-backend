<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\PricingPlan;

class ClientResource extends JsonResource
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
        $pricing_plans = PricingPlan::where('plan_name', '<>', 'super')->get();
        return array_merge($this->resource->toArray(), [
            'tours_count' => $this->tours()->count(),
            'active_plan' => $pricing_plan,
            'pricing_plans' => $pricing_plans,
            'junkets' => $this->tours()->select('id', 'title')->get()
        ]);
    }
}
