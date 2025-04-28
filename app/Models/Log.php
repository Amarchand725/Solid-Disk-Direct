<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'user_id', 'action', 'model', 'model_id', 'changed_fields', 'ip_address', 'description', 'extra_details',
    ];

    public function hasActionUser(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
