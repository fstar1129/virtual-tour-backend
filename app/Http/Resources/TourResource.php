<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\PricingPlan;
use App\Language;
use Log;

class TourResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = $this->resource->toArray();

        if (isset($data['stops'])) {
            $data['stops'] = StopResource::collection($this->resource->stops);
        }

        if (isset($data['route'])) {
            $data['route'] = RouteResource::collection($this->resource->route);
        }

        if (auth()->check()) {
            $pricing_plan = auth()->user()->getPricingPlan();
            $data['allowed_stop_count'] = $pricing_plan->stop_limit - $data['stops_count'];
        }

        if ($data['type'] == 'adventure') {
            $data['connections'] = $this->getAllConnections();
        }

        $data['languages'] = Language::all();
        $data['applied_languages'] = $this->languages();
        return $data;
    }
}
