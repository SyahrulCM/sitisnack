<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentItem extends Model
{
    protected $table = 'payment_items';

    protected $fillable = [
        'payment_id',
        'product_id',
        'qty_terjual',
        'qty_sisa',
        'harga',
        'subtotal',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
