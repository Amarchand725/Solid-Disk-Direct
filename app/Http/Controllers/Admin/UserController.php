<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\MenuField;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\DataTableTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

class UserController extends Controller
{
    use DataTableTrait;
    
    protected $model;
    protected $routePrefix;
    protected $pathInitialize;
    protected $singularLabel;
    protected $pluralLabel;
    protected array $permissions;

    public function __construct(User $model)
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

    protected function getFieldsAndColumns()
    {
        // Dynamic fields fetched from the database
        $dynamicFields = $this->generateDynamicFieldArray($this->model);

        // Common fields that should always be included
        $commonFields = $this->getCommonFields($this->model);
    
        // Merging common fields with dynamic fields
        $mergedFields = array_merge($dynamicFields, $commonFields);
        
        return $mergedFields;
    }

    public function generateDynamicFieldArray($model) {
        $table = $model->getTable();
        // Get column names and types from the database schema
        $columns = DB::connection()->getDoctrineSchemaManager()->listTableColumns($table);
        
        $fieldArray = [];
    
        foreach ($columns as $columnName => $column) {
            // Skip common fields
            if (in_array($columnName, ['id', 'status', 'created_at', 'password', 'is_employee', 'email_verified_at', 'remember_token', 'action', 'deleted_at', 'updated_at'])) {
                continue;
            }
    
            // Get the type of each column (e.g., string, integer, etc.)
            // $type = Schema::getColumnType($table, $column);
            $type = $column->getType()->getName();
            
            // Build the dynamic field configuration
            $fieldArray[$columnName] = [
                'type' => $type == 'text' ? 'text' : ($type == 'boolean' ? 'select' : 'text'), // Default 'text' or 'select' for boolean
                'label' => ucfirst(str_replace('_', ' ', $columnName)), // Use column name as label (capitalize words and replace underscores)
                'placeholder' => "Enter $columnName", // Placeholder text
                'required' => in_array($columnName, ['title', 'status']), // Example: Mark some fields as required
                'value' => fn($model) => $model->{$columnName} ?? '', // Get the value from the model
                'index' => fn($model) => $model->{$columnName} ?? '-', // Index view value
                'index_visible' => true, // You can dynamically set visibility rules
                'create_visible' => true,
                'edit_visible' => true,
                'show_visible' => true,
            ];

            // Specifically handle the 'description' field
            if ($columnName == 'fields') {
                $fieldArray[$columnName]['index_visible'] = false; // Hide description in index view
            }
        }
    
        return $fieldArray;
    }
    public function getCommonFields($model) {
        // Common fields data (status, created_at, created_by, action)
        return [
            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'options' => [
                    1 => 'Active',
                    0 => 'De-Active'
                ],
                'index' => fn($model) => $model->status == 1
                    ? '<span class="badge bg-label-success me-1">Active</span>'
                    : '<span class="badge bg-label-danger me-1">De-Active</span>',
                'required' => true,
                'index_visible' => true,
                'create_visible' => true,
                'edit_visible' => true,
                'show_visible' => true,
            ],
            'created_at' => [
                'type' => 'datetime',
                'label' => 'Created At',
                'required' => false,
                'value' => fn($model) => Carbon::parse($model->created_at)->format('d, M Y | H:i A') ?? '',
                'index' => fn($model) => Carbon::parse($model->created_at)->format('d, M Y'),
                'index_visible' => true,
                'create_visible' => false,  // Hide in create form
                'edit_visible' => false,    // Hide in edit form
                'show_visible' => true,
            ],
            // 'created_by' => [
            //     'type' => 'text',
            //     'label' => 'Created By',
            //     'required' => false,
            //     'value' => fn($model) => isset($model->createdBy) && !empty($model->createdBy) ? $model->createdBy->name : '-',
            //     'index' => fn($model) => isset($model->createdBy) && !empty($model->createdBy) ? $model->createdBy->name : '-',
            //     'index_visible' => true,
            //     'create_visible' => false,  // Hide in create form
            //     'edit_visible' => false,    // Hide in edit form
            //     'show_visible' => true,
            // ],
            'action' => [
                'index' => fn($model) => view($this->pathInitialize . '.action', [
                    'model' => $model,
                    'singularLabel' => $this->singularLabel,
                    'routeInitialize' => $this->routePrefix
                ])->render(),
                'index_visible' => true,
                'create_visible' => false,  // Hide in create form
                'edit_visible' => false,    // Hide in edit form
                'show_visible' => false,
            ]
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

        // $models = [];
        $models = $this->model->latest()
            ->select('id', 'is_employee', 'name', 'email', 'status');
        
        // Get column definitions dynamically
        $getFields = getFields($this->model, $this->getFieldsAndColumns(), 'index');
        
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
        $fields = getFields($this->model, $this->getFieldsAndColumns(), 'create');
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
        $fields = $this->getFieldsAndColumns(); // getFieldsAndColumns() returns dynamic field definitions

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
                    if($field=='created_by'){
                        $saved->$field = auth()->id() ?? null;
                    }elseif($field=='fields'){
                        $types = $request->types;
                        $inputTypes = $request->input_types;

                        $saved->$field = mergeFieldInputTypes($validated[$field], $types, $inputTypes);
                    }else{
                        $saved->$field = $validated[$field] ?? null;
                    }
                }

                $saved->save();
            }
            
            if(isset($saved) && !empty($saved)){
                DB::commit();
                return response()->json(['success' => true, 'message' =>'You have added successfully.']);
            }else{  
                DB::rollback();
                return response()->json(['success' => false, 'message' =>'You have not added '.$singularLabel.' successfully.']);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function createMenuFields($saved, $mergedFields){
        $field_menus = MenuField::where('menu_id', $saved->id)->get();
        if ($field_menus->isNotEmpty()) {
            $field_menus->each->delete();
        }

        foreach ($mergedFields as $fieldItem) {
            $field = $fieldItem['field'];
            $type = $fieldItem['type'];
            $input_type = $fieldItem['input_type'];
        
            $label = ucfirst(str_replace('_', ' ', $field));
            $placeholder = "Enter $label";
            $required = in_array($field, ['name', 'status']);
        
            $extra = [];
            if ($type === 'string') {
                $extra['validation'] = 'max:255';
            } elseif ($type === 'text') {
                $extra['validation'] = NULL;
            }

            $extraValidation = NULL;
            if (count($extra) > 0) {
                $extraValidation = json_encode($extra);
            }
        
            $inserted = MenuField::create([
                'menu_id' => $saved->id,
                'name' => $field,
                'data_type' => $type,
                'input_type' => $input_type,
                'label' => $label,
                'placeholder' => $placeholder,
                'required' => $required,
                'extra' => $extraValidation,
            ]);
        }        

        if(isset($inserted) && !empty($inserted)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Show details the specified resource.
     */
    public function show($id)
    {
        $bladePath = $this->pathInitialize;
        $model = $this->model->findOrFail($id);
        $fields = getFields($model, $this->getFieldsAndColumns(), 'show');
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
        $fields = getFields($model, $this->getFieldsAndColumns(), 'edit');
        
        return view($bladePath.'.edit_content', get_defined_vars());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $modelId)
    {
        $model = $this->model->where('id', $modelId)->first();
        $singularLabel = $this->singularLabel;
        $fields = $this->getFieldsAndColumns(); // getFieldsAndColumns() returns dynamic field definitions

        // Step 1: Build dynamic validation rules
        $rules = buildValidationRules($fields, $model, $request);

        // Step 2: Validate
        $validated = $request->validate($rules);

        DB::beginTransaction();

        try{
            // Step 3: Dynamically assign fields
            foreach ($fields as $field => $config) {
                if($field != 'created_at' && $field != 'action' && $field != 'fields' && $field != 'menu'){
                    if($field=='created_by'){
                        $model->$field = auth()->id() ?? null;
                    }
                    else{
                        $model->$field = $validated[$field] ?? null;
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
                'status' => false,
                'error' => $singularLabel.' not deleted try again.'
            ]);
        }
    }

    public function forceDelete($modelId)
    {
        $singularLabel = $this->singularLabel;
        $model = $this->model->withTrashed()->find($modelId);
        if ($model && $model->forceDelete()) {
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
                'index' => fn($model) => '<a href="' . route($routeInitialize.'.restore', $model->id) . '" class="btn btn-icon btn-label-info waves-effect me-1">'
                                        . '<span><i class="ti ti-refresh ti-sm"></i></span></a>'.
                        '<a href="javascript:;" class="btn btn-icon btn-label-danger waves-effect delete" data-del-url="'.route($routeInitialize.".forceDelete", $model->id).'">' .
                            '<span><i class="ti ti-trash ti-sm"></i></span>' .
                        '</a>'
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
}
