<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
        'quantity',
        'size',
    ];

    /**
     * Get the product associated with the cart item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'productid');
    }

    /**
     * Get the user who owns this cart (if logged in).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
