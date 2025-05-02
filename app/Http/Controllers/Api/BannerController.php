<?php

namespace App\Http\Controllers\Api;

use App\Models\Banner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;

class BannerController extends Controller
{
    protected $model;
    protected $bannerResource;

    public function __construct(Banner $model)
    {
        $this->model = $model;
        $this->bannerResource = new BannerResource(null);
    }

    public function index(){
        $models = $this->model->where('status', 1)->orderBy('id', 'desc')->paginate(10);

        if ($models->count()) {
            return response()->json([
                'status' => true,
                'message' => 'Data found successfully.',
                'data' => $this->bannerResource->collection($models)
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