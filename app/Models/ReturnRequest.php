<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_retur',
        'order_id',
        'reseller_id',
        'tanggal_pengajuan',
        'bukti_foto',
        'catatan_reseller',
        'catatan_penjualan',
        'status_validasi',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    public function reseller()
    {
        return $this->belongsTo(User::class, 'reseller_id');
    }
}
