<?php

namespace App\Http\Controllers\Api;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SettingResource;

class SettingController extends Controller
{
    public function getSetting(){
        $setting = Setting::select([
            'name','support_email', 'currency_symbol', 'favicon', 
            'black_logo','address', 'country', 'phone_number', 
            'facebook_link', 'instagram_link', 'linked_in_link', 'twitter_link' 
        ])->first();
        if($setting){
            return response()->json([
                'status'=>true,
                'data' => new SettingResource($setting),
                'message'=>'Data found successfully.'
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'data'=>null,
                'message'=>'Data not found.'    
            ]);
        }
    }
}
