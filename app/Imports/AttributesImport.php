<?php

namespace App\Imports;

use App\Models\Attribute;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AttributesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $model = Attribute::where('name', $row['name'])->first();
        if(empty($model)){
            return new Attribute([
                'created_by' => Auth::user()->id,
                'name' => $row['name'],
                'description' => $row['description'],
            ]);
        }
    }
}

