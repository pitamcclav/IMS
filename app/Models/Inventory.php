<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $primaryKey = 'inventoryId';
    protected $fillable = ['quantity', 'initialQuantity', 'storeId', 'itemId', 'colourId', 'sizeId'];

    public function store()
    {
        return $this->belongsTo(Store::class, 'storeId');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'itemId');
    }

    public function colour()
    {
        return $this->belongsTo(Colour::class, 'colourId');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'sizeId');
    }
}
