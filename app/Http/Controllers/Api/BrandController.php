<?php

namespace App\Http\Controllers\Api;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Http\Resources\ProductResource;

class BrandController extends Controller
{
    protected $model;
    protected $brandResource;
    protected $productResource;

    public function __construct(Brand $model)
    {
        $this->model = $model;
        $this->brandResource = new BrandResource(null); 
        $this->productResource = new ProductResource(null);
    }

    public function index(){
        $models = $this->model->with(['hasProducts' => function ($query) {
            $query->take(4);
        }])->where('status', 1)->orderBy('id', 'desc')->get();

        if ($models->count()) {
            return response()->json([
                'status' => true,
                'message' => 'Data found successfully.',
                'data' => $this->brandResource->collection($models)
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
                'data' => new $this->brandResource($model)
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'message'=>'Data not found.',    
                'data'=>null
            ]);
        }
    }
    public function featured(){
        $models = $this->model->where('is_featured', 1)->where('status', 1)->orderBy('id', 'desc')->paginate(10);

        if ($models->count()) {
            return response()->json([
                'status' => true,
                'message' => 'Data found successfully.',
                'data' => $this->brandResource->collection($models)
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No data found.',
                'data' => []
            ]);
        }
    }
    public function top(){
        // $models = $this->model->with('hasProducts')->where('is_top', 1)->where('status', 1)->orderBy('id', 'desc')->paginate(10);
        $models = $this->model
            ->with(['hasProducts' => function ($query) {
                $query->inRandomOrder()->limit(4);
            }])
            ->where('is_top', 1)
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->paginate(10);

        if ($models->count()) {
            return response()->json([
                'status' => true,
                'message' => 'Data found successfully.',
                'data' => $this->brandResource->collection($models)
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No data found.',
                'data' => []
            ]);
        }
    }

    public function productsByBrand(Request $request, $brandSlug)
    {
        $perPage = $request->get('per_page', 10);
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $search = $request->get('search');
        $category = $this->model->with('products')->where('slug', $brandSlug)->first();
        
        if(!empty($category)){
            $query = $category->products()
                ->with('hasBrand')
                ->where('status', 1);

            if ($search) {
                $query->where('name', 'like', "%$search%");
            }

            $products = $query->orderBy($sortField, $sortDirection)->paginate($perPage);

            return response()->json([
                'status' => true,
                'message' => 'Data found successfully.',
                'data' => $this->productResource->collection($products), // transformed items
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ]
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No data found.',
                'data' => [],
                'pagination' => null
            ]);
        }
    }
}
