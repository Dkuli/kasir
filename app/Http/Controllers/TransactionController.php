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
            $transaction = $this->createTransaction($validatedData);
            $this->processTransactionItems($transaction, $validatedData['items']);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Transaksi berhasil disimpan.']);
        } catch (\Exception $e) {
            DB::rollback();
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
            'total_harga' => 'required|numeric',
            'bayar' => 'required|numeric',
            'kembali' => 'required|numeric',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
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
            $transaction->products()->attach($item['product_id'], [
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);

            $this->updateProductStock($item['product_id'], $item['quantity']);
        }
    }

    private function updateProductStock($productId, $quantity)
    {
        $product = Product::findOrFail($productId);
        $product->stok = max(0, $product->stok - $quantity);
        $product->save();
    }



    private function generateTransactionCode()
    {
        $now = Carbon::now();
        return 'T' . $now->format('dmYHis');
    }


    public function searchProducts(Request $request)
    {
        $query = $request->input('query');
        $products = Product::where('nama_barang', 'like', "%{$query}%")
                           ->orWhere('kode_barang', 'like', "%{$query}%")
                           ->get();
        return response()->json($products);
    }

    public function history()
{
    $transactions = Transaction::with('products')->orderBy('created_at', 'desc')->paginate(10);
    return view('transaction.history', compact('transactions'));
}

public function show($id)
{
    $transaction = Transaction::with('products')->findOrFail($id);
    return response()->json(['transaction' => $transaction]);
}

}
