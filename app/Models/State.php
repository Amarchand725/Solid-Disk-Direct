<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    public function country(){
        return $this->hasOne(Country::class,"id","country_id");
    }
    public function stateCity(){
        return $this->hasOne(City::class,"id","state_id");
    }

    public function stateCities(){
        return $this->hasMany(City::class,"id","state_id");
    }
}
