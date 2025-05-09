<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\RecentViewProduct;
use Illuminate\Support\Facades\DB;
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
        $models = $this->model->with('mainCategory')->where('status', 1)->orderBy('id', 'desc')->paginate(10);

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
    public function bestSelling(){
        // $bestSellingProducts = $this->model->select('products.*', DB::raw('SUM(order_product.quantity) as total_sold'))
        // ->join('order_items', 'products.slug', '=', 'order_items.product_slug')
        // ->groupBy('products.id')
        // ->orderByDesc('total_sold')
        // ->take(10) // Top 10 best-sellers
        // ->get();

        $bestSellingProduct = $this->model->inRandomOrder()->first();

        if ($bestSellingProduct) {
            return response()->json([
                'status' => true,
                'message' => 'Data found successfully.',
                'data' => new $this->productResource($bestSellingProduct)
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No data found.',
                'data' => []
            ]);
        }
    }
    public function topRated(){
        $topRatedProducts = Product::select('products.*', DB::raw('AVG(product_reviews.rating) as avg_rating'), DB::raw('COUNT(product_reviews.id) as review_count'))
        ->join('product_reviews', 'products.id', '=', 'product_reviews.product_id')
        ->where('product_reviews.approved', true) // Optional, if you use moderation
        ->groupBy('products.id')
        ->havingRaw('COUNT(product_reviews.id) >= 5') // Ensure at least 5 reviews
        ->orderByDesc('avg_rating') // Highest average rating first
        ->orderByDesc('review_count') // Optional: prioritize those with more reviews
        ->take(10) // Limit to top 10
        ->get();

        if ($topRatedProducts->count()) {
            return response()->json([
                'status' => true,
                'message' => 'Data found successfully.',
                'data' => $this->productResource->collection($topRatedProducts)
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No data found.',
                'data' => []
            ]);
        }
    }

    public function getCategoryTrailFromRelations(Category $category)
    {
        $trail = [];

        while ($category) {
            array_unshift($trail, [
                'name' => $category->name,
                'slug' => $category->slug,
            ]);

            // Load first parent (assuming only 1 parent per node for trail)
            $category = $category->parents()->first();
        }

        return $trail;
    }

    public function show($categorySlugChain, $slug)
    {
        $model = $this->model->with('mainCategory')->where('slug', $slug)->first();

        if (!$model) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.',
                'data' => null
            ]);
        }

        // Get the actual category trail from product's main category
        $categoryTrail = $this->getCategoryTrailFromRelations($model->mainCategory);
        $correctCategoryPath = implode('/', array_column($categoryTrail, 'slug'));

        // Compare the path from the URL with the actual path
        $givenCategoryPath = trim($categorySlugChain, '/');

        if ($givenCategoryPath !== $correctCategoryPath) {
            // Redirect to correct URL
            return redirect()->to("/$correctCategoryPath/{$model->slug}", 301);
        }

        // Get related products from the same main category
        $relatedProducts = $model->mainCategory->products()
            ->where('products.id', '!=', $model->id)
            ->latest()
            ->take(10)
            ->get();

        $this->storeRecentViewProduct($slug);

        $data = [
            // 'categoryTrail' => $categoryTrail,
            'details' => new $this->productResource($model),
            'related_products' => $this->productResource->collection($relatedProducts)
        ];

        return response()->json([
            'status' => true,
            'message' => 'Data found successfully.',
            'data' => $data,
        ]);
    }


    // public function show($slug){
    //     $model = $this->model->with('mainCategory')->where('slug', $slug)->first();
    //     // $lastCategory = $model->categories->last(); // Get last category (based on order, not created_at)

    //     $categoryTrail = $this->getCategoryTrailFromRelations($model->mainCategory);
    //     // Now get other products in the same category
    //     $relatedProducts = collect();

    //     if ($model) {
    //         $relatedProducts = $model->mainCategory->products()
    //             ->where('products.id', '!=', $model->id) // Exclude the current product
    //             ->latest() // Optional: order by latest
    //             ->take(10)  // Optional: limit results
    //             ->get();
        
    //         $this->storeRecentViewProduct($slug);

    //         $data = [
    //             'categoryTrail' => $categoryTrail,
    //             'details' => new $this->productResource($model),
    //             'related_products' => $this->productResource->collection($relatedProducts)
    //         ];
            
    //         return response()->json([
    //             'status'=>true,
    //             'message'=>'Data found successfully.',
    //             'data' => $data,
    //         ]);
    //     }else{
    //         return response()->json([
    //             'status'=>false,
    //             'message'=>'Data not found.',    
    //             'data'=>null
    //         ]);
    //     }
    // }

    public function storeRecentViewProduct($slug){
        $model = new RecentViewProduct();
        $model->product = $slug;
        $model->customer = auth()->check() ? auth()->id() : null;
        $model->guest = auth()->check() ? null : session()->getId();
        $model->save();

        return true;
    }

    public function search(Request $request)
    {
        $query = $this->model->query();

        if (!$request->filled('keyword')) {
            return response()->json([
                'status' => false,
                'message' => 'Please provide a search keyword.',
                'data' => []
            ]);
        }
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where('title', 'like', "%{$keyword}%")
                ->orWhere('short_description', 'like', "%{$keyword}%")
                ->orWhere('sku', 'like', "%{$keyword}%")
                ->orWhere('unit_price', 'like', "%{$keyword}%");
        }

        if ($request->filled('category_slug')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.slug', $request->category_slug);
            });
        }

        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        }

        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        // Add relationships or filters as needed
        $models = $query->get();

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
}
