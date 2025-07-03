<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyNow extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product(){
        return $this->hasOne(Product::class, 'slug', 'product_slug');
    }
}
