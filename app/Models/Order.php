<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public function items() {
        return $this->hasMany(OrderItem::class);
    }
    
    public function billing() {
        return $this->hasOne(OrderBillingAddress::class);
    }
    
    public function shipping() {
        return $this->hasOne(OrderShippingAddress::class);
    }    
}
