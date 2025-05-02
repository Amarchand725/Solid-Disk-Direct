<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\RecentViewProduct;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    protected $model;
    protected $productResource;

    public function __construct(Product $model)
    {
        $this->model = $model;
        $this->productResource = new ProductResource(null);
    }

    public function index(){
        $models = $this->model->where('status', 1)->orderBy('id', 'desc')->paginate(10);

        if ($models->count()) {
            return response()->json([
                'status' => true,
                'message' => 'Data found successfully.',
                'data' => $this->productResource->collection($models)
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No data found.',
                'data' => []
            ]);
        }
    }
    public function featured(){
        $models = $this->model->where('is_featured', 1)->where('status', 1)->orderBy('id', 'desc')->paginate(10);

        if ($models->count()) {
            return response()->json([
                'status' => true,
                'message' => 'Data found successfully.',
                'data' => $this->productResource->collection($models)
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No data found.',
                'data' => []
            ]);
        }
    }

    public function recentViewed(){
        $recentViewed = RecentViewProduct::latest()->take(20)->pluck('product')->toArray();
        $models = $this->model->whereIn('slug', $recentViewed)->orderBy('id', 'desc')->paginate(10);

        if ($models->count()) {
            return response()->json([
                'status' => true,
                'message' => 'Data found successfully.',
                'data' => $this->productResource->collection($models)
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
            $this->storeRecentViewProduct($slug);
            
            return response()->json([
                'status'=>true,
                'message'=>'Data found successfully.',
                'data' => new $this->productResource($model)
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'message'=>'Data not found.',    
                'data'=>null
            ]);
        }
    }

    public function storeRecentViewProduct($slug){
        $model = new RecentViewProduct();
        $model->product = $slug;
        $model->customer = auth()->check() ? auth()->id() : null;
        $model->guest = auth()->check() ? null : session()->getId();
        $model->save();

        return true;
    }
}
