<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'order_id',
        'tanggal_jadwal_kirim',
        'tanggal_kirim',
        'tanggal_diterima',
        'alamat_kirim',
        'kurir_driver',
        'status_pengiriman',
        'bukti_kirim',
        'catatan',
        'updated_by',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}