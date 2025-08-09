<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'categoryid';

    protected $fillable = [
        'categoryname',
        // 'subcategoryid',
    ];

    // public function subcategory()
    // {
    //     return $this->belongsTo(Subcategory::class, 'subcategoryid', 'subcategoryid');
    // }

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'category_id', 'categoryid');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'categoryid');
    }
}
