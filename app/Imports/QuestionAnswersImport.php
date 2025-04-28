<?php

namespace App\Imports;

use App\Models\QuestionAnswer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionAnswersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $model = QuestionAnswer::where('question', $row['question'])->first();
        if(empty($model)){
            return new QuestionAnswer([
                'created_by' => Auth::user()->id,
                'question' => $row['question'] ?? NULL,
                'answer' => $row['answer'] ?? NULL,
                'ranking' => $row['ranking'] ?? NULL,
            ]);
        }
    }
}

