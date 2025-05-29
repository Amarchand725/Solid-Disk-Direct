<?php

namespace App\Models;

use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
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

    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product')
                    ->whereNotNull('unit_price')
                    ->where('unit_price', '>', 0);
    }

    public function limitedProducts()
    {
        return $this->belongsToMany(Product::class,'category_product')->limit(4)->whereNotNull('unit_price')
                    ->where('unit_price', '>', 0);
    }
    
    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function hasParent()
    {
        return $this->belongsTo(Category::class, 'parent');
    }
    
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function children()
    {
        return $this->belongsToMany(Category::class, 'category_relations', 'parent_id', 'child_id');
    }

    public function parents()
    {
        return $this->belongsToMany(Category::class, 'category_relations', 'child_id', 'parent_id');
    }
}
