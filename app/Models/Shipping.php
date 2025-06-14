<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    protected $fillable = [
        'order_id', 'full_name', 'email', 'phone', 'address', 'city', 'postal_code', 'country'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
