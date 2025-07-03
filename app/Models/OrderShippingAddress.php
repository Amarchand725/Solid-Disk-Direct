<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderShippingAddress extends Model
{
    use HasFactory;

    public function getState(){
        return $this->hasOne(State::class, 'id', 'state');
    }
    public function getCity(){
        return $this->hasOne(City::class, 'id', 'city');
    }
}
