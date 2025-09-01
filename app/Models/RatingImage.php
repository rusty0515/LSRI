<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RatingImage extends Model
{
    protected $fillable = [
        'rating_id',
        'url',
    ];

    public function rating()
    {
        return $this->belongsTo(Rating::class);
    }
}
