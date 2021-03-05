<?php

namespace App\Mobile\Controllers;

use App\Mobile\Resources\ProfileResource;
use App\User;
use App\Tour;
use App\TransactionLog;
use App\Mobile\Requests\UpdateProfileRequest;
use App\Http\Controllers\Controller;
use App\Mobile\Requests\UpdatePasswordRequest;
use Illuminate\Http\Request;
use App\Action;
use Log;
class ProfileController extends Controller
{
    /**
     * Get the given user's profile.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response()->json(new ProfileResource($user));
    }

    /**
     * Update the current user's profile.
     *
     * @param UpdateProfileRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProfileRequest $request)
    {
        auth()->user()->update($request->validated());

        return response()->json(new ProfileResource(auth()->user()->fresh()));
    }

    /**
     * Change the user's profile.
     *
     * @param UpdatePasswordRequest $request
     * @return \Illuminate\Http\Response
     */
    public function password(UpdatePasswordRequest $request)
    {
        auth()->user()->update(['password' => bcrypt($request->password)]);

        return $this->success('Your password has been updated.');
    }

    /**
     * Purchase token.
     *
     * @return \Illuminate\Http\Response
     */
    public function purchase(Request $request)
    {
        if (empty($request->num_tokens)) {
            return $this->fail(422, 'Missing num_tokens');
        } else if (! ($request->num_tokens > 0 && $request->num_tokens < 1000)) {
            return $this->fail(422, 'num_tokens should be valid!');
        }
        if (!empty($request->digital_store_token)) {
            $logs = TransactionLog::isValidPurchaseToken($request->digital_store_token);
            if (!empty($logs)) {
                return $this->fail(422, 'digital_store_token is already used');
            }
        }

        $user = auth()->user();
        $token_balance = $user->token_balance + $request->num_tokens;
        $user->token_balance = $token_balance;
        $user->save();

        $new_log = new TransactionLog;
        $new_log->transaction_type  = "purchase";
        $new_log->client_ip_address  = request()->ip();
        $new_log->user_id = $user->id;
        $new_log->num_tokens = $request->num_tokens;
        $new_log->digital_store_token = $request->digital_store_token;
        $new_log->balance = $token_balance;
        $new_log->save();

        return response()->json(['token_balance' => $token_balance]);
    }
    /**
     * Change the user's profile.
     *
     * @return \Illuminate\Http\Response
     */

    public function unclaimedPrizes(User $user) {
        $score_cards = $user->scoreCards()
            ->whereNotNull('won_trophy_at')
            ->get()
            ->unique('tour_id');
        $tours_with_unclaimed_prizes = [];
        foreach($score_cards as $score_card) {
            if (!empty($score_card->tour) && $score_card->tour->has_prize) {
                $redeemed_prizes = $user->activity()->where('action', Action::REDEEMED_PRIZE)
                        ->where('actionable_type', 'App\Tour')
                        ->where('actionable_id', $score_card->tour->id)
                        ->get();
                if (empty($redeemed_prizes->toArray())) {
                    $arr = ['tour_id' => $score_card->tour->id, 'prize_details' => $score_card->tour->prize_details];
                    array_push($tours_with_unclaimed_prizes, $arr);
                }
            }
        }
        return $tours_with_unclaimed_prizes;
    }
}
