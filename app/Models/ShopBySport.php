<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopBySport extends Model
{
    protected $table = 'shop_by_sports';

    protected $fillable = [
        'sportname',
        'image',
        'slide_active',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'shopbysport_id');
    }
}
