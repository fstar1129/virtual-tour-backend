<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
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
    public static function getPurchase($user_id, $tour_id)
    {
        return self::where('user_id', $user_id)
            ->where('tour_id', $tour_id)
            ->first();
    }
}
