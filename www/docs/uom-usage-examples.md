# UOM System Usage Examples

## Overview

The enhanced UOM (Unit of Measure) system now supports different dimensions and proper conversion between units within the same dimension. This document provides examples of how to use the system effectively.

## Supported Dimensions

1. **Count** - For counting items (units, pieces, etc.)
2. **Weight** - For measuring weight (grams, kilograms, pounds)
3. **Volume** - For measuring volume (milliliters, liters, gallons)
4. **Length** - For measuring length (millimeters, centimeters, meters, inches, feet)
5. **Area** - For measuring area (square millimeters, square centimeters, square meters)
6. **Time** - For measuring time (seconds, minutes, hours)

## Usage Examples

### 1. Creating UOMs for Different Dimensions

```php
// Weight dimension examples
$gram = Uom::create([
    'name' => 'Gram',
    'code' => 'G',
    'dimension_code' => 'weight',
    'factor_to_base' => 1,        // 1 gram = 1 gram (base unit)
    'offset_to_base' => 0,
    'units_per_uom' => 1,
    'status' => 1
]);

$kilogram = Uom::create([
    'name' => 'Kilogram',
    'code' => 'KG',
    'dimension_code' => 'weight',
    'factor_to_base' => 1000,     // 1 kg = 1000 grams
    'offset_to_base' => 0,
    'units_per_uom' => 1,
    'status' => 1
]);

// Volume dimension examples
$liter = Uom::create([
    'name' => 'Liter',
    'code' => 'L',
    'dimension_code' => 'volume',
    'factor_to_base' => 1000,     // 1 liter = 1000 milliliters
    'offset_to_base' => 0,
    'units_per_uom' => 1,
    'status' => 1
]);
```

### 2. Converting Between Units

```php
// Convert 5 kilograms to grams
$kg = Uom::where('code', 'KG')->first();
$gram = Uom::where('code', 'G')->first();

$grams = $kg->convertTo(5, $gram); // Returns 5000

// Convert 2 liters to milliliters
$liter = Uom::where('code', 'L')->first();
$ml = Uom::where('code', 'ML')->first();

$milliliters = $liter->convertTo(2, $ml); // Returns 2000
```

### 3. Using the UomService for Conversions

```php
use App\Services\UomService;

// Convert 10 pounds to grams
$poundId = Uom::where('code', 'LB')->first()->id;
$gramId = Uom::where('code', 'G')->first()->id;

$grams = UomService::convert(10, $poundId, $gramId); // Returns 4535.92

// Convert to base unit
$baseValue = UomService::toBaseValue(5, $poundId); // Returns 2267.96 (grams)

// Convert from base unit
$pounds = UomService::fromBaseValue(1000, $poundId); // Returns 2.20462
```

### 4. Checking Unit Compatibility

```php
$kg = Uom::where('code', 'KG')->first();
$liter = Uom::where('code', 'L')->first();

if ($kg->isCompatibleWith($liter)) {
    // This will be false - different dimensions
    $converted = $kg->convertTo(1, $liter);
} else {
    // Handle incompatible units
    throw new \InvalidArgumentException('Cannot convert between weight and volume');
}
```

### 5. Filtering UOMs by Dimension

```php
// Get all weight units
$weightUnits = Uom::byDimension('weight')->active()->get();

// Get all volume units
$volumeUnits = Uom::byDimension('volume')->active()->get();

// Get all units for a specific dimension
$lengthUnits = Uom::where('dimension_code', 'length')
    ->where('status', 1)
    ->orderBy('name')
    ->get();
```

### 6. Product Integration Example

```php
// When creating a product with specific UOM
$product = Product::create([
    'name' => 'Cooking Oil',
    'uom_id' => Uom::where('code', 'L')->first()->id, // Liters
    'price' => 15.99,
    // ... other fields
]);

// When processing a sale with quantity conversion
$saleItem = SaleItem::create([
    'product_id' => $product->id,
    'quantity' => 2, // 2 liters
    'uom_id' => Uom::where('code', 'L')->first()->id,
    'uom_quantity' => 1, // 1 liter per UOM
    // ... other fields
]);

// Convert to base units for inventory tracking
$baseQuantity = UomService::toBaseUnits(2, $saleItem->uom_id, $saleItem->uom_quantity);
// This would be 2000 milliliters (base unit for volume)
```

### 7. Inventory Management Example

```php
// When receiving goods in different units
$receiptItem = GoodsReceiptItem::create([
    'product_id' => $product->id,
    'quantity' => 10, // 10 boxes
    'uom_id' => Uom::where('code', 'BOX10')->first()->id, // Box of 10 units
    'uom_quantity' => 1,
    'unit_cost' => 5.00,
    // ... other fields
]);

// Convert to base units for inventory
$baseQuantity = UomService::toBaseUnits(10, $receiptItem->uom_id, $receiptItem->uom_quantity);
// This would be 100 individual units
```

## Best Practices

1. **Always use the same dimension for related products** - Don't mix weight and volume units for the same product type.

2. **Set appropriate minimum increments** - For example, currency units should have min_increment of 0.01.

3. **Use descriptive codes** - Make UOM codes clear and consistent (e.g., 'KG' for kilogram, 'L' for liter).

4. **Validate conversions** - Always check if units are compatible before attempting conversions.

5. **Use base units for calculations** - Convert everything to base units for accurate calculations and then convert back to display units.

## Common Conversion Factors

### Weight (Base: Gram)
- Kilogram (KG): 1000
- Pound (LB): 453.592
- Ounce (OZ): 28.3495

### Volume (Base: Milliliter)
- Liter (L): 1000
- Gallon (GAL): 3785.41
- Fluid Ounce (FL_OZ): 29.5735

### Length (Base: Millimeter)
- Centimeter (CM): 10
- Meter (M): 1000
- Inch (IN): 25.4
- Foot (FT): 304.8

### Area (Base: Square Millimeter)
- Square Centimeter (CM2): 100
- Square Meter (M2): 1,000,000
- Square Inch (IN2): 645.16

### Time (Base: Second)
- Minute (MIN): 60
- Hour (HR): 3600
- Day (DAY): 86400

This enhanced UOM system provides a robust foundation for handling different types of measurements in your invoicing system while maintaining data integrity and enabling accurate conversions.
