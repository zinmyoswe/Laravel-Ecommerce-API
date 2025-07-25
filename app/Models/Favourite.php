<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'size',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'productid'); // map to products.productid
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
