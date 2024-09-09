<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'kode_barang', 'category_id', 'nama_barang', 'merek', 'stok', 'harga', 'keterangan', 'image'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}


