<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    
    protected $fillable = [
        'user_id',
        'session_id',
        'total', // If you're using this field name in your table
        'status',
        'shipping_fee',       // ✅ Add this if missing
        'delivery_option',    // ✅ Add this if missing
        'payment_method',
        'paid_at',
        'stripe_charge_id',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shipping()
    {
        return $this->hasOne(Shipping::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
