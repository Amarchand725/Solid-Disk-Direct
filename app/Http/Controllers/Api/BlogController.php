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

    public function index(Request $request){
        // $perPage = $request->get('per_page', 10);
        // $sortField = $request->get('sort_field', 'created_at');
        // $sortDirection = $request->get('sort_direction', 'desc');
        // $search = $request->get('search');

        // $query = $this->model->where('status', 1);

        // if ($search) {
        //     $query->where('title', 'like', "%$search%");
        // }

        // $query->orderBy($sortField, $sortDirection);

        // $blogs = $query->paginate($perPage);
        $perPage = $request->get('per_page', 10);
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $search = $request->get('search');

        $query = $this->model->where('status', 1);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%");
                // Add more fields as needed
            });
        }

        $query->orderBy($sortField, $sortDirection);

        $blogs = $query->paginate($perPage);
        
        if(!empty($blogs)){
            return response()->json([
                'status' => true,
                'message' => $blogs->count() > 0 ? 'Data found successfully.' : 'No data found.',
                'data' => $this->modelResource->collection($blogs), // transformed items
                'pagination' => [
                    'current_page' => $blogs->currentPage(),
                    'last_page' => $blogs->lastPage(),
                    'per_page' => $blogs->perPage(),
                    'total' => $blogs->total(),
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

    public function show($slug){
        $model = $this->model->where('slug', $slug)->first();

        // Get related products from the same main category
        $relatedBlogs = $this->model
        ->where('slug', '!=', $model->slug)
        ->inRandomOrder()
        ->take(5)
        ->get();

        $data = [
            'details' => new $this->modelResource($model),
            'related_blogs' => $this->modelResource->collection($relatedBlogs)
        ];

        if($model){
            return response()->json([
                'status'=>true,
                'message'=>'Data found successfully.',
                'data' => $data,
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
