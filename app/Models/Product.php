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
        'stock',
        'image', 
        'category_id',
        'sizes' // Add sizes to fillable
    ];

    // Automatically convert the JSON column to a PHP array
    protected $casts = [
        'sizes' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship for the additional gallery images
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
}