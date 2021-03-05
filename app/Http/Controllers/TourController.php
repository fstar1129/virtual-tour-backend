<?php

namespace App\Http\Controllers;

use App\Http\Resources\TourResource;
use App\Tour;
use App\User;
use App\Http\Requests\CreateTourRequest;
use App\Http\Requests\UpdateTourRequest;
use App\Http\Requests\UpdatePromoCodeRequest;
use Illuminate\Support\Arr;
use App\Http\Requests\UpdateStopOrderRequest;
use App\TourStop;
use App\Events\JunketSubmitForApprovalEvent;
use App\Events\JunketApproveNotificationEvent;
use App\Events\JunketRejectNotificationEvent;
use App\Events\RedeemJunketEvent;
use Config;

class TourController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index()
    {
        if (auth()->user()->roles[0]->name === 'admin' || auth()->user()->roles[0]->name === 'superadmin') {
            return TourResource::collection(
                Tour::all()
            );
        } else {
            return TourResource::collection(
                auth()->user()->type->tours
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\CreateTourRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTourRequest $request)
    {
        if (auth()->user()->roles[0]->name !== 'admin' && auth()->user()->roles[0]->name !== 'superadmin') {
            if (auth()->user()->type->tours()->count() >= auth()->user()->tour_limit) {
                return $this->fail(422, 'You have reached your maximum number of allowed tours.');
            }
        }

        if ($tour = auth()->user()->type->tours()->create($request->validated())) {
            return $this->success("The tour {$tour->title} was created successfully.", new TourResource(
                $tour->fresh()->load(['stops', 'route'])
            ));
        }

        return $this->fail();
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Tour $tour
     * @return \Illuminate\Http\Response
     */
    public function show(Tour $tour)
    {
        $tour->load(['stops', 'route', 'promoCodes']);

        return response()->json(new TourResource($tour));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTourRequest $request
     * @param \App\Tour $tour
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTourRequest $request, Tour $tour)
    {
        $data = $request->validated();

        \DB::beginTransaction();

        if ($tour->update(Arr::except($data, [
            'location', 'route', 'prize_location', 'promo_code', 'discount', 'quantity', 'promo_code_limit'
            ]))) {
            if ($request->has('location')) {
                $tour->location()->update(Arr::except($data['location'], ['id']));
            }

            if ($request->has('route')) {
                $tour->syncRoute($data['route']);
            }

            if ($request->has('prize_location')) {
                // var_dump($data);exit;
                $tour->prizeLocation()->update(Arr::except($data['prize_location'], ['id']));
            }

            // $tour->fresh()->updateLength();

            \DB::commit();

            $tour = $tour->fresh()->load(['stops', 'route']);
            return $this->success("{$tour->title} was updated successfully.", new TourResource($tour));
        }

        \DB::rollBack();
        return $this->fail();
    }

        /**
     * Update the tour's promo code.
     *
     * @param UpdatePromoCodeRequest $request
     * @return \Illuminate\Http\Response
     */
    public function addPromoCode(UpdatePromoCodeRequest $request, Tour $tour)
    {
        $data = $request->validated();
        $tour->promoCodes()->create([
            'promo_code' => $data["promo_code"],
            'discount' => $data["discount"],
            'quantity' => $data["quantity"],
        ]);
        $tour->promo_code_limit = $data["promo_code_limit"];
        $tour->save();
        $tour = $tour->fresh()->load(['stops', 'route', 'promoCodes']);
        return $this->success("Promo code has been saved. You can generate Promo code for {$tour->promo_code_limit} times.", new TourResource($tour));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Tour $tour
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Tour $tour)
    {
        if ($tour->delete()) {
            return $this->success("{$tour->title} was archived successfully.");
        }

        return $this->fail();
    }

    /**
     * Updates the order of all Tour's stops.
     *
     * @param UpdateStopOrderRequest $request
     * @param \App\Tour $tour
     * @return \Illuminate\Http\Response
     */
    public function stopOrder(UpdateStopOrderRequest $request, Tour $tour)
    {
        $order = 1;
        foreach ($request->order as $key => $id) {
            TourStop::where('id', $id)->update(['order' => $order]);
            $order ++;
        }

        return $this->success('Stop order successfully saved.', ['order' => $request->order]);
    }

    /**
     * Submit tour for publish approval.
     *
     * @param Tour $tour
     * @return \Illuminate\Http\Response
     */
    public function publish(Tour $tour)
    {
        $tour->load(['stops', 'route']);

        if ($errors = $tour->audit()) {
            return $this->fail(422, 'Cannot publish tour.', [
                'tour' => new TourResource($tour),
                'errors' => $errors,
            ]);
        }
        
        $tour->fresh()->updateLength();

        if ($tour->isPublished) {
            return $this->fail(422, "{$tour->title} has already been published.", new TourResource($tour));
        }

        // auto-approve tour for admins
        if (auth()->user()->isAdmin()) {
            if (empty($tour->in_app_id)) {
                $tour->in_app_id = "com.wejunket";
                // return $this->fail(422, 'Cannot publish tour without an In-App ID', new TourResource($tour));
            }

            if ($tour->isAwaitingApproval) {
                $tour->publishSubmissions()
                    ->pending()
                    ->first()
                    ->approve();
            } else {
                $submission = $tour->publishSubmissions()->create([
                    'tour_id' => $tour->id,
                    'user_id' => $tour->user_id,
                ]);
                $submission->approve();
            }

            $tour = $tour->fresh()->load(['stops', 'route']);
            $user = User::find($tour->user_id);
            event(new JunketApproveNotificationEvent($tour, $user));
            return $this->success("{$tour->title} has been published.", new TourResource($tour));
        }

        if ($tour->submitForPublishing()) {
            $tour = $tour->fresh()->load(['stops', 'route']);
            // email (contact@wejunket.com) that a junket has been submitted for approval.
            event(new JunketSubmitForApprovalEvent($tour, auth()->user()));
            return $this->success("{$tour->title} has been submitted for publishing and awaiting approval.", new TourResource($tour));
        }

        return $this->fail();
    }

    /**
     * Un-publish the tour, or cancel a publish request.
     *
     * @param Tour $tour
     * @return \Illuminate\Http\Response
     */
    public function unpublish(Tour $tour)
    {
        $tour->load(['stops', 'route']);

        if ($tour->isAwaitingApproval) {
            $tour->publishSubmissions()->pending()->first()->delete();
            $tour = $tour->fresh()->load(['stops', 'route']);
            return $this->success("{$tour->title} has been removed from the approval queue.", new TourResource($tour));
        }

        $tour->published_at = null;
        $tour->save();

        return $this->success("{$tour->title} has been unpublished and removed from the apps.", new TourResource($tour));
    }

    /**
     * Reject a publish request.
     *
     * @param Tour $tour
     * @return \Illuminate\Http\Response
     */
    public function unpublishByRejection(Tour $tour)
    {
        $tour->load(['stops', 'route']);

        if ($tour->isAwaitingApproval) {
            $tour->publishSubmissions()->pending()->first()->delete();
            $tour = $tour->fresh()->load(['stops', 'route']);
            $user = User::find($tour->user_id);
            event(new JunketRejectNotificationEvent($tour, $user, request()->reason));
            return $this->success("{$tour->title} has been removed from the approval queue.", new TourResource($tour));
        }
    }

    /**
     * Send email with promo_code and instruction for Peek
     *
     * @param Tour $tour
     * @return \Illuminate\Http\Response
    */
    public function purchaseJunketFromPeek() {
        $email = request()->email;
        $last_name = request()->last_name;
        $first_name = request()->first_name;
        $junket_name = request()->junket_name;
        $number_of_guests = request()->number_of_guests;
        $tour = Tour::where('title', $junket_name)->first();
        if (empty($tour)) {
            return response()->json(['error' => 'invalid_junket_name'], 401);
        }
        $promo_codes = [];
        for($i = 0; $i < $number_of_guests; $i++) {
            $temp = $this->getName(6);
            array_push($promo_codes, $temp);
            $tour->promoCodes()->create(['promo_code' => $temp, 'discount' => 100, 'quantity' => 1]);
        }

        event(new RedeemJunketEvent($tour, $email, $first_name, $last_name, $promo_codes));
        return $this->success("{$tour->title} has been purchased.", new TourResource($tour));
    }

    /**
     * Send email with promo_code and instruction for FareHarbor webhook
     *
     * @param Tour $tour
     * @return \Illuminate\Http\Response
    */
    public function purchaseJunketFromFareHarbor(Tour $tour) {
        $booking = request()->booking;
        $email = $booking['contact']['email'];
        $last_name = '';
        $first_name = $booking['contact']['name'];
        $junket_name = $booking['availability']['item']['name'];
        $number_of_guests = count($booking['customers']);
        if (empty($tour)) {
            return response()->json(['error' => 'invalid_junket_name'], 401);
        }
        $promo_codes = [];
        for($i = 0; $i < $number_of_guests; $i++) {
            $temp = $this->getName(6);
            array_push($promo_codes, $temp);
            $tour->promoCodes()->create(['promo_code' => $temp, 'discount' => 100, 'quantity' => 1]);
        }

        event(new RedeemJunketEvent($tour, $email, $first_name, $last_name, $promo_codes));
        return $this->success("{$tour->title} has been purchased.");
    }

    /**
     * Send email with promo_code and instruction for FareHarbor test webhook 
     *
     * @param Tour $tour
     * @return \Illuminate\Http\Response
    */
    public function purchaseJunketFromFareHarborTest(Tour $tour) {
        $booking = request()->booking;
        $email = $booking['contact']['email'];
        $last_name = '';
        $first_name = $booking['contact']['name'];
        $junket_name = $booking['availability']['item']['name'];
        $number_of_guests = count($booking['customers']);
        // $tour = Tour::where('title', $junket_name)->first();
        if (empty($tour)) {
            return response()->json(['error' => 'invalid_junket_name'], 401);
        }
        $promo_codes = [];
        for($i = 0; $i < $number_of_guests; $i++) {
            $temp = $this->getName(6);
            array_push($promo_codes, $temp);
            $tour->promoCodes()->create(['promo_code' => $temp, 'discount' => 100, 'quantity' => 1]);
        }

        event(new RedeemJunketEvent($tour, 'sandybux99@gmail.com', $first_name, $email . $junket_name, $promo_codes));
        return $this->success("Test webhook from FareHarbor.");
    }

    /**
     * Generate a random, unique, alpha-numeric string
     *
     * @param integer $n
     * @return string
    */
    private function getName($n) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
    
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }

    /**
     * Send email with promo_code and instruction from for thridparty website
     *
     * @param \Illuminate\HttpRequest $request
     * @return \Illuminate\Http\Response
    */
    public function purchaseJunketFromThirdparty(){
        $email = request()->email;
        $full_name = request()->full_name;
        $junket_id = request()->junket_id;
        $number_of_guests = request()->number_of_guests;
        $card_number = request()->card_number;
        $exp_month = request()->exp_month;
        $exp_year = request()->exp_year;
        $cvc = request()->cvc;
        $amount = request()->amount;

        $tour = Tour::find($junket_id);
        if (empty($tour)) {
            return response()->json(['error' => 'invalid_junket_id'], 401);
        }

        // $amount = $tour->token_cost * $number_of_guests * 100;

        try {
          // Use Stripe's library to make requests...
          \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
          
          $token = \Stripe\Token::create(array(
            "card" => array(
              "number" => $card_number,
              "exp_month" => $exp_month,
              "exp_year" => $exp_year,
              "cvc" => $cvc
            )
          ));

          \Stripe\Charge::create(array(
            "amount" => $amount,
            "currency" => "usd",
            "source" => $token, // obtained with Stripe.js
            "description" => "Purchase of " . $tour->title . ' for ' . $number_of_guests . ' people',
            "receipt_email" => $email,
          ));

        } catch(\Stripe\Error\Card $e) {
          // Since it's a decline, \Stripe\Error\Card will be caught
          return response()->json($e->getJsonBody());
        } catch (\Stripe\Error\RateLimit $e) {
          // Too many requests made to the API too quickly
          return response()->json($e->getJsonBody());
        } catch (\Stripe\Error\InvalidRequest $e) {
          // Invalid parameters were supplied to Stripe's API
          return response()->json($e->getJsonBody());
        } catch (\Stripe\Error\Authentication $e) {
          // Authentication with Stripe's API failed
          // (maybe you changed API keys recently)
          return response()->json($e->getJsonBody());
        } catch (\Stripe\Error\ApiConnection $e) {
          // Network communication with Stripe failed
          return response()->json($e->getJsonBody());
        } catch (\Stripe\Error\Base $e) {
          // Display a very generic error to the user, and maybe send
          // yourself an email
          return response()->json($e->getJsonBody());
        } catch (Exception $e) {
          // Something else happened, completely unrelated to Stripe
          return response()->json($e->getJsonBody());
        }

        $promo_codes = [];
        for($i = 0; $i < $number_of_guests; $i++) {
            $temp = $this->getName(6);
            array_push($promo_codes, $temp);
            $tour->promoCodes()->create(['promo_code' => $temp, 'discount' => 100, 'quantity' => 1]);
        }

        try {
            event(new RedeemJunketEvent($tour, $email, $full_name, '', $promo_codes));
        } catch (Exception $e) {
            return $this->fail(422, "Email is incorrect!");
        }
        
        return response()->json([
          'promoCodes' => $promo_codes
        ]);
    }
}
