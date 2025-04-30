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
        return $this->belongsToMany(Product::class, 'category_product');
    }
    
    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function hasParent()
    {
        return $this->belongsTo(Category::class, 'parent');
    }
    public function children()
    {
        return $this->hasMany(Category::class, 'parent');
    }
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }
}
