<?php

namespace App\Http\Controllers\Admin;

use App\Models\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ClientBoarding;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class LogController extends Controller
{
    public function index(Request $request){
        $this->authorize('logs-list');
        $title = "Log's List";
        $logs = Log::orderby('id', 'desc')->get();
        if($request->ajax() && $request->loaddata == "yes"){
            return DataTables::of($logs)
            ->addIndexColumn()
            ->editColumn('user_id', function($model){
                if(isset($model->hasActionUser) && !empty($model->hasActionUser->name)){
                    return $model->hasActionUser->name.' ('. getRole($model->hasActionUser->id) .')';
                }else{
                    return '-';
                }
            })
            ->addColumn('action_type', function($model){
                return actionLabel($model->action);
            })
            ->editColumn('model', function($model){
                $modelClass = $model->model;
                $className = class_basename($modelClass);
                return $className ?? '-';
            })
            ->editColumn('description', function($model){
                return $model->description ?? '-';
            })
            ->editColumn('ip_address', function($model){
                return $model->ip_address ?? '-';
            })
            ->editColumn('created_at', function($model){
                if(!empty($model->created_at)){
                    return newDateFormat($model->created_at) ?? '-';
                }else{
                    return '-';
                }
            })
            ->addColumn('action', function($model){
                return view('admin.logs.action', ['model' => $model])->render();
            })
            ->rawColumns(['action', 'action_type', 'created_date', 'action'])
            ->make(true);
        }
        return view('admin.logs.index', get_defined_vars());
    }

    // public function store(Request $request)
    // {
    //     $model = ClientBoarding::where('id', $request->target_id)->first();
    //     if($request->action_type=='downloaded'){
    //         $model['document_path'] = 'admin/assets/upload/clients';
    //         logDownloadDocument($model, $request->target_type);
    //         return response()->json(['message' => 'Document downloaded successfully.']);
    //     }else{
    //         logShowColumn($model, $request->target_type);
    //         return response()->json(['message' => 'Document view successfully.']);
    //     }
    // }

    public function show(string $id)
    {
        $title = 'Log Details';
        $model = Log::where('id', $id)->first();
        $modelClass = $model->model;

        // Get the class name without namespace
        $className = class_basename($modelClass);
        if(!empty($model)){
            $modelData = $model->model::where('id', $model->model_id)->withTrashed()->first();
            // Get all attributes of the model
            $attributes = $modelData->getAttributes();

            // Filter out keys ending with '_id'
            $modelData = collect($attributes)->filter(function ($value, $key) {
                return !\Illuminate\Support\Str::endsWith($key, '_id'); // Exclude columns ending with '_id'
            });
            return view('admin.logs.show', get_defined_vars());
        }else{
            return abort(405);
        }
    }
}
