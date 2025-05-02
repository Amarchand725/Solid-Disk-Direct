<?php

namespace App\Http\Controllers\Api;

use App\Models\Blog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BlogResource;

class BlogController extends Controller
{
    protected $model;
    protected $modelResource;

    public function __construct(Blog $model)
    {
        $this->model = $model;
        $this->modelResource = new BlogResource(null);
    }

    public function index(){
        $models = $this->model->where('status', 1)->orderBy('id', 'desc')->get();

        if ($models->count()) {
            return response()->json([
                'status' => true,
                'message' => 'Data found successfully.',
                'data' => $this->modelResource->collection($models)
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No data found.',
                'data' => []
            ]);
        }
    }
    public function show($slug){
        $model = $this->model->where('slug', $slug)->first();
        if($model){
            return response()->json([
                'status'=>true,
                'message'=>'Data found successfully.',
                'data' => new $this->modelResource($model)
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'message'=>'Data not found.',    
                'data'=>null
            ]);
        }
    }
}
