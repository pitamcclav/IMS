<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    use HasFactory;

    protected $table = 'supply';
    protected $primaryKey = 'supplyId';
    protected $fillable = ['supplierId', 'itemId', 'quantity', 'supplyDate','delivery_notes'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplierId');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'itemId');
    }
}
