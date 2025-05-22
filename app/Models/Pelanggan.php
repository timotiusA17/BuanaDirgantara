<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggans'; // Pastikan nama tabel benar

    protected $fillable = [
        'user_id',
        'nama_toko',
        'total_pembelian',
        'KODEC_toko', // Tambahkan ini

        'target1',
        'target2',
        'deskripsi_hadiah',
        'gambar_hadiah',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pembelians()
    {
        return $this->hasMany(Pembelian::class);
    }
}
