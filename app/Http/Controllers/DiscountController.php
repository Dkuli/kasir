<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\DiscountService;

class DiscountController extends Controller
{

    protected $discountService;

    public function __construct(DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }

    public function index()
    {
        $discounts = Discount::with(['product', 'category'])->latest()->get();
        return view('discounts.index', compact('discounts'));
    }

    public function create()
    {
        $products = Product::all();
        $categories = Category::all();
        return view('discounts.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:discounts,code',
            'type' => 'required|in:percentage,fixed,buy_x_get_y',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'sometimes|boolean',
            'applies_to' => 'required|in:all,product,category',
            'product_id' => 'required_if:applies_to,product',
            'category_id' => 'required_if:applies_to,category',
        ]);

        // Ensure proper null values based on applies_to
        if ($validated['applies_to'] !== 'product') {
            $validated['product_id'] = null;
        }
        if ($validated['applies_to'] !== 'category') {
            $validated['category_id'] = null;
        }

        Discount::create($validated);

        return redirect()->route('discounts.index')
            ->with('success', 'Diskon berhasil dibuat!');
    }

    public function edit(Discount $discount)
    {
        $products = Product::all();
        $categories = Category::all();
        return view('discounts.edit', compact('discount', 'products', 'categories'));
    }

    public function update(Request $request, Discount $discount)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:discounts,code,'.$discount->id,
            'type' => 'required|in:percentage,fixed,buy_x_get_y',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'sometimes|boolean',
            'applies_to' => 'required|in:all,product,category',
            'product_id' => 'required_if:applies_to,product',
            'category_id' => 'required_if:applies_to,category',
        ]);

        // Ensure proper null values based on applies_to
        if ($validated['applies_to'] !== 'product') {
            $validated['product_id'] = null;
        }
        if ($validated['applies_to'] !== 'category') {
            $validated['category_id'] = null;
        }

        $discount->update($validated);

        return redirect()->route('discounts.index')
            ->with('success', 'Diskon berhasil diupdate!');
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();
        return redirect()->route('discounts.index')
            ->with('success', 'Diskon berhasil dihapus!');
    }

    public function getApplicableDiscounts(Request $request)
    {
        $productId = $request->input('product_id');
        $categoryId = $request->input('category_id');
        $quantity = $request->input('quantity', 1);

        $discounts = Discount::where('is_active', true)
            ->where(function($query) use ($productId, $categoryId) {
                $query->where('applies_to', 'all')
                    ->orWhere(function($q) use ($productId) {
                        $q->where('applies_to', 'product')
                          ->where('product_id', $productId);
                    })
                    ->orWhere(function($q) use ($categoryId) {
                        $q->where('applies_to', 'category')
                          ->where('category_id', $categoryId);
                    });
            })
            ->whereRaw('(start_date IS NULL OR start_date <= NOW())')
            ->whereRaw('(end_date IS NULL OR end_date >= NOW())')
            ->get();

        return response()->json(['discounts' => $discounts]);
    }


    public function checkDiscount(Request $request)
{
    $code = $request->input('code');
    $productIds = $request->input('products', []);
    $totalAmount = $request->input('total_amount', 0);

    // Find discount by code
    $discount = Discount::where('code', $code)
        ->where('is_active', true)
        ->first();

    if (!$discount) {
        return response()->json([
            'valid' => false,
            'message' => 'Kode diskon tidak valid atau tidak ditemukan.'
        ]);
    }

    // Check if discount is valid (not expired)
    if (!$discount->isValid()) {
        return response()->json([
            'valid' => false,
            'message' => 'Kode diskon sudah tidak berlaku.'
        ]);
    }

    // Check minimum purchase if set
    if ($discount->min_purchase && $totalAmount < $discount->min_purchase) {
        return response()->json([
            'valid' => false,
            'message' => 'Minimal belanja Rp ' . number_format($discount->min_purchase, 0, ',', '.')
        ]);
    }

    // Check if products in cart are eligible for this discount
    $eligible = true;
    $eligibleItems = [];

    if ($discount->applies_to === 'product' && $discount->product_id) {
        $eligible = in_array($discount->product_id, $productIds);
        $eligibleItems = [$discount->product_id];
    } else if ($discount->applies_to === 'category' && $discount->category_id) {
        // Get all product IDs in this category
        $categoryProductIds = Product::where('category_id', $discount->category_id)->pluck('id')->toArray();
        $eligibleItems = array_intersect($productIds, $categoryProductIds);
        $eligible = !empty($eligibleItems);
    }

    if (!$eligible && $discount->applies_to !== 'all') {
        return response()->json([
            'valid' => false,
            'message' => 'Diskon ini tidak berlaku untuk produk yang Anda pilih.'
        ]);
    }

    // Calculate discount amount
    $discountAmount = 0;
    $message = '';

    if ($discount->type === 'percentage') {
        $applyToTotal = $discount->applies_to === 'all' ? $totalAmount :
            array_sum(array_map(function($order) use ($eligibleItems) {
                return in_array($order['id'], $eligibleItems) ? $order['harga'] * $order['jumlah'] : 0;
            }, $request->input('orders', [])));

        $discountAmount = $applyToTotal * ($discount->value / 100);
        $message = 'Diskon ' . $discount->value . '% dari total belanja';
    } else if ($discount->type === 'fixed') {
        $discountAmount = $discount->value;
        $message = 'Potongan harga sebesar Rp ' . number_format($discount->value, 0, ',', '.');
    }

    // Apply maximum discount if set
    if ($discount->max_discount && $discountAmount > $discount->max_discount) {
        $discountAmount = $discount->max_discount;
        $message .= ' (maksimal Rp ' . number_format($discount->max_discount, 0, ',', '.') . ')';
    }

    return response()->json([
        'valid' => true,
        'discount' => $discount,
        'eligible_items' => $eligibleItems,
        'discount_amount' => $discountAmount,
        'message' => $message
    ]);
}
}
