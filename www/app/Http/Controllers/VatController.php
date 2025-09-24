<?php

namespace App\Http\Controllers;

use App\Services\VatCalculationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VatController extends Controller
{
    /**
     * Calculate VAT for given amount and tax type
     */
    public function calculate(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'tax_type' => 'required|string|in:standard,zero,exempt',
            'calculation_type' => 'required|string|in:exclusive,inclusive'
        ]);
        
        $amount = (float) $request->amount;
        $taxType = $request->tax_type;
        $calculationType = $request->calculation_type;
        
        if ($calculationType === 'inclusive') {
            $exclusivePrice = VatCalculationService::calculateExclusivePrice($amount, $taxType);
            $vatAmount = VatCalculationService::calculateVatAmount($exclusivePrice, $taxType);
            $inclusivePrice = $amount; // User input
        } else {
            $exclusivePrice = $amount; // User input
            $vatAmount = VatCalculationService::calculateVatAmount($amount, $taxType);
            $inclusivePrice = VatCalculationService::convertExclusiveToDisplay($amount, 'inclusive', $taxType);
        }
        
        return response()->json([
            'exclusive_price' => round($exclusivePrice, 2),
            'vat_amount' => round($vatAmount, 2),
            'inclusive_price' => round($inclusivePrice, 2),
            'vat_rate' => VatCalculationService::getVatRate($taxType),
            'vat_rate_percent' => VatCalculationService::getVatRate($taxType) * 100
        ]);
    }
    
    /**
     * Get VAT breakdown for given exclusive price and tax type
     */
    public function breakdown(Request $request): JsonResponse
    {
        $request->validate([
            'exclusive_price' => 'required|numeric|min:0',
            'tax_type' => 'required|string|in:standard,zero,exempt'
        ]);
        
        $exclusivePrice = (float) $request->exclusive_price;
        $taxType = $request->tax_type;
        
        $breakdown = VatCalculationService::getVatBreakdown($exclusivePrice, $taxType);
        
        return response()->json($breakdown);
    }
}
