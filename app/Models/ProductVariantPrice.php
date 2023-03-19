<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantPrice extends Model
{
    protected $fillable = [
        'product_id', 'price', 'stock'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariantOne()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_one');
    }

    public function productVariantTwo()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_two');
    }

    public function productVariantThree()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_three');
    }
}
