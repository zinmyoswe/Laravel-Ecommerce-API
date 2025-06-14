<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'session_id', 'total_price', 'status'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shipping()
    {
        return $this->hasOne(Shipping::class);
    }
}
