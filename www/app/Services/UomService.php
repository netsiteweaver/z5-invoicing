<?php

namespace App\Services;

use App\Models\Uom;

class UomService
{
    /**
     * Convert a line input to base units.
     *
     * Semantics (Option A):
     * - If a uomId is provided, treat `quantity` as the count of that UOM (packs),
     *   and ignore uomQuantity. Base = quantity * units_per_uom.
     * - If no uomId, base = quantity (already base each).
     */
    public static function toBaseUnits(int $quantity, ?int $uomId, ?int $uomQuantity = 1): int
    {
        if (!$uomId) {
            return (int) $quantity;
        }
        $uom = Uom::find($uomId);
        if (!$uom) {
            return (int) $quantity;
        }
        $unitsPerUom = max(1, (int) $uom->units_per_uom);
        $packsCount = max(0, (int) $quantity);
        return (int) ($packsCount * $unitsPerUom);
    }

    /**
     * Generic conversion using factor/offset between any two UOMs in the same dimension.
     */
    public static function convert(float $value, int $fromUomId, int $toUomId): float
    {
        $from = Uom::find($fromUomId);
        $to = Uom::find($toUomId);
        if (!$from || !$to) {
            throw new \InvalidArgumentException('Invalid UOM');
        }
        if (($from->dimension_code ?? 'count') !== ($to->dimension_code ?? 'count')) {
            throw new \InvalidArgumentException('Dimension mismatch');
        }
        // Affine to base
        $base = ($value + (float) $from->offset_to_base) * (float) $from->factor_to_base;
        // From base to target
        return ($base / (float) $to->factor_to_base) - (float) $to->offset_to_base;
    }

    /**
     * Convert from a UOM to its base (factor/offset aware). Returns float value in base unit of the dimension.
     */
    public static function toBaseValue(float $value, int $fromUomId): float
    {
        $from = Uom::find($fromUomId);
        if (!$from) {
            throw new \InvalidArgumentException('Invalid UOM');
        }
        return ($value + (float) $from->offset_to_base) * (float) $from->factor_to_base;
    }

    /**
     * Convert a base value to a target UOM (factor/offset aware).
     */
    public static function fromBaseValue(float $baseValue, int $toUomId): float
    {
        $to = Uom::find($toUomId);
        if (!$to) {
            throw new \InvalidArgumentException('Invalid UOM');
        }
        return ($baseValue / (float) $to->factor_to_base) - (float) $to->offset_to_base;
    }
}

