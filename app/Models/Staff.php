<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';
    protected $primaryKey = 'staffId';
    protected $fillable = ['staffName', 'role'];

    public function stores()
    {
        return $this->hasMany(Store::class, 'staffId');
    }

    public function requests()
    {
        return $this->hasMany(Request::class, 'staffId');
    }
}
