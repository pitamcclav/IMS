<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $table = 'store';

    protected $primaryKey = 'storeId';
    protected $fillable = ['storeName', 'location', 'managerId'];

    public function manager()
    {
        return $this->belongsTo(Staff::class, 'managerId', 'staffId');
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'storeId');
    }

    public function template()
    {
        return $this->hasMany(EmailTemplate::class, 'storeId');
    }
}
