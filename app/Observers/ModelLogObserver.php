<?php
namespace App\Observers;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class ModelLogObserver
{
    function log_action($model, $action)
    {
        $remarks = 'User login';
        if($action=='logout'){
            $remarks = 'User logout';
        }
        Log::create([
            'user_id' => $model->id,
            'action' => $action,
            'model' => get_class($model),
            'model_id' => $model->id,
            'ip_address' => request()->ip(),
            'description' => $remarks,
        ]);
    }
    
    public function created($model)
    {
        Log::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'model' => get_class($model),
            'model_id' => $model->id,
            'changed_fields' => null,
            'ip_address' => request()->ip(),
            'description' => 'Created a new record.',
        ]);
    }

    public function updated($model)
    {
        if ($model->isDirty('deleted_at') && $model->deleted_at === null) {
            return;
        }

        $changedFields = [];
        foreach ($model->getChanges() as $key => $value) {
            $changedFields[$key] = [
                'old' => $model->getOriginal($key),
                'new' => $value,
            ];
        }

        Log::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'model' => get_class($model),
            'model_id' => $model->id,
            'changed_fields' => json_encode($changedFields),
            'ip_address' => request()->ip(),
            'description' => 'Updated a record.',
        ]);
    }

    public function deleted($model)
    {
        Log::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'model' => get_class($model),
            'model_id' => $model->id,
            'changed_fields' => null,
            'ip_address' => request()->ip(),
            'description' => 'Deleted a record.',
        ]);
    }

    public function show($model)
    {
        Log::create([
            'user_id' => Auth::id(),
            'action' => 'show',
            'model' => get_class($model),
            'model_id' => $model->id,
            'changed_fields' => null, // No changes for a show action
            'ip_address' => request()->ip(),
            'description' => 'Viewed a record.',
        ]);
    }

    public function showColumn($model, $column)
    {
        Log::create([
            'user_id' => Auth::id(),
            'action' => 'show_column',
            'model' => get_class($model),
            'model_id' => $model->id,
            'changed_fields' => null, // No changes for show_column action
            'ip_address' => request()->ip(),
            'description' => 'Viewed the column: ' . $column,
            'extra_details' => json_encode([
                'column_name' => $column,
                'column_value' => $model->{$column},
            ]),
        ]);
    }
    public function downloadedDocument($model, $column)
    {
        Log::create([
            'user_id' => Auth::id(),
            'action' => 'downloaded-document',
            'model' => get_class($model),
            'model_id' => $model->id,
            'changed_fields' => null, // No changes for show_column action
            'ip_address' => request()->ip(),
            'description' => 'Downloaded the document: ' . $column,
            'extra_details' => json_encode([
                'column_name' => $column,
                'document_path' => $model->document_path,
                'column_value' => $model->{$column},
            ]),
        ]);
    }

    public function restoreRecord($model){
        Log::create([
            'user_id' => Auth::id(),
            'action' => 'restore',
            'model' => get_class($model),
            'model_id' => $model->id,
            'changed_fields' => null,
            'ip_address' => request()->ip(),
            'description' => 'Restored a record.',
        ]);
    }
}
