<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['created_by', 'menu', 'slug', 'icon', 'status', 'created_at'];

    public function createdBy(){
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function hasMenFields()
    {
        return $this->hasMany(MenuField::class);
    }

    public function hasMenuGroup(){
        return $this->hasOne(Menu::class, 'id', 'menu_group');
    }

    public function hasChildMenus(){
        return $this->hasMany(Menu::class, 'menu_group', 'id')->select('menu', 'menu_group'); // must include 'menu_group';
    }
}
