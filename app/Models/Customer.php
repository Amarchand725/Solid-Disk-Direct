<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Authenticatable 
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $table = 'customers';

    protected $fillable = ['id', 'phone', 'name', 'email', 'email_verified_at', 'password', 'remember_token','status', 'deleted_at'];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
