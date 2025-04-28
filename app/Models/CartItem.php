<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = ['cart_token', 'product_id', 'quantity', 'unit_price', 'discount', 'sub_total', 'options'];

    public function cart()
    {
        return $this->hasOne(Cart::class, 'cart_token');
    }

    public function product(){
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
