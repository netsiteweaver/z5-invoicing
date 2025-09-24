# UOM Concepts Explained

## The Two Key Fields

### 1. `factor_to_base` - Unit Conversion Factor
**Purpose**: Converts between different units within the same dimension

**Examples**:
- 1 kg = 1000 grams → `factor_to_base = 1000`
- 1 liter = 1000 milliliters → `factor_to_base = 1000`
- 1 meter = 1000 millimeters → `factor_to_base = 1000`

### 2. `units_per_uom` - Package Quantity
**Purpose**: How many individual items are in one UOM

**Examples**:
- 1 box contains 10 units → `units_per_uom = 10`
- 1 case contains 24 bottles → `units_per_uom = 24`
- 1 pallet contains 100 boxes → `units_per_uom = 100`

## Visual Examples

### Example 1: Weight Products
```
Product: Rice
Base Unit: Gram

UOM: Kilogram (KG)
- factor_to_base: 1000 (1 kg = 1000 grams)
- units_per_uom: 1 (1 kilogram per UOM)

UOM: Box of 10kg (BOX10KG)
- factor_to_base: 1000 (1 kg = 1000 grams)
- units_per_uom: 10 (10 kilograms per box)

Calculation: 3 boxes of 10kg each
= 3 * 10 * 1000 = 30,000 grams
```

### Example 2: Volume Products
```
Product: Cooking Oil
Base Unit: Milliliter

UOM: Liter (L)
- factor_to_base: 1000 (1 liter = 1000 milliliters)
- units_per_uom: 1 (1 liter per UOM)

UOM: Gallon (GAL)
- factor_to_base: 3785.41 (1 gallon = 3785.41 milliliters)
- units_per_uom: 1 (1 gallon per UOM)

UOM: Case of 12 bottles (CASE12)
- factor_to_base: 1000 (1 liter = 1000 milliliters)
- units_per_uom: 12 (12 liters per case)

Calculation: 2 cases of 12 liters each
= 2 * 12 * 1000 = 24,000 milliliters
```

### Example 3: Count Products
```
Product: Widgets
Base Unit: Unit

UOM: Unit (UNIT)
- factor_to_base: 1 (1 unit = 1 unit)
- units_per_uom: 1 (1 unit per UOM)

UOM: Box of 10 (BOX10)
- factor_to_base: 1 (1 box = 1 base unit)
- units_per_uom: 10 (10 units per box)

UOM: Case of 100 (CASE100)
- factor_to_base: 1 (1 case = 1 base unit)
- units_per_uom: 100 (100 units per case)

Calculation: 5 cases of 100 units each
= 5 * 100 * 1 = 500 units
```

## Real-World Scenarios

### Scenario 1: Receiving Oil in Drums
```
Supplier delivers: 50 drums of 20 liters each
Product: Cooking Oil (base unit: milliliter)

Receipt:
- quantity: 50 (drums)
- uom_quantity: 20 (liters per drum)
- factor_to_base: 1000 (liters to milliliters)
- units_per_uom: 1 (1 drum per UOM)

Total milliliters = 50 * 20 * 1000 = 1,000,000 milliliters
```

### Scenario 2: Receiving Rice in Bags
```
Supplier delivers: 100 bags of 50kg each
Product: Rice (base unit: gram)

Receipt:
- quantity: 100 (bags)
- uom_quantity: 50 (kg per bag)
- factor_to_base: 1000 (kg to grams)
- units_per_uom: 1 (1 bag per UOM)

Total grams = 100 * 50 * 1000 = 5,000,000 grams
```

### Scenario 3: Receiving Widgets in Cases
```
Supplier delivers: 20 cases of 100 units each
Product: Widgets (base unit: unit)

Receipt:
- quantity: 20 (cases)
- uom_quantity: 100 (units per case)
- factor_to_base: 1 (units to units)
- units_per_uom: 1 (1 case per UOM)

Total units = 20 * 100 * 1 = 2,000 units
```

## Key Differences Summary

| Field | Purpose | Example | When to Use |
|-------|---------|---------|-------------|
| `factor_to_base` | Unit conversion | 1 kg = 1000 g | Converting between units |
| `units_per_uom` | Package quantity | 1 box = 10 items | Counting items in packages |

## Formula

### Complete Conversion Formula:
```
base_quantity = quantity * uom_quantity * factor_to_base * units_per_uom
```

### Where:
- `quantity`: Number of UOMs received/sold
- `uom_quantity`: Quantity per UOM (from receipt/sale)
- `factor_to_base`: Conversion factor to base unit
- `units_per_uom`: Items per UOM

## Best Practices

1. **Always use base units** for inventory calculations
2. **Set factor_to_base** for unit conversions
3. **Use units_per_uom** for package quantities
4. **Keep dimensions consistent** (same dimension for related UOMs)
5. **Display in user-friendly units** for UI

## Common Mistakes

❌ **Wrong**: Only using `units_per_uom` for unit conversions
✅ **Correct**: Use both fields appropriately

❌ **Wrong**: Mixing different dimensions
✅ **Correct**: Keep same dimension for related UOMs

❌ **Wrong**: Not converting to base units
✅ **Correct**: Always convert to base units for calculations

This system ensures accurate inventory management while maintaining flexibility for different business needs.
