<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Activity;
use App\TransactionLog;
use App\PricingPlan;

class FinancialReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $revenue_per_client = 0;
        $earning_per_client = 0;
        foreach($this->tours as $tour) {
            $results = $tour->stats()
            ->betweenDates(request()->start_date, request()->end_date)
            ->orderBy('yyyymmdd')
            ->get();

            $revenue_per_tour = 0;
            $earning_per_tour = 0;

            foreach($results as $item) {
                $google_apple_commission = 30;
                $downloads = Activity::select('device_id')
                    ->distinct()
                    ->where('action', 'start_stop')
                    ->where('actionable_id', $item->tour_id)
                    ->where('actionable_type', 'App\Tour')
                    ->where('begin_at', 'like', date('Y-m-d', strtotime($item->yyyymmdd)) . '%')
                    ->get();
                $revenues = TransactionLog::where('transaction_type', 'redemption')
                    ->where('created_at', 'like', date('Y-m-d', strtotime($item->yyyymmdd)) . '%')
                    ->where('tour_id', $item->tour_id)
                    ->get()
                    ->sum('num_tokens');

                $earning = TransactionLog::where('transaction_type', 'redemption')
                    ->where('created_at', 'like', date('Y-m-d', strtotime($item->yyyymmdd)) . '%')
                    ->where('tour_id', $item->tour_id)
                    ->get()
                    ->sum(function($t){
                        $revenue_split = PricingPlan::find($t->pricing_plan_id)->revenue_split;
                        return $t->num_tokens * $revenue_split / 100; 
                    });
                
                    $revenue_per_tour += -1 * $revenues;
                    $earning_per_tour += $earning * (100 - $google_apple_commission) / 100 * -1;
            }

            $revenue_per_client += $revenue_per_tour;
            $earning_per_client += $earning_per_tour;
        }

        return array_merge($this->resource->toArray(), [
            'tours_count' => $this->tours()->count(),
            'revenue' => $revenue_per_client,
            'earning' => $earning_per_client,
        ]);
    }
}
