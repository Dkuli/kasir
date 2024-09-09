<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public static function generateKodeBarang()
    {
        $prefix = 'PRD';
        $lastProduct = Product::orderBy('id', 'desc')->first();

        if ($lastProduct) {
            $lastKodeBarang = intval(substr($lastProduct->kode_barang, strlen($prefix)));
            $newKodeBarang = $lastKodeBarang + 1;
        } else {
            $newKodeBarang = 1;
        }

        return $prefix . str_pad($newKodeBarang, 5, '0', STR_PAD_LEFT);
    }
}
