<?php

namespace App\Imports;

use App\Models\Brand;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BrandsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $model = Brand::where('name', $row['name'])->first();
        if(empty($model)){
            $banner = NULL;
            if(isset($row['banner'])){
                $banner = $row['banner'];
            }
    
            $logo = NULL;
            if(isset($row['logo'])){
                $logo = $row['logo'];
            }
            return new Brand([
                'created_by' => Auth::user()->id,
                'name' => $row['name'] ?? NULL,
                'description' => $row['description'] ?? NULL,
                'logo' => $logo,
                'banner' => $banner,
            ]);
        }
    }

    protected function storeImage($fileName)
    {
        $sourcePath = public_path('imports/brands/' . $fileName); // or wherever you uploaded the raw files
        $destinationPath = 'brands/' . $fileName;

        if (file_exists($sourcePath)) {
            Storage::disk('public')->put($destinationPath, file_get_contents($sourcePath));
            return $destinationPath;
        }

        return null;
    }
}

