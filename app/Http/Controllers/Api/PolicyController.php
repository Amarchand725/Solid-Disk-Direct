<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Policy;
use App\Http\Controllers\Controller;
use App\Http\Resources\PolicyResource;

class PolicyController extends Controller
{
    protected $model;
    protected $modelResource;

    public function __construct(Policy $model)
    {
        $this->model = $model;
        $this->modelResource = new PolicyResource(null);
    }

    public function policies($slug){
        $model = $this->model->where('status', 1)->where('slug', $slug)->first();

        if (!empty($model)) {
            return response()->json([
                'status' => true,
                'message' => 'Data found successfully.',
                'data' => new $this->modelResource($model)
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
