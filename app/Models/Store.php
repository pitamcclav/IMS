<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $table = 'store';

    protected $primaryKey = 'storeId';
    protected $fillable = ['storeName', 'location', 'staffId'];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staffId');
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'storeId');
    }
}
