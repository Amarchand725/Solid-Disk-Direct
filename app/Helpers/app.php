<?php

use Carbon\Carbon;
use App\Models\Menu;
use App\Models\PaymentMode;
use App\Models\PaymentType;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

function appName(){
    return settings()->name ?? '';
}

function settings()
{
    return cache()->remember('setting', 3600, function () {
        return Setting::first();
    });
}
function currency(){
    $setting = settings();
    if(!empty($setting) && !empty($setting)){
        return $setting->currency_symbol;
    }
}

function SubPermissions($label)
{
    return Permission::where('label', $label)->get();
}

// Function to group permissions by their common prefix
function groupPermissions($permissions) {
    $groups = [];

    foreach ($permissions as $permission) {
        // Extract group name before the hyphen
        $groupName = strtok($permission, '-');

        // Add permission to the group
        $groups[$groupName][] = $permission;
    }

    return $groups;
}

function subPermissionFields(){
    return $sub_permission_fields = [
        'list' => 'list',
        'create' => 'create',
        'show' => 'show',
        'edit' => 'edit',
        'delete' => 'delete',
        'status' => 'status',
        'trashed' => 'trashed',
        'restore' => 'restore',
    ];
}

function getFields($model, $fields, $view = 'index')
{
    return collect($fields)
        ->filter(fn($config) => $config[$view . '_visible'] ?? false) // Check visibility for the current view
        ->mapWithKeys(function ($config, $key) use ($view, $model) {
            $type = $config['type'] ?? 'text';
            $field = $key;
            $defaultImage = 'images/default.png';
            $indexCallback = match ($type) {
                'file' => fn($model) => $model->$field
                    ? view('admin.layouts.show_image', ['image' => $model->$field])->render()
                    : view('admin.layouts.show_image', ['image' => $defaultImage])->render(),
                default => $config['index'] ?? fn($model) => $model->$field ?? '-',
            };                      

            return [
                $key => [
                    'type' => $type,
                    'accept' => $config['accept'] ?? 'image/*',
                    'label' => $config['label'] ?? ucfirst($key),
                    'placeholder' => $config['placeholder'] ?? '',
                    'options' => $config['options'] ?? [],
                    'required' => $config['required'] ?? false,
                    'value' => isset($config['value']) && is_callable($config['value']) 
                        ? ($model ? $config['value']($model) : '') 
                        : ($config['value'] ?? ''),
                    'index' => $indexCallback,
                ]
            ];
        })
        ->toArray();
}

function buildValidationRules($fields, $model = null, $request = null)
{
    $rules = [];

    foreach ($fields as $key => $config) {
        $isRequired = $config['required'] ?? false;
        $validations = [];

        if (isset($config['extra']) && !empty($config['extra'])) {
            $decoded = json_decode($config['extra'], true);

            if (isset($decoded['validation']) && is_array($decoded['validation'])) {
                $validations = $decoded['validation'];
            }
        }

        // Adjust unique rule if updating
        foreach ($validations as &$v) {
            if (Str::startsWith($v, 'unique:')) {
                [$table, $column] = explode(',', str_replace('unique:', '', $v));
                if ($model && $model->id) {
                    $v = "unique:$table,$column," . $model->id . ",id";
                }
            }
        }

        // Ensure 'required' or 'nullable' is added appropriately
        if (isset($config['type']) && ($config['type'] === 'file' || in_array('file', $validations)) && $model) {
            if (!$model->$key && !$request->hasFile($key) && $isRequired) {
                array_unshift($validations, 'required');
            } else {
                array_unshift($validations, 'nullable');
            }
        }elseif ($isRequired && !in_array('required', $validations)) {
            array_unshift($validations, 'required');
        } elseif (!$isRequired && !in_array('nullable', $validations)) {
            array_unshift($validations, 'nullable');
        }

        $rules[$key] = $validations;
    }

    return $rules;
}

function fieldTypes(){
    $types = [
        'string' => 'String',
        'integer' => 'Integer',
        'bigInteger' => 'BigInteger',
        'boolean' => 'Boolean',
        'text' => 'Text',
        'date' => 'Date',
        'time' => 'Time',
        'dateTime' => 'Date Time',
        'decimal' => 'Decimal',
        'longText' => 'Long Text',
    ];
    return $types;    
}
function inputTypes(){
    $inputTypes = [
        'text' => 'text',
        'number' => 'number',
        'email' => 'email',
        'password' => 'password',
        'file' => 'file',
        'checkbox' => 'checkbox',
        'radio' => 'radio',
        'color' => 'color',
        'url' => 'url',
        'tel' => 'tel',
        'range' => 'range',
        'date' => 'date',
        'datetime-local' => 'datetime-local',
        'month' => 'month',
        'week' => 'week',
        'time' => 'time',
        'hidden' => 'hidden',
        'search' => 'search',
        'submit' => 'submit',
        'reset' => 'reset',
        'button' => 'button',
        'textarea' => 'textarea',
        'select' => 'select',
    ];
    return $inputTypes;    
}

function mergeFieldInputTypes($fields, $types, $input_types){
    $merged = [];

    foreach ($fields as $index => $field) {
        // Skip null or incomplete entries
        if (!isset($types[$index], $input_types[$index])) continue;

        $merged[] = [
            'field' => $field,
            'type' => $types[$index],
            'input_type' => $input_types[$index],
        ];
    }

    return json_encode($merged);
}

function getNewMenus(){
    // Step 1: Get unique, lowercase permission labels
    $existingLabels = Permission::select('label')
    ->groupBy('label')
    ->pluck('label')
    ->map(fn($label) => Str::lower($label))
    ->toArray();

    // Step 2: Filter menus where lowercase menu is not in permission labels
    $menusWithoutPermissions = Menu::all()->filter(function ($menu) use ($existingLabels) {
        return !in_array(Str::kebab(Str::plural($menu->menu)), $existingLabels);
    })->pluck('menu');  // Only get the 'menu' column

    return $menusWithoutPermissions;
}

function getDynamicMenuGroups(){
    // return Menu::with('hasChildMenus')->where('status', 1)
    // ->whereNull('menu_group')
    // ->orderBy('priority', 'ASC')
    // ->select('id', 'menu', 'icon')
    // ->get();

    $menus = Menu::with('hasChildMenus')
        ->where('status', 1)
        ->whereNull('menu_group')
        ->orderBy('priority', 'ASC')
        ->select('id', 'menu', 'icon')
        ->get();

    $result = $menus->map(function ($menu) {
        $children = $menu->hasChildMenus->map(function ($child) {
            return  $child->menu;
        });
    
        // Include the parent itself in children array
        $allMenus = collect([
            $menu->menu
        ])->merge($children)->toArray();
    
        return [
            'id' => $menu->id,
            'menu' => $menu->menu,
            'icon' => $menu->icon,
            'has_child_menus' => $allMenus, // ensure numeric keys
        ];
    });    

    return $result;
}

function formatSales($amount)
{
    if ($amount >= 1000000000) {
        // For billions (e.g.5B)
        return $amount / 1000000000 . 'B';  
    } elseif ($amount >= 1000000) {
        // For millions (e.g..5M)
        return $amount / 1000000 . 'M';  
    } elseif ($amount >= 1000) {
        // For thousands (e.g., 48.9k)
        return $amount / 1000 . 'k';  
    } else {
        // For values less than 1,000, keep full precision (e.g., $999.99)
        return $amount;
    }
}

function getActiveAdminUser(){
    $admin = User::where('status', 1)->role('admin')->first();
    if($admin){
        return $admin;
    }
}

function getFieldsAndColumns($model, $pathInitialize, $singularLabel, $routePrefix)
{
    $modelName = class_basename($model);
    $menuName = Str::headline(Str::kebab(Str::singular($modelName)));
    $menu = Menu::select(['id', 'menu'])
    ->with('hasMenFields:id,menu_id,name,data_type,input_type,label,placeholder,required,index_visible,create_visible,edit_visible,show_visible,extra')
                ->where('menu', $menuName)->first();
    
    $fieldArray = [];

    if(isset($menu->hasMenFields) && !empty($menu->hasMenFields)){
        $menuFields = $menu->hasMenFields;
        foreach ($menuFields as $field) {
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
                'extra' => $field->extra,
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
                    $fieldData['index'] = fn($model) => view($pathInitialize . '.action', [
                        'model' => $model,
                        'singularLabel' => $singularLabel,
                        'routeInitialize' => $routePrefix
                    ])->render();
                    break;
            }

            $fieldArray[$field->name] = $fieldData;
        }
    }

    return $fieldArray;
}

function getProductConditions(){
    return [
        'new' => 'New',
        'used' => 'Used',
        'refurbished' => 'Refurbished',
    ];
}

function getTabIcons(){
    return [
        "ti ti-shopping-cart",
        "ti ti-shopping-bag",
        "ti ti-credit-card",
        "ti ti-cash",
        "ti ti-receipt",
        "ti ti-box",
        "ti ti-package",
        "ti ti-tag",
        "ti ti-barcode",
        "ti ti-discount",
        "ti ti-gift",
        "ti ti-wallet",
        "ti ti-users",
        "ti ti-user",
        "ti ti-user-check",
        "ti ti-user-dollar",
        "ti ti-building-store",
        "ti ti-building-warehouse",
        "ti ti-truck",
        "ti ti-truck-delivery",
        "ti ti-truck-loading",
        "ti ti-truck-return",
        "ti ti-chart-bar",
        "ti ti-chart-line",
        "ti ti-report",
        "ti ti-currency-dollar",
        "ti ti-currency-euro",
        "ti ti-currency-bitcoin",
        "ti ti-category",
        "ti ti-filter",
        "ti ti-heart",
        "ti ti-star",
        "ti ti-alert-circle",
        "ti ti-shield-check",
        "ti ti-settings",
        "ti ti-home",
        "ti ti-lock",
        "ti ti-key",
        "ti ti-logout",
        "ti ti-upload",
        "ti ti-download",
        "ti ti-camera",
        "ti ti-photo",
        "ti ti-edit",
        "ti ti-trash",
        "ti ti-search",
        "ti ti-eye",
        "ti ti-eye-off",
        "ti ti-layers-subtract",
        "ti ti-brand-producthunt",
        "ti ti-certificate",
        "ti ti-file-text",
        "ti ti-file-invoice",
        "ti ti-rotate",
        "ti ti-currency-dollar-off",
        "ti ti-building-bank",
        "ti ti-brand-paypal",
        "ti ti-brand-stripe",
        "ti ti-user-x",
        "ti ti-user-star",
        "ti ti-notes",
    ];      
}

function getPaymentModes(){
    $modes = PaymentMode::where('status', 1)->get();
    if($modes){
        return $modes;
    }else{
        return [];
    }
}

function getPaymentTypes(){
    $types = PaymentType::where('status', 1)->get();
    if($types){
        return $types;
    }else{
        return [];
    }
}