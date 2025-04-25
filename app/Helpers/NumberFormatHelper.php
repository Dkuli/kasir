<?php

namespace App\Helpers;

class NumberFormatHelper
{
    /**
     * Format number to Indonesian Rupiah format
     *
     * @param float|int $number
     * @param bool $withSymbol
     * @return string
     */
    public static function formatRupiah($number, $withSymbol = true)
    {
        $formatted = number_format($number, 0, ',', '.');
        return $withSymbol ? "Rp {$formatted}" : $formatted;
    }

    /**
     * Parse formatted number string back to float
     *
     * @param string $formattedNumber
     * @return float
     */
    public static function parseFormattedNumber($formattedNumber)
    {
        // Remove non-numeric characters except decimal separator
        return (float) str_replace(['.', 'Rp', ' '], ['', '', ''], $formattedNumber);
    }
}
