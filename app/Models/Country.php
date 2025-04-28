<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    public function state(){
        return $this->hasOne(State::class,"id","state_id");
    }

    public function states(){
        return $this->hasMany(State::class,"id","state_id");
    }

    // Country has many Cities THROUGH States
    public function cities()
    {
        return $this->hasManyThrough(
            City::class,
            State::class,
            'country_id',  // Foreign key in states table (states.country_id)
            'state_id',    // Foreign key in cities table (cities.state_id)
            'id',          // Primary key in countries table (countries.id)
            'id'           // Primary key in states table (states.id)
        );
    }
}
