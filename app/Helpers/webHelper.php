<?php

use App\Models\Cart;
use App\Models\City;
use App\Models\State;
use App\Models\Country;
use Illuminate\Support\Str;

if (!function_exists('formatDate')) {
    function formatDate($date, $format = 'd-m-Y')
    {
        return \Carbon\Carbon::parse($date)->format($format);
    }
}

if (!function_exists('getAppName')) {
    function getAppName($title = '')
    {
        $baseTitle = config('app.name'); // Or use env('APP_NAME')
        return $title ? "$title - $baseTitle" : $baseTitle;
    }
}

if (!function_exists('getCartData')) {
    function getCartData()
    {
        $cart = Cart::with(['items.product' => function ($query) {
                    $query->select(['id', 'title', 'sku', 'provider_product_id', 'description', 'image_url', 'sale_price', 'price', 'min_price', 'max_price', 'category_name']);
                }])
                ->where('user_id', auth()->id())
                ->orWhere('guest_id', session()->getId())
                ->first();
        return $cart ? $cart->toArray() : [];
    }
}

if (!function_exists('countries')) {
    function countries()
    {
        return Country::get();
    }
}

if (!function_exists('states')) {
    function states($countryId)
    {
        $states = State::where('country_id', $countryId)->get();
        return $states ? $states->toArray() : [];
    }
}

if (!function_exists('cities')) {   
    function cities($stateId)
    {
        $cities = City::where('country_id', $stateId)->get();
        return $cities ? $cities->toArray() : [];
    }
}

if (!function_exists('getProductTitle')) {
    function getProductTitle($title, $max)
    {
        return Str::limit($title, $max);
    }
}