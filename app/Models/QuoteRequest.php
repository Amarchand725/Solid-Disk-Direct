<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

     // Accessor to combine first_name and last_name into full_name
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // Mutator to split full_name into first_name and last_name before saving
    public function setFullNameAttribute($value)
    {
        $names = explode(' ', $value);
        $this->attributes['first_name'] = $names[0];
        $this->attributes['last_name'] = $names[1] ?? ''; // Handle case where there might be no last name.
    }

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
