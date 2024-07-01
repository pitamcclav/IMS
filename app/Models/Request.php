<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $table = 'request';
    protected $primaryKey = 'requestId';
    protected $fillable = ['date', 'status', 'storeId', 'staffId'];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staffId');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'storeId');
    }

    public function requestDetails()
    {
        return $this->hasMany(RequestDetail::class, 'requestId');
    }
}
