<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuField extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id', 'name', 'data_type', 'input_type', 'label', 'placeholder',
        'required', 'index_visible', 'create_visible', 'edit_visible',
        'show_visible', 'extra',
    ];

    protected $casts = [
        'extra' => 'array',
        'required' => 'boolean',
        'index_visible' => 'boolean',
        'create_visible' => 'boolean',
        'edit_visible' => 'boolean',
        'show_visible' => 'boolean',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
