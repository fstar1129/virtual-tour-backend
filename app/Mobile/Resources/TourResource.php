<?php

namespace App\Mobile\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\TransactionLog;
use Illuminate\Support\Carbon;

class TourResource extends JsonResource
{
    /**
     * @var
     */
    private $detail;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource, $detail)
    {
        // Ensure you call the parent constructor
        parent::__construct($resource);
        $this->resource = $resource;
        $this->detail = $detail;
    }


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $images = [];
        if (! empty($this->image1)) {
            array_push($images, $this->image1->path);
        }
        if (! empty($this->image2)) {
            array_push($images, $this->image2->path);
        }
        if (! empty($this->image3)) {
            array_push($images, $this->image3->path);
        }
        $data = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'description' => $this->description,
            'pricing_type' => $this->pricing_type,
            'type' => $this->type,
            'stops_count' => $this->stops_count,
            'location' => new LocationResource($this->location),

            'facebook_url' => $this->facebook_url,
            'twitter_url' => $this->twitter_url,
            'instagram_url' => $this->instagram_url,

            'video_url' => $this->video_url,

            'has_prize' => $this->has_prize,
            'prize_details' => $this->prize_details,
            'prize_instructions' => $this->prize_instructions,
            'token_cost' => $this->token_cost,
            'local_token_cost' => $this->local_token_cost,
            'currency' => $this->currency,
            'trophy_image' => $this->trophyImage ? $this->trophyImage->path : null,

            'start_point' => $this->start_point_id,
            'start_message' => $this->start_message,
            'start_video_url' => $this->start_video_url,
            'start_image' => $this->startImage ? $this->startImage->path : null,

            'end_point' => $this->end_point_id,
            'end_message' => $this->end_message,
            'end_video_url' => $this->end_video_url,
            'end_image' => $this->endImage ? $this->endImage->path : null,

            'main_image' => $this->mainImage ? $this->mainImage->path : null,
            'images' => $images,
            'pin_image' => $this->pinImage ? $this->pinImage->path : null,

            'intro_audio' => $this->introAudio ? $this->introAudio->path : null,
            'background_audio' => $this->backgroundAudio ? $this->backgroundAudio->path : null,

            'length' => $this->length,
            'rating' => $this->rating,
            'in_app_id' => $this->in_app_id,

            'is_favorite' => (auth()->user()) ? auth()->user()->favorites->pluck('id')->contains($this->id) : false,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at ? $this->updated_at->toDateTimeString() : null,
            'published_at' => $this->published_at ? $this->published_at->toDateTimeString() : null
        ];

        if ($this->detail === 'detail') {
            $data['prize_location'] = new LocationResource($this->prizeLocation);
        }

        if (isset($this->distance)) {
            $data['distance'] = $this->distance;
        }

        if (auth()->check()) {
            $log = TransactionLog::getAccessExipries(auth()->user()->id, $this->id);
            $access_expires = -1;
            if (! empty($log)) {
                $access_expires1 = new Carbon($log->access_expires);
                $access_expires = $access_expires1->timestamp;
            }
            $data['access_expires'] = $access_expires;
        } else {
            return $data;
        }
        return $data;
    }
}
