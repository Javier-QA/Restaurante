<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'category_id',
        'stock',
        'is_active',
        'is_saleable'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(
            Product::class,
            'product_ingredients',
            'product_id',
            'ingredient_id'
        )->withPivot('quantity');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
