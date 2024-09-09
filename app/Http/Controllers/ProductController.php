<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category; // Tambahkan model kategori
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use App\Http\Requests\ProductRequest;
use App\Services\ProductService;
use Illuminate\Support\Facades\Storage;



class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $products = Product::with('category') // Include kategori
            ->when($search, function ($query, $search) {
                return $query->where('kode_barang', 'like', "%{$search}%")
                             ->orWhere('nama_barang', 'like', "%{$search}%")
                             ->orWhereHas('category', function($q) use ($search) {
                                 $q->where('name', 'like', "%{$search}%");
                             });
            })
            ->paginate(7);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all(); // Ambil semua kategori
        return view('products.create', compact('categories'));
    }


    public function store(Request $request)
    {
        // Validasi data
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'nama_barang' => 'required|string|max:255',
            'merek' => 'nullable|string|max:255',
            'stok' => 'required|integer|min:0',
            'harga' => 'required|integer|min:0',
            'keterangan' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        // Jika ada file gambar, simpan gambar
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        // Generate kode barang menggunakan ProductService
        $kodeBarang = ProductService::generateKodeBarang();

        // Simpan data ke database, pastikan kode_barang disertakan
        Product::create($validated + ['kode_barang' => $kodeBarang]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan');
    }


    public function edit(Product $product)
    {
        $categories = Category::all(); // Ambil kategori untuk dropdown
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($product->image) {
                Storage::delete('public/' . $product->image);
            }
            $data['image'] = $request->file('image')->store('images', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        // Hapus gambar terkait sebelum produk dihapus
        if ($product->image) {
            Storage::delete('public/' . $product->image);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus!');
    }

    public function importForm()
    {
        return view('products.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        Excel::import(new ProductsImport, $request->file('file'));

        return redirect()->route('products.index')->with('success', 'Produk berhasil diimport!');
    }

    public function search(Request $request)
    {
        $term = $request->input('term');
        $products = Product::where('kode_barang', 'LIKE', "%$term%")
                           ->orWhere('nama_barang', 'LIKE', "%$term%")
                           ->get();
        return response()->json($products);
    }
}
