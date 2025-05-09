<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function setFullName($firstName, $lastName)
    {
        $this->full_name = trim($firstName . ' ' . $lastName);
    }


    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
