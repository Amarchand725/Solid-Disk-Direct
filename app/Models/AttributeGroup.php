<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeGroup extends Model
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

    // public function attributes()
    // {
    //     return $this->belongsToMany(Attribute::class, 'attribute_group_values');
    // }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attribute_group_values', 'attribute_group_id', 'attribute_id')
                    ->withPivot('attribute_id') // optional
                    ->withTimestamps();
    }
}
