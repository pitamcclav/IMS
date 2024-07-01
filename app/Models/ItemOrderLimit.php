<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemOrderLimit extends Model
{
    use HasFactory;

    protected $table = 'item_order_limit';

    protected $primaryKey = 'limitId';
    protected $fillable = ['itemId', 'orderLimit', 'period'];

    public function item()
    {
        return $this->belongsTo(Item::class, 'itemId');
    }
}
