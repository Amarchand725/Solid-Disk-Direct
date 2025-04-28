<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    public function state(){
        return $this->hasOne(State::class,"id","state_id");
    }

    // City belongs to a Country THROUGH State
    public function country()
    {
        return $this->hasOneThrough(
            Country::class, // Final model we want to reach
            State::class,   // Intermediate model
            'id',           // Foreign key in states table (states.id)
            'id',           // Foreign key in countries table (countries.id)
            'state_id',     // Foreign key in cities table (cities.state_id)
            'country_id'    // Foreign key in states table (states.country_id)
        );
    }
}
