<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use App\Models\Menu;
use App\Models\Category;
use App\Models\MenuField;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\DataTableTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CategoriesImport;

class CategoryController extends Controller
{
    use DataTableTrait;
    
    protected $model;
    protected $routePrefix;
    protected $pathInitialize;
    protected $singularLabel;
    protected $pluralLabel;
    protected array $permissions;

    public function __construct(Category $model)
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

        $models = [];
        $this->model->latest()
            ->with('createdBy:id,name', 'hasParent:id,name')
            ->chunk(100, function ($modelData) use (&$models) {
                foreach ($modelData as $modelItem) {
                    $models[] = $modelItem;
                }
        });        
        
        // Get column definitions dynamically
        $getFields = getFields($this->model, getFieldsAndColumns($this->model, $this->pathInitialize, $this->singularLabel, $this->routePrefix), 'index');
        
        // Check and handle relation
        if (isset($getFields['banner'])) {
            $getFields = ['banner' => $getFields['banner']] + $getFields;
        }
        
        if (isset($getFields['parent'])) {
            // Clone config from parent_id
            // $parentConfig = $getFields['parent_id'];
            // Customize index to pull from relation
            // $parentConfig['index'] = fn($model) => optional($model->parent)->name ?? '-';
            // Remove parent_id and add custom parent
            // unset($getFields['parent_id']);

            $getFields['parent']['index'] = fn($model) => $model->hasParent?->name ?? '-';
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
    public function create(){
        $bladePath = $this->pathInitialize;

        $models = [];
        $parent_categories = $this->model->where('parent', NULL)->where('status', 1)->latest()->get();

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
                $categories = array_filter($request->categories, function($value) {
                    return !is_null($value) && $value !== '';  // Filter out null and empty values
                });
                
                if (!empty($categories)) {
                    $saved->parents()->attach($categories);
                }                

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
        $parent_categories = $this->model->where('status', 1)->latest()->get();
        $model = $this->model->where('id', $id)->first();
        
        $categoriesData = [];
        foreach ($model->parents as $index => $parent) {
            // Find the parent(s) of this parent (i.e., grandparents)
            $parentOfParentIds = DB::table('category_relations')
                ->where('child_id', $parent->id)
                ->pluck('parent_id');

            if ($parentOfParentIds->isNotEmpty()) {
                $categoriesData[$index] = $this->model->where('id', $parent->id)->get();
            } 
        }

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
                $categories = array_filter($request->categories, function($value) {
                    return !is_null($value) && $value !== '';  // Clean input
                });
                
                if (!empty($categories)) {
                    $model->parents()->sync($categories); // ← safe update
                }
                
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
        
        $models = [];
        $this->model->onlyTrashed()->latest()
            ->chunk(100, function ($modelData) use (&$models) {
                foreach ($modelData as $modelItem) {
                    $models[] = $modelItem;
                }
        });

        // Get column definitions dynamically
        $getFields = getFields($this->model, getFieldsAndColumns($this->model, $this->pathInitialize, $this->singularLabel, $this->routePrefix), 'index');

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
                                        . '<span><i class="ti ti-refresh ti-sm"></i></span></a>'
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
            $model = Excel::import(new CategoriesImport, $request->file('file'));

            if(isset($model) && !empty($model)){
                return response()->json(['success' => true, 'message' =>'You have imported '.$singularLabel.' successfully.']);
            }else{
                return response()->json(['success' => false, 'message' =>'You have not imported '.$singularLabel.' successfully.']);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function subCategories(Request $request){
        $subCategories = Category::find($request->category_id)->children;

        if(!empty($subCategories)){
            return response()->json([
                'status' => true,
                'subCategories' => $subCategories,
                'message' => 'Sub categories found',
            ]);
        }else{
            return response()->json([
                'status' => true,
                'subCategories' => $subCategories,
                'message' => 'Sub categories not found',
            ]);
        }
    }
}
