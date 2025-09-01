<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceRequest extends Model
{
    //
    protected $fillable = [
        'service_number',
        'user_id',
        'mechanic_id',
        'requested_date',
        'scheduled_date',
        'vehicle_type',
        'status',
        'remarks',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'scheduled_date' => 'date',
    ];

    public function customer() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function mechanic() : BelongsTo
    {
        return $this->belongsTo(User::class, 'mechanic_id', 'id');
    }

    public function items() : HasMany
    {
        return $this->hasMany(ServiceRequestItem::class);
    }

    public function warrantyRequest() : HasOne
    {
        return $this->hasOne(WarrantyRequest::class);
    }

    public function serviceRating() : HasOne
    {
        return $this->hasOne(ServiceRating::class);
    }

}
