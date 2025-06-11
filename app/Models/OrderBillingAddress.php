<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderBillingAddress extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getState(){
        return $this->hasOne(State::class, 'id', 'state');
    }
    public function getCity(){
        return $this->hasOne(City::class, 'id', 'city');
    }
}
