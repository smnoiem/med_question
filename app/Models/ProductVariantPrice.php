<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantPrice extends Model
{
    protected $fillable = [
        'product_id', 'price', 'stock'
    ];
}
