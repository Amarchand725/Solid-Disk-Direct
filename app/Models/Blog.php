<?php

namespace App\Models;

use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $plainTitle = strip_tags($model->title);
            $model->slug = Str::slug($plainTitle);
        });

        static::updating(function ($model) {
            $plainTitle = strip_tags($model->title);
            $model->slug = Str::slug($plainTitle);
        });
    }

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
