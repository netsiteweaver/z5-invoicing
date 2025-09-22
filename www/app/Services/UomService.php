<?php

namespace App\Services;

use App\Models\Uom;

class UomService
{
    public static function toBaseUnits(int $quantity, ?int $uomId, ?int $uomQuantity = 1): int
    {
        if (!$uomId) {
            return (int) $quantity;
        }
        $uom = Uom::find($uomId);
        if (!$uom) {
            return (int) $quantity;
        }
        $multiplier = max(1, (int) $uom->units_per_uom);
        $uomQty = max(1, (int) ($uomQuantity ?? 1));
        return (int) ($uomQty * $multiplier);
    }
}

