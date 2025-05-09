<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\State;
use App\Models\City;

class LocationController extends Controller
{
    public function getCountries()
    {
        return response()->json(Country::all());
    }

    public function getStates($country_id)
    {
        return response()->json(State::where('country_id', $country_id)->get());
    }

    public function getCities($state_id)
    {
        return response()->json(City::where('state_id', $state_id)->get());
    }
}
