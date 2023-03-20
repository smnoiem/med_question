<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'variant', 'variant_id', 'product_id',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function variants()
    {
        return $this->hasMany(Variant::class);
    }

    public function productVariantPriceOne()
    {
        return $this->hasMany(ProductVariantPrice::class, 'product_variant_one');
    }

    public function productVariantPriceTwo()
    {
        return $this->hasMany(ProductVariantPrice::class, 'product_variant_two');
    }

    public function productVariantPriceThree()
    {
        return $this->hasMany(ProductVariantPrice::class, 'product_variant_three');
    }
}
