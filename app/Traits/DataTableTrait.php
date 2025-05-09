<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

trait DataTableTrait
{
    public function getDataTable($request, $models, $columns = [])
    {
        if ($request->ajax() && $request->loaddata == "yes") {
            $dataTable = DataTables::of($models)->addIndexColumn();

            // Loop through each column and dynamically apply transformations
            foreach ($columns as $column => $callback) {
                $dataTable->addColumn($column, function ($model) use ($callback) {
                    return $callback($model);
                });
            }
            $dataTable->filter(function ($instance) use ($request, $columns) {
                if (!empty($request->get('search'))) {
                    $search = $request->get('search');
                    $instance = $instance->where(function ($query) use ($search, $columns) {
                        foreach ($columns as $column => $callback) {
                            if($column != 'action'){
                                $query->orWhere($column, 'LIKE', "%$search%");
                            }
                        }
                    });
                }
            });

            // Identify which columns should be treated as raw HTML
            $rawColumns = array_keys($columns);

            return $dataTable->rawColumns($rawColumns)->make(true);
        }       
    }
}
