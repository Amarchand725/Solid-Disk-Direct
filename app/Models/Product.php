<?php

namespace App\Models;

use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->slug = Str::slug($model->title);
        });

        static::updating(function ($model) {
            $model->slug = Str::slug($model->title);
        });
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by')->select(['name']);
    }

    public function hasBrand(){
        return $this->hasOne(Brand::class, 'id', 'brand');
    }
    public function hasCategory(){
        return $this->hasOne(Category::class, 'id', 'category');
    }
    public function hasUnit(){
        return $this->hasOne(Unit::class, 'id', 'unit');
    }
    public function hasTaxType(){
        return $this->hasOne(TaxType::class, 'id', 'tax_type');
    }
    public function hasProductCondition(){
        return $this->hasOne(ProductCondition::class, 'id', 'condition');
    }

    public function hasProductImages(){
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }
}
