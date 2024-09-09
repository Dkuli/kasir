<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Services\ProductService;

class ProductsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Cari atau buat kategori
        $category = Category::firstOrCreate(['name' => $row['kategori']]);

        return new Product([
            'kode_barang' => ProductService::generateKodeBarang(),
            'category_id' => $category->id,  // Menyimpan ID kategori
            'jenis_barang' => $row['jenis_barang'] ?? 'Tidak Diketahui',
            'nama_barang' => $row['nama_barang'] ?? 'Tidak Diketahui',
            'merek' => $row['merek'] ?? null,
            'stok' => is_numeric($row['stok']) ? intval($row['stok']) : 0,
            'harga' => is_numeric($row['harga']) ? floatval($row['harga']) : 0.0,
            'keterangan' => $row['keterangan'] ?? 'Tersedia',
        ]);
    }
}
