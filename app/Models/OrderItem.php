<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'provider_product_id',
        'provider_variant_id',
        'product_title',
        'sku',
        'image_url',
        'quantity',
        'unit_price',
        'total_price',
        'raw',
    ];

    protected $casts = [
        'raw' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Optional: If you're saving products in DB and want to relate back
    public function product()
    {
        return $this->belongsTo(Product::class, 'provider_product_id', 'provider_product_id')
            ->where('provider', 'cj');
    }
}

