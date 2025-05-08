<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use App\Models\Menu;
use App\Models\Brand;
use App\Models\MenuField;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\DataTableTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BrandsImport;

class BrandController extends Controller
{
    use DataTableTrait;
    
    protected $model;
    protected $routePrefix;
    protected $pathInitialize;
    protected $singularLabel;
    protected $pluralLabel;
    protected array $permissions;

    public function __construct(Brand $model)
    {
        parent::__construct();
        
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
            'trashed' => $this->routePrefix . '-trashed',
            'restore' => $this->routePrefix . '-restore',
            'import' => $this->routePrefix . '-import',
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

        // Get column definitions dynamically
        $getFields = getFields($this->model, getFieldsAndColumns($this->model, $this->pathInitialize, $this->singularLabel, $this->routePrefix), 'index');
        
        // Check and handle relation
        if (isset($getFields['logo'])) {
            $getFields = ['logo' => $getFields['logo']] + $getFields;
        }

        // Reorder is_featured and is_top after 'name'
        if (isset($getFields['is_featured']) && isset($getFields['is_top']) && isset($getFields['name'])) {
            $reorderedFields = [];
            foreach ($getFields as $key => $value) {
                $reorderedFields[$key] = $value;

                if ($key === 'name') {
                    $reorderedFields['is_featured'] = $getFields['is_featured'];
                    $reorderedFields['is_top'] = $getFields['is_top'];
                }
            }   
            $getFields = $reorderedFields;
        }
        
        if (isset($getFields['is_featured'])) {
            // Customize index to pull from relation
            $getFields['is_featured']['index'] = fn($model) => $model->is_featured==1 ? '<span class="badge bg-success">Yes</span>'
            : '<span class="badge bg-danger">No</span>';
        }
        if (isset($getFields['is_top'])) {
            // Customize index to pull from relation
            $getFields['is_top']['index'] = fn($model) => $model->is_top==1 ? '<span class="badge bg-success">Yes</span>'
            : '<span class="badge bg-danger">No</span>';
        }

        //select columns
        $selectedColumns = collect($getFields)
        ->mapWithKeys(function ($config, $key) {
            return [$key => $config['index']];
        })
        ->keys()
        ->filter(function ($key) {
            return $key !== 'action'; // Remove 'action'
        })
        ->values() // Reindex the array
        ->toArray();
    
        // Optionally prepend 'id'
        array_unshift($selectedColumns, 'id');
        
        $models = $this->model->latest()
            ->with('createdBy:id,name')
            ->select($selectedColumns);
        //select columns
        
        $columns = collect($getFields)->mapWithKeys(function ($config, $key) {
            return [$key => $config['index']];
        })->toArray();  // Convert Collection to Array
        
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

        $model = $this->model;
        $fields = getFields($this->model, getFieldsAndColumns($this->model, $this->pathInitialize, $this->singularLabel, $this->routePrefix), 'create');
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
        $fields = getFieldsAndColumns($this->model, $this->pathInitialize, $this->singularLabel, $this->routePrefix); // getFieldsAndColumns() returns dynamic field definitions

        // Step 1: Build dynamic validation rules
        $rules = buildValidationRules($fields, null, $request);

        // Step 2: Validate
        $validated = $request->validate($rules);

        DB::beginTransaction();

        try{
            $saved = new $this->model;

            // Step 3: Dynamically assign fields
            foreach ($fields as $field => $config) {
                if($field != 'created_at' && $field != 'action'){
                    if (isset($config['type']) && $config['type'] === 'file' && $request->hasFile($field)) {
                        $uploadPath = $uploadPath = Str::plural(Str::lower($this->singularLabel));
                        $saved->$field = $request->file($field)->store('uploads/'.$uploadPath, 'public');
                    } else {
                        if($field=='created_by'){
                            $saved->$field = auth()->id() ?? null;
                        }elseif($field=='status'){
                            $saved->$field = $validated[$field] ?? 1;
                        }else{
                            $saved->$field = $validated[$field] ?? null;
                        }
                    }
                }

                $saved->save();
            }

            if(isset($saved) && !empty($saved)){
                DB::commit();
                return response()->json(['success' => true, 'message' =>'You have added '.$singularLabel.' successfully.']);
            }else{
                DB::rollback();
                return response()->json(['success' => false, 'message' =>'You have not added '.$singularLabel.' successfully.']);
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
        $fields = getFields($model, getFieldsAndColumns($this->model, $this->pathInitialize, $this->singularLabel, $this->routePrefix), 'show');
        return (string) view($bladePath.'.show_content', get_defined_vars());
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $bladePath = $this->pathInitialize;
        $title = $this->singularLabel;
        $model = $this->model->where('id', $id)->first();
        $fields = getFields($model, getFieldsAndColumns($this->model, $this->pathInitialize, $this->singularLabel, $this->routePrefix), 'edit');
        
        return view($bladePath.'.edit_content', get_defined_vars());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $modelId)
    {
        $model = $this->model->where('id', $modelId)->first();
        $singularLabel = $this->singularLabel;
        $fields = getFieldsAndColumns($this->model, $this->pathInitialize, $this->singularLabel, $this->routePrefix); // getFieldsAndColumns() returns dynamic field definitions

        // Step 1: Build dynamic validation rules
        $rules = buildValidationRules($fields, $model, $request);

        // Step 2: Validate
        $validated = $request->validate($rules);

        DB::beginTransaction();

        try{
            // Step 1: Dynamically assign fields
            foreach ($fields as $field => $config) {
                if($field != 'created_at' && $field != 'action'){
                    // If the field is an image and a new file is uploaded
                    if (isset($config['type']) && $config['type'] === 'file') {
                        // Step 2: Delete old file if it exists
                        if($request->hasFile($field)){
                            if ($model->$field) {
                                $oldImagePath = storage_path('app/uploads/'.$this->pluralLabel.'/'.$model->$field);
                                if (file_exists($oldImagePath)) {
                                    unlink($oldImagePath); // Delete the old image
                                }
                            }

                            // Step 3: Store the new image
                            $uploadPath = Str::plural(Str::lower($this->singularLabel));
                            $model->$field = $request->file($field)->store('uploads/'.$uploadPath, 'public');
                        }
                    } else {
                        // For other fields, just assign the validated value
                        if($field=='created_by'){
                            $model->$field = auth()->id() ?? null;
                        }else{
                            $model->$field = $validated[$field] ?? null;
                        }
                    }
                }
                $model->save();
            }

            if(isset($model) && !empty($model)){
                DB::commit();
                return response()->json(['success' => true, 'message' =>'You have updated '.$singularLabel.' successfully.']);
            }else{
                DB::rollback();
                return response()->json(['success' => false, 'message' =>'You have not updated '.$singularLabel.' successfully.']);
            }
        } catch (Exception $e) {
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
    public function destroy($modelId)
    {
        $singularLabel = $this->singularLabel;
        if($this->model->where('id', $modelId)->delete()) {
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

    public function trashed(Request $request)
    {
        $singularLabel = $this->singularLabel;
        $routeInitialize = $this->routePrefix;
        $bladePath = $this->pathInitialize;
        $title = 'All Trashed '.Str::plural($singularLabel);

        // Get column definitions dynamically
        $getFields = getFields($this->model, getFieldsAndColumns($this->model, $this->pathInitialize, $this->singularLabel, $this->routePrefix), 'index');

        //select columns
        $selectedColumns = collect($getFields)
        ->mapWithKeys(function ($config, $key) {
            return [$key => $config['index']];
        })
        ->keys()
        ->filter(function ($key) {
            return $key !== 'action'; // Remove 'action'
        })
        ->values() // Reindex the array
        ->toArray();
    
        // Optionally prepend 'id'
        array_unshift($selectedColumns, 'id');
        
        $models = $this->model->onlyTrashed()->latest()
            ->with('createdBy:id,name')
            ->select($selectedColumns);
        //select columns

        // Step 2: Check if current route is trashed
        if (Route::currentRouteName() === $routeInitialize.'.trashed') {
            // Step 3: Remove existing 'action' config
            unset($getFields['action']);

            // Step 4: Add custom restore button to 'action'
            $getFields['action'] = [
                'type' => 'custom',
                'label' => 'Action',
                'index_visible' => true,
                'index' => fn($model) => '<a href="' . route($routeInitialize.'.restore', $model->id) . '" class="btn btn-icon btn-label-info waves-effect">'
                                        . '<span><i class="ti ti-refresh ti-sm"></i></span></a>'.
                                        '<a href="' . route($routeInitialize.'.force-delete', $model->id) . '" class="btn btn-icon btn-label-danger waves-effect">'
                                        . '<span><i class="ti ti-trash-off ti-sm text-danger" title="Force Delete"></i></span></a>'
            ];
        }

        $columns = collect($getFields)->mapWithKeys(function ($config, $key) {
            return [$key => $config['index']];
        })->toArray();  // Convert Collection to Array
        
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
    public function restore($id)
    {
       $find = $this->model->onlyTrashed()->where('id', $id)->first();
        if(isset($find) && !empty($find)) {
            $restore = $find->restore();
            if(!empty($restore)) {
                return redirect()->back()->with('message', 'Record Restored Successfully.');
            }
        } else {
            return false;
        }
    }

    public function importCreate(Request $request)
    {
        $bladePath = $this->pathInitialize;

        $model = $this->model;
        return (string) view($bladePath.'.import_create_content', get_defined_vars());
    }

    public function importStore(Request $request)
    {
        $singularLabel = $this->singularLabel;
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        try{
            $model = Excel::import(new BrandsImport, $request->file('file'));

            if(isset($model) && !empty($model)){
                return response()->json(['success' => true, 'message' =>'You have imported '.$singularLabel.' successfully.']);
            }else{
                return response()->json(['success' => false, 'message' =>'You have not imported '.$singularLabel.' successfully.']);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function forceDelete($modelId)
    {
        $singularLabel = $this->singularLabel;

        $model = $this->model->withTrashed()->find($modelId);

        if ($model && $model->forceDelete()) {
            return response()->json([
                'status' => true,
                'message' => $singularLabel . ' Deleted Successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'error' => $singularLabel . ' not deleted. Try again.'
            ]);
        }
    }
}
