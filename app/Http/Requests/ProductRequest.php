<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function rules()
    {
        return [
          'category_id' => 'required|exists:categories,id',
            'nama_barang' => 'required|string|max:255',
            'merek' => 'nullable|string|max:255',
            'stok' => 'required|integer|min:0',
            'harga' => 'required|numeric|min:0',
            'keterangan' => 'required|string|max:255',
        ];
    }
}
