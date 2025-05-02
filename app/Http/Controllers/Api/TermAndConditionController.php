<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\TermAndCondition;
use App\Http\Controllers\Controller;
use App\Http\Resources\TermAndConditionResource;

class TermAndConditionController extends Controller
{
    protected $model;
    protected $modelResource;

    public function __construct(TermAndCondition $model)
    {
        $this->model = $model;
        $this->modelResource = new TermAndConditionResource(null);
    }

    public function index(){
        $models = $this->model->where('status', 1)->orderBy('id', 'desc')->get();

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
}
