<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\AttributeGroup;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    protected $model;
    protected $productModel;
    protected $attributeGroupModel;
    protected $modelResource;
    protected $productResource;

    public function __construct(Category $model)
    {
        $this->model = $model;
        $this->attributeGroupModel = new AttributeGroup();
        $this->productModel = new Product();
        $this->modelResource = new CategoryResource(null);
        $this->productResource = new ProductResource(null);
    }

    public function index(){
        $models = $this->model->whereDoesntHave('parents') // Get root categories
                ->with('childrenRecursive')  
                ->latest()               // Eager load children recursively
                ->get();

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

    public function getCategories(){
        $models = $this->model->whereDoesntHave('parents')
            ->with('childrenRecursive')
            ->get()
            ->map(function ($item) {
                $item->children_recursive = collect($item->children_recursive)->unique('id')->values();
                return $item;
            });

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

    public function getGroups(){
        $groups = $this->attributeGroupModel
        ->with(['attributes.attributeValues']) // nested eager loading
        ->get();

        if ($groups->count()) {
            $cleanData = $groups->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'slug' => $group->slug,
                    'attributes' => $group->attributes->map(function ($attribute) {
                        return [
                            'id' => $attribute->id,
                            'name' => $attribute->name,
                            'slug' => $attribute->slug,
                            'attribute_values' => $attribute->attributeValues->map(function ($value) {
                                return [
                                    'id' => $value->id,
                                    'name' => $value->value,
                                ];
                            }),
                        ];
                    }),
                ];
            });
        }


        if ($groups->count()) {
            return response()->json([
                'status' => true,
                'message' => 'Data found successfully.',
                'data' => $cleanData
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

    public function featured(){
        $models = $this->model->where('is_featured', 1)->where('status', 1)->orderBy('id', 'desc')->paginate(10);

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
    public function top(){
        $models = $this->model
            ->with(['products' => function ($query) {
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

    public function productsByCategory(Request $request, $categorySlug)
    {
        $perPage = $request->get('per_page', 10);
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $search = $request->get('search');
        $category = $this->model->with('products')->where('slug', $categorySlug)->first();
        
        if(!empty($category)){
            $query = $category->products()
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
