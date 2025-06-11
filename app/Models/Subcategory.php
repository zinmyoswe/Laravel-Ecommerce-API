<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $primaryKey = 'subcategoryid';

    protected $fillable = [
        'subcategoryname',
    ];

    public function categories()
    {
        return $this->hasMany(Category::class, 'subcategoryid', 'subcategoryid');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'subcategory_id', 'subcategoryid');
    }
}
