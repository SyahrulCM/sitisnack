<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'kode_pembayaran','tanggal_input','order_id','reseller_id','items_json',
        'total_penjualan','metode_pembayaran','bukti_transfer',
        'status_validasi','catatan_validasi','verified_by','verified_at'
    ];

    protected $casts = [
        'items_json' => 'array',
        'tanggal_input' => 'date',
        'verified_at' => 'datetime',
    ];
    
    public function items()
    {
        return $this->hasMany(PaymentItem::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function reseller()
    {
        return $this->belongsTo(User::class, 'reseller_id');
    }

}
