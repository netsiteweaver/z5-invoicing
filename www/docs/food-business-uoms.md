# UOMs for Food Business (Cakes & Drinks)

## Overview
The UOM system has been optimized for food businesses dealing with cakes and drinks. Unnecessary dimensions (time, temperature, currency, area) have been removed.

## Available Dimensions

### 1. **COUNT DIMENSION** - For counting items
- **Unit (UNIT)** - Base unit for counting individual items
- **Box of 10 (BOX10)** - 10 individual items per box
- **Pack of 100 (PACK100)** - 100 individual items per pack

**Use cases:**
- Counting individual cakes, cupcakes, cookies
- Counting bottles, cans, containers
- Counting packages, boxes, cases

### 2. **WEIGHT DIMENSION** - For measuring weight
- **Gram (G)** - Base unit for weight
- **Kilogram (KG)** - 1000 grams
- **Pound (LB)** - 453.592 grams

**Use cases:**
- Measuring flour, sugar, butter for baking
- Measuring meat, cheese, vegetables
- Measuring ingredients by weight

### 3. **VOLUME DIMENSION** - For measuring liquids
- **Milliliter (ML)** - Base unit for volume
- **Liter (L)** - 1000 milliliters
- **Gallon (GAL)** - 3785.41 milliliters

**Use cases:**
- Measuring milk, water, oil for recipes
- Measuring drinks, juices, beverages
- Measuring liquid ingredients

### 4. **LENGTH DIMENSION** - For measuring dimensions
- **Millimeter (MM)** - Base unit for length
- **Centimeter (CM)** - 10 millimeters
- **Meter (M)** - 1000 millimeters
- **Inch (IN)** - 25.4 millimeters
- **Foot (FT)** - 304.8 millimeters

**Use cases:**
- Measuring cake dimensions
- Measuring packaging sizes
- Measuring equipment dimensions

## Real-World Examples

### Example 1: Cake Business
```php
// Product: Chocolate Cake
$cakeProduct = Product::create([
    'name' => 'Chocolate Cake',
    'uom_id' => Uom::where('code', 'UNIT')->first()->id, // Count dimension
    'cost_price' => 15.00,
    'selling_price' => 25.00,
]);

// Receiving ingredients
$flourReceipt = GoodsReceiptItem::create([
    'product_id' => $flourProduct->id,
    'quantity' => 50,           // 50 kg
    'uom_id' => Uom::where('code', 'KG')->first()->id,
    'uom_quantity' => 1,
    'unit_cost' => 2.50,
]);

$milkReceipt = GoodsReceiptItem::create([
    'product_id' => $milkProduct->id,
    'quantity' => 100,          // 100 liters
    'uom_id' => Uom::where('code', 'L')->first()->id,
    'uom_quantity' => 1,
    'unit_cost' => 1.20,
]);
```

### Example 2: Drinks Business
```php
// Product: Fresh Orange Juice
$juiceProduct = Product::create([
    'name' => 'Fresh Orange Juice',
    'uom_id' => Uom::where('code', 'ML')->first()->id, // Volume dimension
    'cost_price' => 0.05,
    'selling_price' => 0.10,
]);

// Receiving in gallons
$juiceReceipt = GoodsReceiptItem::create([
    'product_id' => $juiceProduct->id,
    'quantity' => 10,           // 10 gallons
    'uom_id' => Uom::where('code', 'GAL')->first()->id,
    'uom_quantity' => 1,
    'unit_cost' => 15.00,
]);
```

### Example 3: Mixed Inventory
```php
// Cakes (count)
$cakeStock = InventoryUomService::getStockInUom($cakeProduct->id, 1, $unitUom->id);
echo "Cakes in stock: {$cakeStock} units";

// Flour (weight)
$flourStock = InventoryUomService::getStockInUom($flourProduct->id, 1, $kgUom->id);
echo "Flour in stock: {$flourStock} kg";

// Milk (volume)
$milkStock = InventoryUomService::getStockInUom($milkProduct->id, 1, $literUom->id);
echo "Milk in stock: {$milkStock} liters";
```

## Best Practices for Food Business

### 1. **Product Setup**
- **Cakes, pastries**: Use COUNT dimension (units)
- **Ingredients**: Use WEIGHT dimension (grams/kg)
- **Liquids**: Use VOLUME dimension (ml/liters)
- **Packaging**: Use LENGTH/AREA dimensions

### 2. **Inventory Management**
- **Always convert to base units** for calculations
- **Display in user-friendly units** for UI
- **Use appropriate UOMs** for each product type
- **Maintain consistency** within product categories

### 3. **Common Scenarios**
- **Receiving flour**: 50kg bags → convert to grams
- **Selling cakes**: 1 unit per cake → convert to units
- **Mixing drinks**: 2 liters per batch → convert to milliliters
- **Packaging**: 12-inch boxes → convert to millimeters

## Removed Dimensions

The following dimensions have been removed as they're not relevant for food business:
- ❌ **Time** (seconds, minutes, hours)
- ❌ **Temperature** (Celsius, Fahrenheit)
- ❌ **Currency** (dollars, cents)
- ❌ **Area** (square millimeters, square centimeters, square meters)

## Benefits

✅ **Focused**: Only relevant UOMs for food business
✅ **Clean**: No unnecessary dimensions
✅ **Efficient**: Faster processing and validation
✅ **User-friendly**: Easier to understand and use
✅ **Scalable**: Easy to add new UOMs as needed

This optimized UOM system is perfect for managing inventory in cakes and drinks businesses!
