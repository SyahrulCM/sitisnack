<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'kode_pemesanan','tanggal_pemesanan','reseller_id','items_json',
        'total_estimasi','status_pemesanan','catatan_reseller','catatan_penjualan'
    ];

    protected $casts = [
        'items_json' => 'array',
        'tanggal_pemesanan' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(\App\Models\OrderItem::class);
    }
    
    public function reseller()
    {
        return $this->belongsTo(\App\Models\User::class, 'reseller_id');
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }

    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class);
    }

    public function returnRequest()
    {
        return $this->hasOne(ReturnRequest::class)->latestOfMany();
    }


}


