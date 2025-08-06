<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'productid';

    protected $fillable = [
        'productname',
        'productimage',
        'productimages',
        'productvideo',
        'category_id',
        'subcategory_id',
        'shopbysport_id',
        'size',
        'color',
        'price',
        'discount',
        'stock',
        'description',
        'gender',
        'adminid',
        'sameproductid',
    ];

    protected $casts = [
        'productimages' => 'array',
        'sizes' => 'array',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'categoryid');
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id', 'subcategoryid');
    }


    public function similarProducts()
{
    return $this->belongsToMany(Product::class, 'product_similars', 'product_id', 'similar_product_id');
}

    public function sizes()
    {
        return $this->belongsToMany(Size::class, 'product_size', 'product_id', 'size_id');
    }

    public function shopBySport()
    {
        return $this->belongsTo(ShopBySport::class, 'shopbysport_id');
    }
}
