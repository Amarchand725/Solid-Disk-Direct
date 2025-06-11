<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\AttributeGroup;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\Category2Resource;

class CategoryController extends Controller
{
    protected $model;
    protected $productModel;
    protected $attributeGroupModel;
    protected $modelResource;
    protected $modelResource2;
    protected $productResource;

    public function __construct(Category $model)
    {
        $this->model = $model;
        $this->attributeGroupModel = new AttributeGroup();
        $this->productModel = new Product();
        $this->modelResource = new CategoryResource(null);
        $this->modelResource2 = new Category2Resource(null);
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
            ->orderby('priority', 'desc')
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
            ->with(['attributes.attributeValues'])
            ->get();

        if ($groups->count()) {
            $cleanData = $groups->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'slug' => $group->slug,
                    'attributes' => $group->attributes->map(function ($attribute) use ($group) {
                        return [
                            'id' => $attribute->id,
                            'name' => $attribute->name,
                            'slug' => $attribute->slug,
                            'attribute_values' => $attribute->attributeValues->map(function ($value) use ($group, $attribute) {
                                return [
                                    'id' => $value->id,
                                    'name' => $value->value,
                                    'slug_path' => "{$group->slug}/{$attribute->slug}/{$value->value}", // build URL path here
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

    // public function getGroups(){
    //     $groups = $this->attributeGroupModel
    //     ->with(['attributes.attributeValues']) // nested eager loading
    //     ->get();

    //     if ($groups->count()) {
    //         $cleanData = $groups->map(function ($group) {
    //             return [
    //                 'id' => $group->id,
    //                 'name' => $group->name,
    //                 'slug' => $group->slug,
    //                 'attributes' => $group->attributes->map(function ($attribute) {
    //                     return [
    //                         'id' => $attribute->id,
    //                         'name' => $attribute->name,
    //                         'slug' => $attribute->slug,
    //                         'attribute_values' => $attribute->attributeValues->map(function ($value) {
    //                             return [
    //                                 'id' => $value->id,
    //                                 'name' => $value->value,
    //                             ];
    //                         }),
    //                     ];
    //                 }),
    //             ];
    //         });
    //     }

    //     if ($groups->count()) {
    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Data found successfully.',
    //             'data' => $cleanData
    //         ]);
    //     } else {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'No data found.',
    //             'data' => []
    //         ]);
    //     }
    // }

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
        // $models = $this->model->where('is_featured', 1)->where('status', 1)->orderBy('id', 'desc')->get();
        $models = $this->model
                ->select('id', 'name', 'slug', 'banner') // use only necessary fields
                ->where('is_featured', 1)
                ->where('status', 1)
                ->orderBy('id', 'desc')
                ->limit(10)
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
    public function top(){
        $models = $this->model->with('limitedProducts')->where('is_top', 1)
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($models as $category) {
            $category->limitedProducts = $category->products()
                ->where('status', 1)
                ->orderByDesc('id')
                ->limit(4)
                ->get();
        }

        // $models = $this->model
        // ->where('is_top', 1)
        // ->where('status', 1)
        // ->with(['limitedProducts' => function ($query) {
        //     $query->where('status', 1)
        //         ->orderByDesc('id');
        // }])
        // ->orderByDesc('id')
        // ->get()
        // ->filter(function ($category) {
        //     // Filter products with valid thumbnails
        //     $validProducts = $category->limitedProducts
        //         ->filter(function ($product) {
        //             return !empty($product->thumbnail)
        //                 && Storage::disk('public')->exists($product->thumbnail);
        //         });

        //     // Only keep categories with at least one valid product
        //     if ($validProducts->isNotEmpty()) {
        //         $category->setRelation('limitedProducts', $validProducts->take(4)->values());
        //         return true;
        //     }

        //     return false;
        // })
        // ->values(); // Reindex the collection

        // $models = $this->model->where('is_top', 1)
        // ->where('status', 1)
        // ->orderByDesc('id')
        // ->get()
        // ->filter(function ($category) {
        //     // Get products first
        //     $products = $category->limitedProducts()
        //         ->where('status', 1)
        //         ->orderByDesc('id')
        //         ->get()
        //         ->filter(function ($product) {
        //             return !empty($product->thumbnail) && Storage::disk('public')->exists($product->thumbnail);
        //         });
    
        //     // Only keep brands that have at least 1 product with valid thumbnail
        //     if ($products->isNotEmpty()) {
        //         // Set the relation with up to 4 valid products
        //         $category->setRelation('limitedProducts', $products->take(4)->values());
        //         return true;
        //     }
    
        //     return false; // Exclude brand
        // })->values(); // Reindex the result

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

        if (!empty($category)) {
            $categoryIds = $category->children->pluck('id')->toArray();

            // Query products from those categories
            $query = Product::with('hasBrand', 'hasProductCondition')
                ->whereIn('category', $categoryIds)
                ->where('status', 1);

            if ($search) {
                $query->where('name', 'like', "%$search%");
            }

            $products = $query->orderBy($sortField, $sortDirection)->paginate($perPage);

            return response()->json([
                'status' => true,
                'message' => 'Data found successfully.',
                'data' => $this->productResource->collection($products),
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
        
        // if(!empty($category)){
        //     $query = $category->products()
        //         ->with('hasBrand', 'hasProductCondition')
        //         ->where('status', 1);

        //     if ($search) {
        //         $query->where('name', 'like', "%$search%");
        //     }

        //     $products = $query->orderBy($sortField, $sortDirection)->paginate($perPage);

        //     return response()->json([
        //         'status' => true,
        //         'message' => 'Data found successfully.',
        //         'data' => $this->productResource->collection($products), // transformed items
        //         'pagination' => [
        //             'current_page' => $products->currentPage(),
        //             'last_page' => $products->lastPage(),
        //             'per_page' => $products->perPage(),
        //             'total' => $products->total(),
        //         ]
        //     ]);
        // } else {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'No data found.',
        //         'data' => [],
        //         'pagination' => null
        //     ]);
        // }
    }
}
