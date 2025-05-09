<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function hasProducts()
    {
        return $this->hasMany(Product::class, 'id', 'product');
    }
    
    public function hasCustomer()
    {
        return $this->hasMany(Customer::class, 'id', 'customer');
    }
}
