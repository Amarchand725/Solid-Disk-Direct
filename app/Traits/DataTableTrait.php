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

            // Identify which columns should be treated as raw HTML
            $rawColumns = array_keys($columns);

            return $dataTable->rawColumns($rawColumns)->make(true);
        }       
    }
}
