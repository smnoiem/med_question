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
}
