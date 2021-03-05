<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TourPromoCode extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The relationships to always load with the model.
     *
     * @var array
     */
    public $with = [];

    /**
     * Handles the model boot options.
     *
     * @return void
     */
    protected static function boot()
    {
        //
        parent::boot();
    }

    // **********************************************************
    // RELATIONSHIPS
    // **********************************************************

    /**
     * Get the Tour relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    // **********************************************************
    // MUTATORS
    // **********************************************************

    // **********************************************************
    // QUERY SCOPES
    // **********************************************************

    // **********************************************************
    // OTHER METHODS
    // **********************************************************
}
