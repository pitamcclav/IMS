<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Staff extends Authenticatable
{
    use HasFactory, Notifiable;

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
    // Add the required attributes and methods for authentication
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
