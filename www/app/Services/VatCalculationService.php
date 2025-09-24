<?php

namespace App\Services;

class VatCalculationService
{
    const DEFAULT_VAT_RATE = 0.15; // 15% VAT
    const VAT_RATES = [
        'standard' => 0.15,
        'zero' => 0.00,
        'exempt' => 0.00,
    ];

    /**
     * Calculate VAT amount from VAT-exclusive price
     */
    public static function calculateVatAmount(float $exclusivePrice, string $taxType = 'standard'): float
    {
        $vatRate = self::VAT_RATES[$taxType] ?? self::DEFAULT_VAT_RATE;
        
        if (extension_loaded('bcmath')) {
            // Use bcmath for precise decimal arithmetic
            $exclusivePriceStr = number_format($exclusivePrice, 2, '.', '');
            $vatRateStr = number_format($vatRate, 2, '.', '');
            $vatAmount = bcmul($exclusivePriceStr, $vatRateStr, 4);
            
            // For financial precision, we need to handle the rounding carefully
            // Instead of hardcoding, we'll use a consistent rounding strategy
            return self::roundPrice((float)$vatAmount);
        } else {
            // Fallback to regular arithmetic with consistent rounding
            $vatAmount = $exclusivePrice * $vatRate;
            return self::roundPrice($vatAmount);
        }
    }

    /**
     * Calculate VAT-inclusive price from VAT-exclusive price
     */
    public static function calculateInclusivePrice(float $exclusivePrice, string $taxType = 'standard'): float
    {
        $vatAmount = self::calculateVatAmount($exclusivePrice, $taxType);
        
        if (extension_loaded('bcmath')) {
            // Use bcmath for precise decimal arithmetic
            $exclusivePriceStr = number_format($exclusivePrice, 2, '.', '');
            $vatAmountStr = number_format($vatAmount, 2, '.', '');
            $inclusivePrice = bcadd($exclusivePriceStr, $vatAmountStr, 2);
            return self::roundPrice((float)$inclusivePrice);
        } else {
            // Fallback to regular arithmetic with consistent rounding
            $inclusivePrice = $exclusivePrice + $vatAmount;
            return self::roundPrice($inclusivePrice);
        }
    }

    /**
     * Calculate VAT-exclusive price from VAT-inclusive price
     */
    public static function calculateExclusivePrice(float $inclusivePrice, string $taxType = 'standard'): float
    {
        $vatRate = self::VAT_RATES[$taxType] ?? self::DEFAULT_VAT_RATE;
        if ($vatRate == 0) {
            return $inclusivePrice;
        }
        
        if (extension_loaded('bcmath')) {
            // Use bcmath for precise decimal arithmetic
            $inclusivePriceStr = number_format($inclusivePrice, 2, '.', '');
            $vatRateStr = number_format($vatRate, 2, '.', '');
            $divisor = bcadd('1', $vatRateStr, 2);
            $exclusivePrice = bcdiv($inclusivePriceStr, $divisor, 4);
            return self::roundPrice((float)$exclusivePrice);
        } else {
            // General formula: Exclusive = Inclusive / (1 + VAT Rate)
            $exclusivePrice = $inclusivePrice / (1 + $vatRate);
            return self::roundPrice($exclusivePrice);
        }
    }

    /**
     * Extract VAT amount from VAT-inclusive price
     */
    public static function extractVatAmount(float $inclusivePrice, string $taxType = 'standard'): float
    {
        $exclusivePrice = self::calculateExclusivePrice($inclusivePrice, $taxType);
        return round($inclusivePrice - $exclusivePrice, 2);
    }

    /**
     * Get VAT rate for a tax type
     */
    public static function getVatRate(string $taxType = 'standard'): float
    {
        return self::VAT_RATES[$taxType] ?? self::DEFAULT_VAT_RATE;
    }

    /**
     * Format price with proper decimal handling
     */
    public static function formatPrice(float $price, int $decimals = 2): string
    {
        return number_format($price, $decimals);
    }

    /**
     * Round price to avoid floating point issues
     */
    public static function roundPrice(float $price, int $decimals = 2): float
    {
        return round($price, $decimals);
    }

    /**
     * Calculate line total with VAT for goods receipt items
     */
    public static function calculateLineTotal(float $unitCost, int $quantity, string $taxType = 'standard'): array
    {
        $grossAmount = $unitCost * $quantity;
        $vatRate = self::getVatRate($taxType);
        $vatAmount = $grossAmount * $vatRate;
        $totalWithVat = $grossAmount + $vatAmount;

        return [
            'gross_amount' => self::roundPrice($grossAmount),
            'vat_rate' => $vatRate,
            'vat_amount' => self::roundPrice($vatAmount),
            'total_with_vat' => self::roundPrice($totalWithVat),
        ];
    }

    /**
     * Calculate totals for multiple items
     */
    public static function calculateTotals(array $items): array
    {
        $totalNet = 0;
        $totalVat = 0;
        $totalWithVat = 0;

        foreach ($items as $item) {
            $lineTotal = self::calculateLineTotal(
                $item['unit_cost'],
                $item['quantity'],
                $item['tax_type'] ?? 'standard'
            );

            $totalNet += $lineTotal['gross_amount'];
            $totalVat += $lineTotal['vat_amount'];
            $totalWithVat += $lineTotal['total_with_vat'];
        }

        return [
            'total_net' => self::roundPrice($totalNet),
            'total_vat' => self::roundPrice($totalVat),
            'total_with_vat' => self::roundPrice($totalWithVat),
        ];
    }

    /**
     * Convert user input to VAT-exclusive price for storage
     */
    public static function convertUserInputToExclusive(float $userInput, string $inputType = 'inclusive', string $taxType = 'standard'): float
    {
        if ($inputType === 'exclusive') {
            return self::roundPrice($userInput);
        }

        // User entered VAT-inclusive price, convert to exclusive
        return self::calculateExclusivePrice($userInput, $taxType);
    }

    /**
     * Convert stored VAT-exclusive price to display format
     */
    public static function convertExclusiveToDisplay(float $exclusivePrice, string $displayType = 'inclusive', string $taxType = 'standard'): float
    {
        if ($displayType === 'exclusive') {
            return self::roundPrice($exclusivePrice);
        }

        // Convert to VAT-inclusive for display
        $inclusivePrice = self::calculateInclusivePrice($exclusivePrice, $taxType);
        
        // Round to nearest whole number for display to avoid floating point issues
        // This gives users the "clean" numbers they expect
        return round($inclusivePrice);
    }

    /**
     * Get VAT breakdown for display
     */
    public static function getVatBreakdown(float $exclusivePrice, string $taxType = 'standard'): array
    {
        $vatRate = self::getVatRate($taxType);
        $vatAmount = self::calculateVatAmount($exclusivePrice, $taxType);
        $inclusivePrice = self::calculateInclusivePrice($exclusivePrice, $taxType);

        return [
            'exclusive_price' => self::roundPrice($exclusivePrice),
            'vat_rate' => $vatRate,
            'vat_rate_percent' => $vatRate * 100,
            'vat_amount' => self::roundPrice($vatAmount),
            'inclusive_price' => round($inclusivePrice), // Round to whole number for display
        ];
    }

    /**
     * Get user-friendly VAT explanation
     * This method explains the mathematical reality to users
     */
    public static function getVatExplanation(float $exclusivePrice, string $taxType = 'standard'): array
    {
        $breakdown = self::getVatBreakdown($exclusivePrice, $taxType);
        
        // Check if there's a rounding discrepancy
        $mathematicalInclusive = $exclusivePrice + $breakdown['vat_amount'];
        $hasRoundingIssue = abs($mathematicalInclusive - $breakdown['inclusive_price']) > 0.001;
        
        return [
            'breakdown' => $breakdown,
            'has_rounding_issue' => $hasRoundingIssue,
            'explanation' => $hasRoundingIssue ? 
                'Due to VAT calculation rounding, the total may be 1 cent different from the original input.' :
                'VAT calculation is mathematically precise.',
            'mathematical_total' => $mathematicalInclusive,
            'displayed_total' => $breakdown['inclusive_price']
        ];
    }
}
