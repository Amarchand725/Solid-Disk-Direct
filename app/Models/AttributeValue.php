<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_attributes');
    }

    public function attributeGroup()
    {
        return $this->hasOneThrough(
            AttributeGroup::class,
            AttributeGroupValue::class,
            'attribute_id',       // Foreign key on attribute_group_values table...
            'id',                 // Local key on attribute_groups table
            'attribute_id',       // Local key on attribute_values table
            'attribute_group_id'  // Foreign key on attribute_group_values table
        );
    }
}