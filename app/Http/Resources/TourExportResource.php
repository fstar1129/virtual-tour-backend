<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TourExportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $tour_data = $this->resource->toArray();
        $data['id'] = strval($tour_data['id']);
        $data['title'] = $tour_data['title'];
        $data['location'] = $this->resource->location->city == '' ? '' : ($this->resource->location->city . ", ") . $this->resource->location->state;
        $data['type'] = $tour_data['type'];
        $data['number_of_stops'] = strval($this->resource->getStopsCountAttribute());
        $data['total_audio'] = strval(number_format($this->resource->getTotalAudioLength() / 60, 2));
        $data['length'] = strval($this->resource->calculator()->getTourLength());
        $data['created_at'] = $tour_data['created_at'];
        $data['description'] = $tour_data['description'];
        $data['prize_details'] = strval($tour_data['prize_details']);
        $data['price'] = strval($this->resource->token_cost);
        $data['fareharbor_url'] = "https://api.wejunket.com/purchase-junket-from-fareharbor/". $data['id'];
        return $data;
    }
}
