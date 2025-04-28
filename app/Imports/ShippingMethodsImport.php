<?php

namespace App\Imports;

use App\Models\ShippingMethod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ShippingMethodsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $model = ShippingMethod::where('title', $row['title'])->first();
        if(empty($model)){
            return new ShippingMethod([
                'created_by' => Auth::user()->id,
                'creator_type' => $row['creator_type'] ?? NULL,
                'title' => $row['title'] ?? NULL,
                'cost' => $row['cost'] ?? NULL,
                'duration' => $row['duration'] ?? NULL,
            ]);
        }
    }
}

