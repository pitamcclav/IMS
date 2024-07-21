<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'item';

    protected $primaryKey = 'itemId';
    protected $fillable = ['categoryId', 'itemName', 'description','initialQuantity', 'quantity'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'categoryId');
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'itemId');
    }

    public function requestDetail()
    {
        return $this->hasMany(RequestDetail::class, 'itemId');
    }
}
