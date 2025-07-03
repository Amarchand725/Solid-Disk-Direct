<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function items() {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function getVendor(){
        return $this->hasOne(Vendor::class, 'id', 'vendor');
    }

    public function getOrder(){
        return $this->hasOne(Order::class, 'order_number', 'order_number');
    }
}
