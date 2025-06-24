<?php

namespace App\Models;

use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;
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

        // static::creating(function ($model) {
        //     $model->slug = Str::slug($model->title);
        // });

        static::creating(function ($model) {
            $slug = Str::slug($model->title);
            $originalSlug = $slug;
            $count = 1;

            // Check if slug exists in the `products` table
            while (DB::table('products')->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }

            $model->slug = $slug;
        });

        static::updating(function ($model) {
            $model->slug = Str::slug($model->title);
        });
    }

    public function pivotCategories()
    {
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id');
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
        return $this->belongsTo(Brand::class, 'brand');
    }
    public function hasProductCondition(){
        return $this->hasOne(ProductCondition::class, 'id', 'condition');
    }

    public function mainCategory()
    {
        return $this->belongsTo(Category::class, 'category');
    }

    public function hasUnit(){
        return $this->hasOne(Unit::class, 'id', 'unit');
    }
    public function hasTaxType(){
        return $this->hasOne(TaxType::class, 'id', 'tax_type');
    }

    public function hasProductImages(){
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function leafCategories()
    {
        return $this->categories()->whereDoesntHave('children');
    }

    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attributes');
    }
}
