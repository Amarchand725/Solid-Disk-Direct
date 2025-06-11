<?php

namespace App\Http\Controllers\Api;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;

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
        // $models = $this->model->where('is_featured', 1)->where('status', 1)->orderBy('id', 'desc')->get();
        $models = $this->model
                ->select('id', 'name', 'slug', 'logo') // adjust to your actual needed fields
                ->where('is_featured', 1)
                ->where('status', 1)
                ->orderByDesc('id')
                ->limit(10)
                ->get();

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
        // $models = $this->model->with('limitedProducts')->where('is_top', 1)
        //     ->where('status', 1)
        //     ->orderBy('id', 'desc')
        //     ->get();

        // foreach ($models as $brand) {
        //     $brand->limitedProducts = $brand->products()
        //         ->where('status', 1)
        //         ->orderByDesc('id')
        //         ->limit(4)
        //         ->get();
        // }

        $models = Brand::where('is_top', 1)
        ->where('status', 1)
        ->with(['limitedProducts' => function ($query) {
            $query->where('status', 1)
                ->orderByDesc('id');
        }])
        ->orderByDesc('id')
        ->limit(8)
        ->get()
        ->filter(function ($brand) {
            // Only include products with valid thumbnails (disk check is expensive)
            $validProducts = $brand->limitedProducts
                ->filter(function ($product) {
                    return !empty($product->thumbnail)
                        && Storage::disk('public')->exists($product->thumbnail);
                });

            if ($validProducts->isNotEmpty()) {
                $brand->setRelation('limitedProducts', $validProducts->take(4)->values());
                return true;
            }

            return false;
        })
        ->values(); // Reindex

        // $models = Brand::where('is_top', 1)
        // ->where('status', 1)
        // ->orderByDesc('id')
        // ->get()
        // ->filter(function ($brand) {
        //     // Get products first
        //     $products = $brand->limitedProducts()
        //         ->where('status', 1)
        //         ->orderByDesc('id')
        //         ->get()
        //         ->filter(function ($product) {
        //             return !empty($product->thumbnail) && Storage::disk('public')->exists($product->thumbnail);
        //         });
    
        //     // Only keep brands that have at least 1 product with valid thumbnail
        //     if ($products->isNotEmpty()) {
        //         // Set the relation with up to 4 valid products
        //         $brand->setRelation('limitedProducts', $products->take(4)->values());
        //         return true;
        //     }
    
        //     return false; // Exclude brand
        // })->values(); // Reindex the result

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
        $brand = $this->model->with('products')->where('slug', $brandSlug)->first();
        
        if(!empty($brand)){
            $query = $brand->products()
                ->with('hasBrand', 'hasProductCondition')
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
