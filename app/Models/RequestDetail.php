<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestDetail extends Model
{
    use HasFactory;

    protected $table = 'request_detail';

    protected $primaryKey = 'requestDetailId';
    protected $fillable = ['requestId', 'itemId', 'colourId', 'sizeId', 'quantity'];

    public function request()
    {
        return $this->belongsTo(Request::class, 'requestId');
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
