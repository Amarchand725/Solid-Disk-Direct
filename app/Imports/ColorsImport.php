<?php

namespace App\Imports;

use App\Models\Color;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ColorsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $model = Color::where('name', $row['name'])->first();
        if(empty($model)){
            return new Color([
                'created_by' => Auth::user()->id,
                'name' => $row['name'] ?? NULL,
                'code' => $row['code'] ?? NULL,
            ]);
        }
    }
}

