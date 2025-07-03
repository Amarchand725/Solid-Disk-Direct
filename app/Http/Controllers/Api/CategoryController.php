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
        // $categories = $this->model
        //     ->where('is_top', 1)
        //     ->where('status', 1)
        //     ->with('childrenRecursive')
        //     ->get();

        // $processed = $categories->map(function ($category) {
        //     // Step 1: Gather all category IDs recursively
        //     $allCategoryIds = collect([$category->id]);

        //     $gatherIds = function ($children) use (&$gatherIds, &$allCategoryIds) {
        //         foreach ($children as $child) {
        //             $allCategoryIds->push($child->id);
        //             if ($child->childrenRecursive) {
        //                 $gatherIds($child->childrenRecursive);
        //             }
        //         }
        //     };

        //     $gatherIds($category->childrenRecursive);

        //     // Step 2: Query products for all collected category IDs
        //     $products = Product::whereIn('category', $allCategoryIds)
        //         ->where('status', 1)
        //         ->whereNotNull('unit_price')
        //         ->where('unit_price', '>', 0)
        //         ->orderByDesc('unit_price')
        //         ->get()
        //         ->filter(function ($product) {
        //             return $product->thumbnail && Storage::disk('public')->exists($product->thumbnail);
        //         });

        //     if ($products->isEmpty()) {
        //         return null; // skip this category if no valid products
        //     }

        //     // Step 3: Attach products + price info
        //     $category->setRelation('limitedProducts', $products->take(4)->values());
        //     $category->max_unit_price = $products->max('unit_price');

        //     return $category;
        // })->filter()->values(); // remove nulls

        // $finalCategories = $processed->sortByDesc('max_unit_price')->take(10)->values();


        $categories = $this->model
            ->where('is_top', 1)
            ->where('status', 1)
            ->get();

        // Step 1: Filter categories by valid products (unit_price > 0 + thumbnail exists)
        $filtered = $categories->map(function ($category) {
            $products = $category->limitedProducts()
                ->orderByDesc('unit_price') // Sort by price (highest first)
                ->get()
                ->filter(function ($product) {
                    return $product->thumbnail && Storage::disk('public')->exists($product->thumbnail);
                });

            if ($products->isNotEmpty()) {
                $category->setRelation('limitedProducts', $products->take(4)->values());
                $category->max_unit_price = $products->max('unit_price'); // attach max price for sorting
                return $category;
            }

            return null;
        })->filter()->values(); // remove nulls, reindex

        // Step 2: Sort by max product unit price DESC
        $finalCategories = $filtered->sortByDesc('max_unit_price')->take(10)->values();

        if ($finalCategories->count()) {
            return response()->json([
                'status' => true,
                'message' => 'Data found successfully.',
                'data' => $this->modelResource->collection($finalCategories)
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
        // $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $search = $request->get('search');

        $category = $this->model->with('children')->where('slug', $categorySlug)->first();
        // $categoryIds = [];
        // if (isset($category->children) && !empty($category->children) && $category->children->count() > 0) {
        //     // Get child category IDs
        //     $categoryIds = $category->children->pluck('id')->toArray();
        // } else {
        //     // Fallback to current category ID
        //     $categoryIds = [$category->id];
        // }
        $categoryIds = [$category->id];

        if (!empty($category->children) && $category->children->count() > 0) {
            $categoryIds = array_merge(
                $categoryIds,
                $category->children->pluck('id')->toArray()
            );
        }

        if (!empty($categoryIds)) {
            // Query products from those categories
            $query = Product::with('hasBrand', 'hasProductCondition')
                ->whereIn('category', $categoryIds)
                ->where('status', 1);

            if ($search) {
                $query->where('name', 'like', "%$search%");
            }

            $products = $query->paginate($perPage);
            $productsCollection = $products->getCollection();

            $productsCollection = $productsCollection->sortByDesc(function ($product) {
                $thumbnail = ltrim($product->thumbnail, '/');
                $path = public_path($thumbnail);
                return !empty($product->thumbnail) && file_exists($path);
            });

            $products->setCollection($productsCollection->values());

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
    }
}
