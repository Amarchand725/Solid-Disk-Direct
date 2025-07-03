<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function getCountry(){
        return $this->hasOne(Country::class, 'id', 'country');
    }
    public function getState(){
        return $this->hasOne(State::class, 'id', 'state');
    }
    public function getCity(){
        return $this->hasOne(City::class, 'id', 'city');
    }
}
