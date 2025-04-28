<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'user_id', 'product_id', 'status', 'deleted_at'];

    public function customer(){
        return $this->hasOne(Customer::class,"id","user_id");
    }
    public function hasProduct(){
        return $this->hasOne(Product::class,"id","product_id");
    }
}
