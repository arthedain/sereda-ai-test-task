<?php

namespace App\Helpers;

class PriceHelper
{
    /**
     * Formats the given price according to the specified currency.
     *
     * @param int $price The price amount in cents.
     * @param string $currency The currency code (e.g., 'USD').
     * @return string The formatted price string including the currency symbol.
     */
    public static function format(int $price, string $currency): string
    {
        $currency = match ($currency) {
            'USD' => '$',
            default => '',
        };

        return $currency . number_format($price / 100, 2, '.', ' ');
    }
}
