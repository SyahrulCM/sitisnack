<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterialStock extends Model
{
    protected $fillable = [
        'nama_bahan','satuan','stok_akhir','stok_minimum','updated_by'
    ];

    protected $casts = [
        'stok_akhir' => 'decimal:2',
        'stok_minimum' => 'decimal:2',
    ];
}
