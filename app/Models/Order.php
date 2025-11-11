<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Order extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'courier_id',
        'order_number',
        'order_total_price',
        'order_status',
        'shipping_price',
        'distance_in_km',
        'payment_method',
        'payment_status',
        'payment_reference',
        'order_notes',
        'payment_intent_id',
    ];

    protected $casts = [
        'is_billing_same_as_shipping' => 'boolean',
        'order_status' => OrderStatusEnum::class,
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // /** @return MorphOne<Address> */
    // public function address(): MorphOne
    // {
    //     return $this->morphOne(Address::class, 'addressable');
    // }

    public function addresses(): MorphToMany
    {
        return $this->morphToMany(Address::class, 'addressable');
    }

    public function address()
    {
        // Helper method to get the first (shipping) address
        return $this->addresses()->where('address_type', 'shipping')->first();
    }

    // public function shippingAddress() : BelongsTo
    // {
    //     return $this->belongsTo(Address::class, 'shipping_address_id');
    // }

    // public function billingAddress() : BelongsTo
    // {
    //     return $this->belongsTo(Address::class, 'billing_address_id');
    // }

    // public function shippingAddress()
    // {
    //     return $this->morphToMany(Address::class, 'addressable', 'addressables')
    //         ->wherePivot('address_type', 'shipping')
    //         ->withTimestamps();
    // }

    // public function billingAddress()
    // {
    //     return $this->morphToMany(Address::class, 'addressable', 'addressables')
    //         ->wherePivot('address_type', 'billing')
    //         ->withTimestamps();
    // }

    // public function addresses() : MorphToMany
    // {
    //     return $this->morphToMany(Address::class, 'addressable', 'addressables')
    //         ->withTimestamps();
    // }

    public function orderItems() : HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
