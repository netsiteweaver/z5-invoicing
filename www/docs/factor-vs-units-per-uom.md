# Factor to Base vs Units per UOM

## Key Differences

### `factor_to_base`
- **Purpose**: Conversion factor to the base unit within the same dimension
- **Example**: 1 kg = 1000 grams (factor_to_base = 1000)
- **Usage**: Converting between different units of the same dimension
- **Formula**: `base_value = (value + offset) * factor_to_base`

### `units_per_uom`
- **Purpose**: How many individual items are in one UOM
- **Example**: 1 box contains 10 units (units_per_uom = 10)
- **Usage**: Counting items in packages/containers
- **Formula**: `total_items = quantity * units_per_uom`

## Real-World Examples

### Example 1: Weight Conversion
```php
// Kilogram UOM
$kg = Uom::create([
    'name' => 'Kilogram',
    'code' => 'KG',
    'dimension_code' => 'weight',
    'factor_to_base' => 1000,    // 1 kg = 1000 grams
    'units_per_uom' => 1,       // 1 kilogram per UOM
]);

// Converting 5 kg to grams
$grams = 5 * 1000 * 1 = 5000 grams
```

### Example 2: Package Conversion
```php
// Box of 10 UOM
$box10 = Uom::create([
    'name' => 'Box of 10',
    'code' => 'BOX10',
    'dimension_code' => 'count',
    'factor_to_base' => 1,      // 1 box = 1 base unit
    'units_per_uom' => 10,      // 10 individual units per box
]);

// Converting 3 boxes to individual units
$individual_units = 3 * 1 * 10 = 30 individual units
```

### Example 3: Volume Conversion
```php
// Liter UOM
$liter = Uom::create([
    'name' => 'Liter',
    'code' => 'L',
    'dimension_code' => 'volume',
    'factor_to_base' => 1000,   // 1 liter = 1000 milliliters
    'units_per_uom' => 1,       // 1 liter per UOM
]);

// Converting 2 liters to milliliters
$milliliters = 2 * 1000 * 1 = 2000 milliliters
```

## When to Use Each

### Use `factor_to_base` for:
- **Unit conversions** (kg to grams, liters to milliliters)
- **Different scales** (meters to millimeters)
- **Currency conversions** (dollars to cents)
- **Temperature conversions** (Celsius to Kelvin)

### Use `units_per_uom` for:
- **Package quantities** (boxes, cases, pallets)
- **Bulk items** (dozens, hundreds)
- **Container sizes** (bottles per case)
- **Counting items** (items per package)

## Combined Usage

### Complex Example: Receiving Oil in Drums
```php
// Receiving 50 drums of 20 liters each
$receiptItem = GoodsReceiptItem::create([
    'product_id' => $oilProduct->id,
    'quantity' => 50,           // 50 drums
    'uom_id' => $drumUom->id,   // Drum UOM
    'uom_quantity' => 20,       // 20 liters per drum
]);

// Calculation:
// quantity = 50 (drums)
// uom_quantity = 20 (liters per drum)
// factor_to_base = 1000 (liters to milliliters)
// units_per_uom = 1 (1 drum per UOM)

// Total milliliters = 50 * 20 * 1000 = 1,000,000 milliliters
```

### Another Example: Receiving Rice in Bags
```php
// Receiving 100 bags of 50kg each
$receiptItem = GoodsReceiptItem::create([
    'product_id' => $riceProduct->id,
    'quantity' => 100,          // 100 bags
    'uom_id' => $bagUom->id,    // Bag UOM
    'uom_quantity' => 50,       // 50kg per bag
]);

// Calculation:
// quantity = 100 (bags)
// uom_quantity = 50 (kg per bag)
// factor_to_base = 1000 (kg to grams)
// units_per_uom = 1 (1 bag per UOM)

// Total grams = 100 * 50 * 1000 = 5,000,000 grams
```

## Formula Summary

### For Inventory Calculations:
```
base_quantity = quantity * uom_quantity * factor_to_base * units_per_uom
```

### For Unit Conversions:
```
converted_value = (value + offset) * factor_to_base
```

### For Package Calculations:
```
total_items = quantity * units_per_uom
```

## Best Practices

1. **Always set factor_to_base** for unit conversions
2. **Use units_per_uom** for package quantities
3. **Keep dimensions consistent** (don't mix weight and volume)
4. **Use base units** for all inventory calculations
5. **Display in user-friendly units** for UI

## Common Mistakes

❌ **Wrong**: Using only `units_per_uom` for unit conversions
✅ **Correct**: Use both `factor_to_base` and `units_per_uom`

❌ **Wrong**: Mixing different dimensions
✅ **Correct**: Keep same dimension for all related UOMs

❌ **Wrong**: Not converting to base units
✅ **Correct**: Always convert to base units for calculations

This system ensures accurate inventory management while maintaining flexibility for different business needs.
