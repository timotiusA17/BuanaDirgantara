<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $fillable = ['pelanggan_id', 'total_pembelian', 'tanggal_pembelian'];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }
}
