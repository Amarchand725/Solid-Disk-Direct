<?php

namespace App\Models;

use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->slug = Str::slug($model->name);
        });

        static::updating(function ($model) {
            $model->slug = Str::slug($model->name);
        });
    }

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function hasProducts()
    {
        return $this->hasMany(Product::class, 'brand', 'id')
                    ->whereNotNull('unit_price')
                    ->where('unit_price', '>', 0);
    }

    public function products() {
        return $this->hasMany(Product::class, 'brand', 'id');
    }

    public function limitedProducts()
    {
          return $this->hasMany(Product::class, 'brand', 'id')
                ->whereNotNull('unit_price')
                ->where('unit_price', '>', 0);
    }
}
