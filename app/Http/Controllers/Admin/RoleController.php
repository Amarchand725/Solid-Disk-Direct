<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Route;
use App\Traits\DataTableTrait;

class RoleController extends Controller
{
    use DataTableTrait;

    protected $model;
    protected $routePrefix;
    protected $pathInitialize;
    protected $singularLabel;
    protected $pluralLabel;
    protected $permissionModel;
    protected array $permissions;

    /**
     * Applying permissions with all functions.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(Role $model)
    {
        $this->model = $model; 
        $this->permissionModel = new Permission(); 
        $this->routePrefix = Str::before(Route::currentRouteName(), '.');
        $this->pathInitialize = 'admin.'.$this->routePrefix;
        $this->singularLabel = Str::ucfirst(Str::singular($this->routePrefix));
        $this->pluralLabel = 'All '.Str::ucfirst($this->routePrefix);

        // Initialize the permissions array
        $this->permissions = [
            'index'  => $this->routePrefix . '-list',
            'create' => $this->routePrefix . '-create',
            'edit'   => $this->routePrefix . '-edit',
            'show'   => $this->routePrefix . '-show',
            'destroy' => $this->routePrefix . '-delete',
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = $this->pluralLabel;
        $singularLabel = $this->singularLabel;
        $routeInitialize = $this->routePrefix;
        $bladePath = $this->pathInitialize;

        $models = $this->model->orderby('id', 'desc')->select(['id', 'name', 'status', 'created_at'])->get();
        // Define the columns dynamically
        $columns = [
            'role' => function ($model) {
                return $model->name;
            },
            'status' => function ($model) use ($bladePath) {
                $label = '-';
                if($model->status==1){
                    $label = '<span class="badge bg-label-success me-1">Active</span>';
                }elseif($model->status==0){
                    $label = '<span class="badge bg-label-danger me-1">De-Active</span>';
                }

                return $label;
            },
            'created_at' => function ($model) {
                if(!empty($model->created_at)){
                    return date('d, M Y | h:i A', strtotime($model->created_at));
                }else{
                    return '-';
                }
            },
            'action' => function ($model) use ($bladePath, $singularLabel, $routeInitialize) {
                return view($bladePath.'.action', ['model' => $model, 'singularLabel' => $singularLabel, 'routeInitialize' => $routeInitialize])->render();
            }
        ];
        
        if ($request->ajax() && $request->loaddata == "yes") {
            return $this->getDataTable($request, $models, $columns);
        }

        $columnsConfig = collect($columns)->map(function ($callback, $key) {
            return [
                'data' => $key,
                'name' => $key,
                'orderable' => !in_array($key, ['action']), // Set orderable=false for 'action'
                'searchable' => !in_array($key, ['action']) // Set searchable=false for 'action'
            ];
        })->values()->toArray();
        
        return view($bladePath.'.index', get_defined_vars());
    }
    public function create(){
        $bladePath = $this->pathInitialize;
        $title = $this->singularLabel;
        $models = $this->permissionModel->orderby('id','DESC')->groupBy('label')->get();
        return view($bladePath.'.create', get_defined_vars());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $routeInitialize = $this->routePrefix;
        $singularLabel = $this->singularLabel;
        $this->validate($request, [
            'name' => ['required', 'unique:roles', 'max:100'],
        ]);

        DB::beginTransaction();

        try{
            $role = $this->model->create([
                'name' => $request->name,
                'guard_name' => 'web'
            ]);
            if($request->input('permissions') != ''){
                $role->syncPermissions($request->input('permissions'));
            }

            if($role){
                DB::commit();
                return response()->json(['success' => true, 'message' =>'You have added '.$singularLabel.' successfully.', 'route' => route($routeInitialize.'.index')]);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show details the specified resource.
     */
    public function show($id)
    {
        $bladePath = $this->pathInitialize;
        $model = $this->model->findOrFail($id);
        $permissions = $model->permissions()->pluck('name')->toArray();
        $groupedPermissions = groupPermissions($permissions);
        return (string) view($bladePath.'.show_content', get_defined_vars());
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $bladePath = $this->pathInitialize;
        $title = $this->singularLabel;
        $role = $this->model->where('id', $id)->first();
        $permissions = $this->permissionModel->orderby('id','DESC')->groupBy('label')->get();
        return view($bladePath.'.edit', get_defined_vars());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $role_id)
    {
        $routeInitialize = $this->routePrefix;
        $singularLabel = $this->singularLabel;
        $this->validate($request, [
            'name' => 'required|max:150|unique:roles,id,'.$role_id,
        ]);

        DB::beginTransaction();

        try{
            $role = $this->model->where('id', $role_id)->first();
            $role->name = $request->name;
            $role->save();
            $role->syncPermissions($request->input('permissions'));

            if($role){
                DB::commit();
                return response()->json(['success' => true, 'message' =>'You have updated '.$singularLabel.' successfully.', 'route' => route($routeInitialize.'.index')]);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $singularLabel = $this->singularLabel;
        if($this->model->find($id)->delete()) {
            return response()->json([
                'status' => true,
                'message' => $singularLabel.' Deleted Successfully'
            ]);
        } else{
            return response()->json([
                'status' => true,
                'error' => $singularLabel.' not deleted try again.'
            ]);
        }
    }
}
