<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colour extends Model
{
    use HasFactory;

    protected $table = 'colour';
    protected $primaryKey = 'colourId';
    protected $fillable = ['colourName'];

    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'colourId');
    }
}
