<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Staff extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    protected $guard_name = 'staff';
    protected $table = 'staff';
    protected $primaryKey = 'staffId';
    protected $fillable = ['staffName', 'role', 'email', 'password'];

    public function stores()
    {
        return $this->hasMany(Store::class, 'staffId');
    }

    public function requests()
    {
        return $this->hasMany(Request::class, 'staffId');
    }

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
