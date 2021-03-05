<?php

namespace App\Mobile\Controllers;

use App\Tour;
use App\User;
use App\TransactionLog;
use App\Http\Controllers\Controller;
use App\Mobile\Resources\TourResource;
use App\Mobile\Resources\StopResource;
use App\Mobile\Resources\TourRouteResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Mobile\Resources\ReviewResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TourController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param \Illumate\Http\Request
     * @return \Illuminate\Http\Resources\Json\ResourceCollection|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $lat = 0;
        $lon = 0;
        if ($request->has('nearby')) {
            $coordinates = $request->nearby;

            if (! strpos($coordinates, ',')) {
                return $this->fail(422, 'Invalid nearby coordinates.');
            }

            $lat = floatval(substr($coordinates, 0, strpos($coordinates, ',')));
            $lon = floatval(substr($coordinates, strpos($coordinates, ',') + 1));

            if ($lat == 0 || $lon == 0) {
                return $this->fail(422, 'Invalid distance_from coordinates.');
            }
        }

        return TourResource::collection(
            Tour::published($request->debug == 1)
                ->distanceFrom($lat, $lon)
                ->favoritedBy($request->favorites == 1 ? auth()->id() : null)
                ->search($request->search)
                ->paginate()
        );
    }

    /**
     * Display a listing of free tours.
     *
     * @param \Illumate\Http\Request
     * @return \Illuminate\Http\Resources\Json\ResourceCollection|\Illuminate\Http\Response
     */
    public function getFreeTours(Request $request)
    {
        $lat = 0;
        $lon = 0;
        if ($request->has('nearby')) {
            $coordinates = $request->nearby;

            if (! strpos($coordinates, ',')) {
                return $this->fail(422, 'Invalid nearby coordinates.');
            }

            $lat = floatval(substr($coordinates, 0, strpos($coordinates, ',')));
            $lon = floatval(substr($coordinates, strpos($coordinates, ',') + 1));

            if ($lat == 0 || $lon == 0) {
                return $this->fail(422, 'Invalid distance_from coordinates.');
            }
        }
        
        return TourResource::collection(
            Tour::publishedFree($request->debug == 1)
                ->distanceFrom($lat, $lon)
                ->favoritedBy($request->favorites == 1 ? auth()->id() : null)
                ->search($request->search)
                ->paginate()
        );
    }

    /**
     * Display a free tour.
     *
     * @param \Illumate\Http\Request
     * @return \Illuminate\Http\Resources\Json\ResourceCollection|\Illuminate\Http\Response
     */
    public function getFreeTour(Request $request, $tour_id)
    {
        $tour = Tour::find($tour_id);
        $reviews = $tour->reviews()->whereNotNull('review')->with('user')->latest()->limit(5)->get();
        return response()->json([
            'tour' => new TourResource($tour, 'detail'),
            'stops' => StopResource::collection($tour->stops),
            'route' => TourRouteResource::collection($tour->route),
            'latest_reviews' => ReviewResource::collection($reviews),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Tour $tour
     * @return \Illuminate\Http\Response
     * @throws ModelNotFoundException
     */
    public function show(Tour $tour)
    {
        // if (! $tour->isLive(request()->debug == 1)) {
        //     if (! empty($tour->last_published_at)) {
        //         // tour was published at one time, but it no longer available
        //         throw new ModelNotFoundException('Tour no longer available.');
        //     }
        //     throw new ModelNotFoundException('Tour not available.');
        // }

        $reviews = $tour->reviews()->whereNotNull('review')->with('user')->latest()->limit(5)->get();
        return response()->json([
            'tour' => new TourResource($tour, 'detail'),
            'stops' => StopResource::collection($tour->stops),
            'route' => TourRouteResource::collection($tour->route),
            'latest_reviews' => ReviewResource::collection($reviews),
        ]);
    }

    /**
     * Display the specified resource from tour_id.
     *
     * @param \App\Tour $tour
     * @return \Illuminate\Http\Response
     * @throws ModelNotFoundException
     */
    public function showTour($tour_id)
    {
        $tour = Tour::find($tour_id);
        $reviews = $tour->reviews()->whereNotNull('review')->with('user')->latest()->limit(5)->get();
        return response()->json([
            'tour' => new TourResource($tour, 'detail'),
            'stops' => StopResource::collection($tour->stops),
            'route' => TourRouteResource::collection($tour->route),
            'latest_reviews' => ReviewResource::collection($reviews),
        ]);
    }
    

    /**
     * Gets all tours without paging.
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function all(Request $request)
    {
        return TourResource::collection(
            Tour::published($request->user()->id, request()->debug)->paginate(999999)
        );
    }

    /**
     * Purchase Tour tokens
     * 
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function purchase(Request $request, Tour $tour)
    {
        $user = auth()->user();
        $num_people = 1;
        if (!empty($request->num_people)) {
            $num_people = $request->num_people;
        }
        if ( empty($request->promo_code) ) {
            $discounted_price = $num_people * $tour->token_cost;
            if ($tour->token_cost > $user->token_balance) {
                if (!$user->isAdmin()) {
                    $addtional_token = $discounted_price - $user->token_balance;
                    return $this->fail(422, "Token_balance: ".$user->token_balance." is not enough. You need ". $addtional_token . " additional tokens to access this Junket. Please add more tokens and try again.");
                }
            }
        } else {
            $promo_code = $tour->promoCodes()->where('promo_code', $request->promo_code)->first();
            if (empty($promo_code)) {
                return $this->fail(406, "The promo code is invalid.");
            } else if ($promo_code->quantity == 0) {
                return $this->fail(406, "The promo code is expired.");
            } else {
                $discounted_price = $num_people * $tour->token_cost * (100 - $promo_code->discount) / 100;
                if ($discounted_price > $user->token_balance) {
                    if (!$user->isAdmin()) {
                        $addtional_token = $discounted_price - $user->token_balance;
                        return $this->fail(402, "The code was valid, but you still need {$addtional_token} additional tokens to access this Junket. Please add more tokens and try again.");
                    }
                }
            }
        }

        $now = Carbon::now();
        $log = TransactionLog::getAccessExipries($user->id, $tour->id);
        if (!empty($log)) {
            $current_access_expires = new Carbon($log->access_expires);
            if ($now->timestamp < $current_access_expires->timestamp) {
                return $this->fail(422, "User had already purchased the tour, and the tour isn't expired");
                // return response()->json(['Access_expires' => "tour's access expires is in the future"], 422);
            }
        }

        $updated_token_balance = $user->token_balance - $discounted_price;
        $user->token_balance = $updated_token_balance;
        $user->save();

        $new_log = new TransactionLog;
        $new_log->transaction_type  = "redemption";
        $new_log->client_ip_address  = request()->ip();
        $new_log->user_id = $user->id;
        $new_log->num_tokens = -1 * $tour->token_cost;
        $new_log->tour_id = $tour->id;
        $new_log->pricing_plan_id = User::find($tour->user_id)->pricing_plan_id;
        if (!empty($request->duration)) {
            $access_expires = $now->addSeconds($request->duration);
        }
        else {
            $access_expires = $now->addDays(7);
        }
        $new_log->access_expires = $access_expires;
        
        $new_log->balance = $updated_token_balance;

        if ( !empty($request->promo_code) ) {
            $new_log->promo_code = $request->promo_code;
            $new_log->save();
            $promo_code->quantity = $promo_code->quantity - 1;
            $promo_code->save();
            return response()->json(new TourResource($tour, 'detail'));
        } else {
            $new_log->save();
            return response()->json(new TourResource($tour, 'detail'));
        }
    }

    /**
     * Check promo code
     *
     * @param Tour $tour
     * @param string $promo_code
     * @return \Illuminate\Http\Response
     */
    public function checkPromoCode(Tour $tour, string $promo_code_string)
    {
        $promo_code = $tour->promoCodes()->where('promo_code', $promo_code_string)->first();

        if (!empty($promo_code)){
            return response()->json(['promo_code_token_cost' => $tour->token_cost * (100 - $promo_code->discount) / 100,
                'num_uses' => $promo_code->quantity]);
        }
        return $this->fail(404, "Invalid promo code: {$promo_code_string}");
    }
}
