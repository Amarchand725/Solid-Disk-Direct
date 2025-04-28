<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function index(){
        $models = Product::where('status', 1)->orderby('id', 'desc')->get();
        if($models){
            return response()->json([
                'status'=>true,
                'data' => ProductResource::collection($models),
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
    public function show($slug){
        $model = Product::where('slug', $slug)->first();
        if($model){
            return response()->json([
                'status'=>true,
                'data' => new ProductResource($model),
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
