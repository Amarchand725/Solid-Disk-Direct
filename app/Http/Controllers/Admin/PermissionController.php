<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\DataTableTrait;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    use DataTableTrait;

    protected $model;
    protected $routePrefix;
    protected $pathInitialize;
    protected $singularLabel;
    protected $pluralLabel;
    protected array $permissions;

    public function __construct(Permission $model)
    {
        $this->model = $model; 
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
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = $this->pluralLabel;
        $singularLabel = $this->singularLabel;
        $routeInitialize = $this->routePrefix;
        $bladePath = $this->pathInitialize;

        $models = $this->model->groupBy('label')
                    ->latest()
                    ->select(['id', 'label'])
                    ->get();

        // Define the columns dynamically
        $columns = [
            'label' => function ($model) {
                return '<span class="text-primary">' . Str::upper($model->label) . '</span>';
            },
            'permissions' => function ($model) use ($bladePath) {
                return view($bladePath.'.permissions', ['model' => $model])->render();
            },
            'created_at' => function ($model) {
                return Carbon::parse($model->created_at)->format('d, M Y');
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
        $menusWithoutPermissions = getNewMenus();
        $models = $this->model->orderby('id','DESC')->groupBy('label')->get();
        return (string) view($bladePath.'.create_content', get_defined_vars());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $singularLabel = $this->singularLabel;
        $this->validate($request, [
            'exist_permission_group' => 'required_without:new_permission_group',
            'new_permission_group' => 'required_without:exist_permission_group|unique:permissions,name',
            'custom_permission' => 'required_without:permissions',
            'permissions' => 'required_without:custom_permission|array|min:1',
        ]);

        DB::beginTransaction();

        try{
            $input_permissions = $request->permissions;
            if(!empty($request->custom_permission)){
                $input_permissions[] = str_replace([' ', '-'], '_', Str::lower($request->custom_permission));
            }

            $permission_group_label = '';
            if($request->exist_permission_group != ''){
                $permission_group = str_replace([' ', '-'], '_', Str::lower($request->exist_permission_group));
                $permission_group_label = Str::lower($request->exist_permission_group);
            }elseif($request->new_permission_group != ''){
                $permission_group = str_replace([' ', '-'], '_', Str::lower($request->new_permission_group));
                $permission_group_label = Str::lower($request->new_permission_group);
            }

            if(!empty($input_permissions)){
                foreach($input_permissions as $permission){
                    $this->model->create([
                        'label' =>  $permission_group_label,
                        'name' =>  $permission_group.'-'.$permission,
                        'guard_name' => 'web',
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' =>'You have added '.$singularLabel.' successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $bladePath = $this->pathInitialize;
        $models = $this->model->orderby('id','DESC')->groupBy('label')->get();
        $group_permission = $this->model->where('id', $id)->first();
        $all_group_sub_permissions = $this->model->where('label', $group_permission->label)->get();

        $permissions = [];
        $otherPermissions = [];

        foreach ($all_group_sub_permissions as $permission) {
            $nameParts = explode('-', $permission['name']);
            $permissionName = end($nameParts); // Get the last part of the exploded name

            // Check if the permission name belongs to a predefined set
            if (in_array($permissionName, ['list', 'create', 'show', 'edit', 'delete', 'status', 'trashed', 'restore'])) {
                $permissions[] = $permissionName;
            } else {
                $otherPermissions[] = $permissionName;
            }
        }

        return (string) view($bladePath.'.edit_content', get_defined_vars());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $permission_id)
    {
        $singularLabel = $this->singularLabel;
        $this->validate($request, [
            'exist_permission_group' => 'required_without:new_permission_group',
            'new_permission_group' => 'required_without:exist_permission_group|unique:permissions,id,'.$permission_id,
            'custom_permissions' => 'required_without:permissions',
            'permissions' => 'required_without:custom_permissions|array|min:1',
        ]);

        DB::beginTransaction();

        try{
            $input_permissions = $request->permissions;
            if (isset($request->custom_permissions) && $request->custom_permissions !== null && count($request->custom_permissions) > 0 && $request->custom_permissions[0] !== null) {
                foreach($request->custom_permissions as $custom_permission){
                    $input_permissions[] = str_replace([' ', '-'], '_', Str::lower($custom_permission));
                }
            }

            $permission_group_label = '';
            if($request->exist_permission_group != ''){
                $permission_group = str_replace([' ', '-'], '_', Str::lower($request->exist_permission_group));
                $permission_group_label = Str::lower($request->exist_permission_group);
            }elseif($request->new_permission_group != ''){
                $permission_group = str_replace([' ', '-'], '_', Str::lower($request->new_permission_group));
                $permission_group_label = Str::lower($request->new_permission_group);
            }

            if(!empty($input_permissions)){
                $permission_pre_name = $this->model->where('id', $permission_id)->first()->label;
                if($this->model->where('label', $permission_pre_name)->delete()){
                    foreach($input_permissions as $permission){
                        $this->model->create([
                            'label' =>  $permission_group_label,
                            'name' =>  $permission_group.'-'.$permission,
                            'guard_name' => 'web',
                        ]);
                    }

                    DB::commit();
                    return response()->json(['success' => true, 'message' =>'You have updated '.$singularLabel.' successfully.']);
                }else{
                    return response()->json(['error' => true, 'message' =>'Something went wrong.']);
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($permission_id)
    {
        $singularLabel = $this->singularLabel;
        $permission_pre_name = $this->model->where('id', $permission_id)->first()->label;
        if($this->model->where('label', $permission_pre_name)->delete()) {
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
