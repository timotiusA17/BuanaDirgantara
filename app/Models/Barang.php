<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;
    protected $table = 'gudang'; 
    protected $primaryKey = 'KODEB'; 
    public $incrementing = false; 
    protected $keyType = 'string';
    
    public $timestamps = false;
    protected $fillable = [
        'NAMAB',
        'HJUALB',
        'satuan',
        'gambar',
    ];
}
