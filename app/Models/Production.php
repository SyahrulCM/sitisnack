<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    protected $fillable = [
        'kode_batch','tanggal_produksi','pjpu_id','produksi_input_by',
        'produk_json','target_produksi','hasil_berhasil','hasil_gagal',
        'status_produksi','keterangan'
    ];

    protected $casts = [
        'produk_json' => 'array',
        'tanggal_produksi' => 'date',
    ];
}
