<?php

namespace App\Imports;

use App\Models\Tag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TagsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $model = Tag::where('title', $row['title'])->first();
        if(empty($model)){
            return new Tag([
                'created_by' => Auth::user()->id,
                'title' => $row['title'] ?? NULL,
                'visit_count' => $row['visit_count'] ?? NULL,
            ]);
        }
    }
}

