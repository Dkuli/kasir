<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
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
}
