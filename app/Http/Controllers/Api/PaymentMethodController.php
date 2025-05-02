<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentMethodResource;

class PaymentMethodController extends Controller
{
    protected $model;
    protected $modelResource;

    public function __construct(PaymentMethod $model)
    {
        $this->model = $model;
        $this->modelResource = new PaymentMethodResource(null);
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
