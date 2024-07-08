<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';
    protected $primaryKey = 'supplierId';
    protected $fillable = ['supplierName', 'contactInfo'];

    public function supply()
    {
        return $this->hasMany(Supply::class, 'supplierId');
    }
}
