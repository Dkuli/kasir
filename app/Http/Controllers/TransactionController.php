<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $transactionCode = $this->generateTransactionCode();
        return view('transaction.transaction', compact('products', 'transactionCode'));
    }

    public function store(Request $request)
    {
        $validatedData = $this->validateTransactionData($request);

        DB::beginTransaction();

        try {
            // Membuat transaksi baru
            $transaction = $this->createTransaction($validatedData);

            // Memproses item transaksi (produk dan update stok)
            $this->processTransactionItems($transaction, $validatedData['items']);

            // Commit transaksi database
            DB::commit();

            // Return JSON response
            return response()->json(['success' => true, 'transaction' => $transaction->load('products')]);

        } catch (\Exception $e) {
            // Rollback jika terjadi kesalahan
            DB::rollback();

            // Mengembalikan respon kesalahan
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }


    public function getProduct($kode_barang)
    {
        $product = Product::where('kode_barang', $kode_barang)->first();

        if ($product) {
            return response()->json(['success' => true, 'product' => $product]);
        } else {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
        }
    }

    private function validateTransactionData(Request $request)
    {
        return $request->validate([
            'kode_transaksi' => 'required|unique:transactions',
            'total_harga' => 'required|numeric|min:0',
            'bayar' => 'required|numeric|min:0',
            'kembali' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);
    }

    private function createTransaction(array $data)
    {
        return Transaction::create([
            'kode_transaksi' => $data['kode_transaksi'],
            'total_harga' => $data['total_harga'],
            'bayar' => $data['bayar'],
            'kembali' => $data['kembali'],
            'user_id' => auth()->id(),

        ]);

    }

    private function processTransactionItems(Transaction $transaction, array $items)
    {
        foreach ($items as $item) {
            $product = Product::findOrFail($item['product_id']);

            if ($product->stok < $item['quantity']) {
                throw new \Exception("Stok tidak mencukupi untuk produk: {$product->nama_barang}");
            }

            $transaction->products()->attach($item['product_id'], [
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);

            $this->updateProductStock($product, $item['quantity']);

        }

    }

    private function updateProductStock(Product $product, $quantity)
    {
        $product->stok -= $quantity;
        $product->save();
    }

    private function generateTransactionCode()
    {
        $prefix = 'T' . Carbon::now()->format('Ymd');
        $lastTransaction = Transaction::where('kode_transaksi', 'like', $prefix . '%')
            ->orderBy('kode_transaksi', 'desc')
            ->first();

        if ($lastTransaction) {
            $lastNumber = intval(substr($lastTransaction->kode_transaksi, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function searchProducts(Request $request)
    {
        $query = $request->input('query');
        $products = Product::where('nama_barang', 'like', "%{$query}%")
                           ->orWhere('kode_barang', 'like', "%{$query}%")
                           ->get();
        return response()->json($products);
    }

    public function history(Request $request)
    {
        $transactions = Transaction::with('products')
            ->when($request->date_from, function($query) use ($request) {
                return $query->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function($query) use ($request) {
                return $query->whereDate('created_at', '<=', $request->date_to);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('transaction.history', compact('transactions',));
    }

    public function show($id)
    {
        $transaction = Transaction::with('products', 'user')->findOrFail($id);
        return response()->json(['transaction' => $transaction]);
    }

    public function success($id)
{
    // Mendapatkan detail transaksi berdasarkan ID
    $transaction = Transaction::with('products')->findOrFail($id);

    // Menampilkan view sukses transaksi
    return view('transaction.success', compact('transaction'));
}

}

