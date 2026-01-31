<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_id',
        'product_id',
        'qty_retur',
    ];

    public function retur()
    {
        return $this->belongsTo(ReturnRequest::class, 'return_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
