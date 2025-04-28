<?php

namespace App\Http\Controllers\Api;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TestimonialResource;

class TestimonialController extends Controller
{
    public function index(){
        $models = Testimonial::where('status', 1)->orderby('id', 'desc')->get();
        if($models){
            return response()->json([
                'status'=>true,
                'data' => TestimonialResource::collection($models),
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
