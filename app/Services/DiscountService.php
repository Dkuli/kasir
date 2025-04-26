<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Product;
use Carbon\Carbon;

class DiscountService
{
    /**
     * Validate and calculate discount amount for a given code and cart items
     *
     * @param string $code
     * @param array $items Cart items with product_id, quantity, price
     * @param float $totalAmount
     * @return array
     */
    public function processDiscount($code, $items, $totalAmount)
    {
        // Find discount by code
        $discount = Discount::where('code', $code)
            ->where('is_active', true)
            ->first();

        if (!$discount) {
            return [
                'valid' => false,
                'message' => 'Kode diskon tidak valid atau tidak ditemukan.'
            ];
        }

        // Check if discount is valid (not expired)
        if ($this->isDiscountExpired($discount)) {
            return [
                'valid' => false,
                'message' => 'Kode diskon sudah tidak berlaku.'
            ];
        }

        // Check minimum purchase if set
        if ($discount->min_purchase && $totalAmount < $discount->min_purchase) {
            return [
                'valid' => false,
                'message' => 'Minimal belanja Rp ' . number_format($discount->min_purchase, 0, ',', '.')
            ];
        }

        // Extract product IDs from items
        $productIds = array_column($items, 'product_id');

        // Check eligibility based on discount type
        $eligibilityResult = $this->checkDiscountEligibility($discount, $productIds);

        if (!$eligibilityResult['eligible']) {
            return [
                'valid' => false,
                'message' => 'Diskon ini tidak berlaku untuk produk yang Anda pilih.'
            ];
        }

        // Calculate discount amount
        $discountCalc = $this->calculateDiscountAmount(
            $discount,
            $items,
            $eligibilityResult['eligible_items'],
            $totalAmount
        );

        return [
            'valid' => true,
            'discount' => $discount,
            'discount_amount' => $discountCalc['amount'],
            'message' => $discountCalc['message'],
            'eligible_items' => $eligibilityResult['eligible_items']
        ];
    }

    /**
     * Check if a discount is expired
     *
     * @param Discount $discount
     * @return bool
     */
    private function isDiscountExpired($discount)
    {
        $now = Carbon::now();

        if ($discount->start_date && $now->lt(Carbon::parse($discount->start_date))) {
            return true;
        }

        if ($discount->end_date && $now->gt(Carbon::parse($discount->end_date))) {
            return true;
        }

        return false;
    }

    /**
     * Check if items in cart are eligible for this discount
     *
     * @param Discount $discount
     * @param array $productIds
     * @return array
     */
    private function checkDiscountEligibility($discount, $productIds)
    {
        // If discount applies to all products, everything is eligible
        if ($discount->applies_to === 'all') {
            return [
                'eligible' => true,
                'eligible_items' => $productIds
            ];
        }

        // For product-specific discounts
        if ($discount->applies_to === 'product' && $discount->product_id) {
            $eligible = in_array($discount->product_id, $productIds);
            return [
                'eligible' => $eligible,
                'eligible_items' => $eligible ? [$discount->product_id] : []
            ];
        }

        // For category-specific discounts
        if ($discount->applies_to === 'category' && $discount->category_id) {
            $categoryProductIds = Product::where('category_id', $discount->category_id)
                                     ->pluck('id')
                                     ->toArray();
            $eligibleItems = array_intersect($productIds, $categoryProductIds);
            return [
                'eligible' => !empty($eligibleItems),
                'eligible_items' => $eligibleItems
            ];
        }

        // Default case - not eligible
        return [
            'eligible' => false,
            'eligible_items' => []
        ];
    }

    /**
     * Calculate discount amount based on type
     *
     * @param Discount $discount
     * @param array $items
     * @param array $eligibleItemIds
     * @param float $totalAmount
     * @return array
     */
    private function calculateDiscountAmount($discount, $items, $eligibleItemIds, $totalAmount)
    {
        $discountAmount = 0;
        $message = '';

        // Calculate applicable amount based on eligible items
        $applicableAmount = $this->calculateApplicableAmount($discount, $items, $eligibleItemIds, $totalAmount);

        switch ($discount->type) {
            case 'percentage':
                $discountAmount = $applicableAmount * ($discount->value / 100);
                $message = 'Diskon ' . $discount->value . '%';
                break;

            case 'fixed':
                $discountAmount = $discount->value;
                $message = 'Potongan harga sebesar Rp ' . number_format($discount->value, 0, ',', '.');
                break;

            case 'buy_x_get_y':
                // Implementation for buy X get Y free logic
                $discountAmount = $this->calculateBuyXGetYDiscount($items, $eligibleItemIds, $discount->value);
                $message = 'Diskon beli ' . $discount->value . ' gratis 1';
                break;
        }

        // Apply maximum discount if set
        if ($discount->max_discount && $discountAmount > $discount->max_discount) {
            $discountAmount = $discount->max_discount;
            $message .= ' (maksimal Rp ' . number_format($discount->max_discount, 0, ',', '.') . ')';
        }

        return [
            'amount' => $discountAmount,
            'message' => $message
        ];
    }

    /**
     * Calculate the amount that the discount applies to
     */
    private function calculateApplicableAmount($discount, $items, $eligibleItemIds, $totalAmount)
    {
        if ($discount->applies_to === 'all') {
            return $totalAmount;
        }

        $applicableAmount = 0;
        foreach ($items as $item) {
            if (in_array($item['product_id'], $eligibleItemIds)) {
                $applicableAmount += $item['price'] * $item['quantity'];
            }
        }

        return $applicableAmount;
    }

    /**
     * Calculate discount for Buy X Get Y free promotion
     */
    private function calculateBuyXGetYDiscount($items, $eligibleItemIds, $buyXValue)
    {
        $discountAmount = 0;
        $groupedItems = [];

        // Group items by product ID
        foreach ($items as $item) {
            if (in_array($item['product_id'], $eligibleItemIds)) {
                if (!isset($groupedItems[$item['product_id']])) {
                    $groupedItems[$item['product_id']] = [
                        'quantity' => 0,
                        'price' => $item['price']
                    ];
                }
                $groupedItems[$item['product_id']]['quantity'] += $item['quantity'];
            }
        }

        // Calculate free items
        foreach ($groupedItems as $productId => $info) {
            $freeItems = floor($info['quantity'] / ($buyXValue + 1));
            $discountAmount += $freeItems * $info['price'];
        }

        return $discountAmount;
    }
}
