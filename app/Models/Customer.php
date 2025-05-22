<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customer'; // Pastikan nama tabel benar

    // protected $fillable = [
    //     'user_id',
    //     'nama_toko',
    //     'total_pembelian',
    //     'target1',
    //     'target2',
    //     'deskripsi_hadiah',
    //     'gambar_hadiah',
    // ];
    

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
}
