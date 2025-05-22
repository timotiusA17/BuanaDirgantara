<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;
    protected $table = 'gudang'; // Pastikan nama tabel benar
    protected $primaryKey = 'KODEB'; // Set primary key
    public $incrementing = false; // Jika KODEB bukan integer auto-increment
    protected $keyType = 'string'; // Jika KODEB adalah string
    
    // Jika tidak menggunakan timestamp
    public $timestamps = false;
    protected $fillable = [
        'NAMAB',
        'HJUALB',
        'satuan',
        'gambar',
    ];
}
