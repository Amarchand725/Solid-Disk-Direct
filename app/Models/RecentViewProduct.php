<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecentViewProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function hasProduct()
    {
        return $this->belongsTo(Product::class, 'product', 'slug');
    }
    public function hasCustomer()
    {
        return $this->belongsTo(Customer::class, 'customer', 'id');
    }
}
