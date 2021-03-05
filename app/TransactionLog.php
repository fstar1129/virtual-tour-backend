<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class TransactionLog extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get access_expires
     *
     * @param int $user_id
     * @param int $tour_id
     * @return access_expires
     */
    public static function getAccessExipries($user_id, $tour_id)
    {
        $now = Carbon::now();
        return self::where('user_id', $user_id)
            ->where('tour_id', $tour_id)
            ->where('access_expires', '>', $now)
            ->first();
    }

    /**
     * Check if the purchase token is valid.
     *
     * @param string $digital_store_token
     * @return array
     */
    public static function isValidPurchaseToken($digital_store_token)
    {
        return self::where('digital_store_token', $digital_store_token)
            ->first();
    }
}
