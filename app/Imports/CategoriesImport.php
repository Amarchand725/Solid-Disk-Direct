<?php

namespace App\Imports;

use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CategoriesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $model = Category::where('name', $row['name'])->first();
        if(empty($model)){
            $parent_id = NULL;
            if(isset($row['parent_id']) && $row['parent_id'] != 0){
                $parent_id = $row['parent_id'];
            }
            return new Category([
                'created_by' => Auth::user()->id,
                'parent_id' => $parent_id,
                'name' => $row['name'] ?? NULL,
                'description' => $row['description'] ?? NULL,
            ]);
        }
    }
}

