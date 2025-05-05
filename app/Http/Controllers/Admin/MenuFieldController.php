<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use App\Models\Menu;
use App\Models\MenuField;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class MenuFieldController extends Controller
{
    protected $model;
    protected $routePrefix;
    protected $pathInitialize;
    protected $singularLabel;
    protected $pluralLabel;
    protected array $permissions;

    public function __construct(MenuField $model)
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
        ];
    }

    public function getFieldsAndColumns()
    {
        $modelName = class_basename($this->model);
        $menuName = Str::kebab(Str::singular($modelName));
        $menu = Menu::where('menu', $menuName)->first();
        $menuFields = MenuField::where('menu_id', $menu->id)->get();

        $fieldArray = [];

        foreach ($menuFields as $field) {
            $extraAttributes = json_decode($field->extra ?? '{}', true);

            // Defaults
            $fieldData = [
                'type' => $field->input_type,
                'label' => $field->label ?? ucfirst(str_replace('_', ' ', $field->name)),
                'placeholder' => $field->placeholder ?? "Enter {$field->name}",
                'required' => (bool) $field->required,
                'value' => fn($model) => $model->{$field->name} ?? '',
                'index' => fn($model) => $model->{$field->name} ?? '-',
                'index_visible' => (bool) $field->index_visible,
                'create_visible' => (bool) $field->create_visible,
                'edit_visible' => (bool) $field->edit_visible,
                'show_visible' => (bool) $field->show_visible,
                'extra' => $extraAttributes,
            ];

            // Dynamic custom logic for specific fields
            switch ($field->name) {
                case 'status':
                    $fieldData['options'] = [1 => 'Active', 0 => 'De-Active'];
                    $fieldData['value'] = fn($model) => $model->status ?? 0;
                    $fieldData['index'] = fn($model) =>
                        $model->status == 1
                            ? '<span class="badge bg-label-success me-1">Active</span>'
                            : '<span class="badge bg-label-danger me-1">De-Active</span>';
                    break;

                case 'created_at':
                    $fieldData['value'] = fn($model) => $model->created_at
                        ? Carbon::parse($model->created_at)->format('d, M Y | H:i A') : '';
                    $fieldData['index'] = fn($model) => $model->created_at
                        ? Carbon::parse($model->created_at)->format('d, M Y | H:i A') : '';
                    break;

                case 'created_by':
                    $fieldData['value'] = fn($model) => optional($model->createdBy)->name ?? '-';
                    $fieldData['index'] = fn($model) => optional($model->createdBy)->name ?? '-';
                    break;

                case 'action':
                    $fieldData['index'] = fn($model) => view($this->pathInitialize . '.action', [
                        'model' => $model,
                        'singularLabel' => $this->singularLabel,
                        'routeInitialize' => $this->routePrefix
                    ])->render();
                    break;

                // Add more dynamic overrides if needed
            }

            $fieldArray[$field->name] = $fieldData;
        }

        return $fieldArray;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $bladePath = $this->pathInitialize;
        $title = $this->singularLabel;
        $menu = Menu::where('id', $id)->first();
        $fields = $this->model->where('menu_id', $menu->id)->get();
        return view($bladePath.'.edit_content', get_defined_vars());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $modelId)
    {
        $mainMenu = Menu::where('id', $modelId)->first();
        $singularLabel = $this->singularLabel;
        DB::beginTransaction();

        try{
            // Loop through the fields and types
            foreach ($request->fields as $field => $fieldObj) {
                $model = $this->model
                        ->where('menu_id', $mainMenu->id)
                        ->where('name', $field)
                        ->first();

                $extraValidation = NULL;
                if(isset($fieldObj['extra']) && !empty($fieldObj['extra'])){
                    $extraValidation = $fieldObj['extra'];
                }else{
                    if ($fieldObj['type'] == 'string') {
                        $extra['validation'] = 'max:255';
                    } elseif ($fieldObj['type'] == 'text') {
                        $extra['validation'] = NULL;
                    }

                    $extraValidation = json_encode($extra);
                }

                $model->data_type = $fieldObj['type'] ?? null;
                $model->input_type = $fieldObj['input_type'] ?? null;
                $model->label = $fieldObj['label'] ?? null;
                $model->placeholder = $fieldObj['placeholder'] ?? null;
                $model->required = $fieldObj['required'] ?? 0;
                $model->index_visible = $fieldObj['index_visible'] ?? 0;
                $model->create_visible = $fieldObj['create_visible'] ?? 0;
                $model->edit_visible = $fieldObj['edit_visible'] ?? 0;
                $model->show_visible = $fieldObj['show_visible'] ?? 0;
                $model->extra = $extraValidation;
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
}