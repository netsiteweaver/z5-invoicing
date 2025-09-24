# UOM Practical Examples

## Real-World Scenarios

### Scenario 1: Cooking Oil Business

**Product**: Premium Cooking Oil
**Base UOM**: Liter (L)
**Supplier**: Delivers in 20-liter drums
**Customers**: Buy in various sizes (500ml, 1L, 5L, 1 gallon)

#### Setup
```php
// Create product with base UOM
$oilProduct = Product::create([
    'name' => 'Premium Cooking Oil',
    'uom_id' => Uom::where('code', 'L')->first()->id, // Base: Liter
    'cost_price' => 15.99,
    'selling_price' => 19.99,
]);

// Create inventory
$inventory = Inventory::create([
    'product_id' => $oilProduct->id,
    'department_id' => 1,
    'current_stock' => 0,
    'min_stock_level' => 100000, // 100 liters minimum
    'reorder_point' => 50000,   // 50 liters reorder point
]);
```

#### Receiving Goods
```php
// Receive 50 drums of 20 liters each (1000 liters total)
$receiptItem = GoodsReceiptItem::create([
    'product_id' => $oilProduct->id,
    'quantity' => 50,           // 50 drums
    'uom_id' => Uom::where('code', 'L')->first()->id,
    'uom_quantity' => 20,       // 20 liters per drum
    'unit_cost' => 15.99,
]);

// System converts: 50 * 20 = 1000 liters = 1,000,000 milliliters
$baseQty = UomService::toBaseUnits(50, $literUom->id, 20);
// Inventory updated: +1,000,000 milliliters
```

#### Sales Examples
```php
// Customer 1: Buys 5 liters
$sale1 = SaleItem::create([
    'product_id' => $oilProduct->id,
    'quantity' => 5,
    'uom_id' => Uom::where('code', 'L')->first()->id,
    'uom_quantity' => 1,
    'unit_price' => 19.99,
]);
// Stock reduced: -5,000 milliliters

// Customer 2: Buys 2 gallons
$sale2 = SaleItem::create([
    'product_id' => $oilProduct->id,
    'quantity' => 2,
    'uom_id' => Uom::where('code', 'GAL')->first()->id,
    'uom_quantity' => 1,
    'unit_price' => 75.00, // $75 per gallon
]);
// Stock reduced: -7,570.82 milliliters (2 * 3785.41)

// Customer 3: Buys 10 bottles of 500ml each
$sale3 = SaleItem::create([
    'product_id' => $oilProduct->id,
    'quantity' => 10,
    'uom_id' => Uom::where('code', 'ML')->first()->id,
    'uom_quantity' => 500,  // 500ml per bottle
    'unit_price' => 0.50,    // $0.50 per 500ml
]);
// Stock reduced: -5,000 milliliters (10 * 500)
```

#### Stock Display
```php
// Current stock: 982,429.18 milliliters
$currentStock = $inventory->current_stock;

// Display in different units
$stockInLiters = UomService::fromBaseValue($currentStock, $literUom->id);
$stockInGallons = UomService::fromBaseValue($currentStock, $gallonUom->id);

echo "Current stock: {$stockInLiters} liters or {$stockInGallons} gallons";
// Output: Current stock: 982.43 liters or 259.57 gallons
```

### Scenario 2: Rice Business

**Product**: Basmati Rice
**Base UOM**: Gram (G)
**Supplier**: Delivers in 50kg bags
**Customers**: Buy in various weights (1kg, 5kg, 10kg, 1 pound)

#### Setup
```php
// Create product with base UOM
$riceProduct = Product::create([
    'name' => 'Premium Basmati Rice',
    'uom_id' => Uom::where('code', 'G')->first()->id, // Base: Gram
    'cost_price' => 0.05,  // $0.05 per gram
    'selling_price' => 0.08, // $0.08 per gram
]);
```

#### Receiving Goods
```php
// Receive 100 bags of 50kg each (5000kg total)
$receiptItem = GoodsReceiptItem::create([
    'product_id' => $riceProduct->id,
    'quantity' => 100,           // 100 bags
    'uom_id' => Uom::where('code', 'KG')->first()->id,
    'uom_quantity' => 50,       // 50kg per bag
    'unit_cost' => 0.05,
]);

// System converts: 100 * 50 = 5000kg = 5,000,000 grams
$baseQty = UomService::toBaseUnits(100, $kgUom->id, 50);
// Inventory updated: +5,000,000 grams
```

#### Sales Examples
```php
// Customer 1: Buys 10kg
$sale1 = SaleItem::create([
    'product_id' => $riceProduct->id,
    'quantity' => 10,
    'uom_id' => Uom::where('code', 'KG')->first()->id,
    'uom_quantity' => 1,
    'unit_price' => 80.00, // $80 per 10kg
]);
// Stock reduced: -10,000 grams

// Customer 2: Buys 5 pounds
$sale2 = SaleItem::create([
    'product_id' => $riceProduct->id,
    'quantity' => 5,
    'uom_id' => Uom::where('code', 'LB')->first()->id,
    'uom_quantity' => 1,
    'unit_price' => 18.00, // $18 per 5 pounds
]);
// Stock reduced: -2,267.96 grams (5 * 453.592)
```

### Scenario 3: Mixed Inventory Management

**Products**: Various items with different UOMs
**Challenge**: Manage inventory across different dimensions

#### Products Setup
```php
// Weight-based product
$flourProduct = Product::create([
    'name' => 'All-Purpose Flour',
    'uom_id' => Uom::where('code', 'G')->first()->id, // Base: Gram
    'cost_price' => 0.02,
    'selling_price' => 0.03,
]);

// Volume-based product
$milkProduct = Product::create([
    'name' => 'Fresh Milk',
    'uom_id' => Uom::where('code', 'ML')->first()->id, // Base: Milliliter
    'cost_price' => 0.001,
    'selling_price' => 0.002,
]);

// Count-based product
$bottleProduct = Product::create([
    'name' => 'Glass Bottles',
    'uom_id' => Uom::where('code', 'UNIT')->first()->id, // Base: Unit
    'cost_price' => 2.00,
    'selling_price' => 3.00,
]);
```

#### Inventory Management
```php
// Add inventory for each product
InventoryUomService::addInventory(
    $flourProduct->id,
    1, // department
    1000, // 1000kg
    Uom::where('code', 'KG')->first()->id,
    1,
    'manual',
    null,
    'INITIAL_STOCK',
    'Initial stock setup'
);

InventoryUomService::addInventory(
    $milkProduct->id,
    1, // department
    500, // 500 liters
    Uom::where('code', 'L')->first()->id,
    1,
    'manual',
    null,
    'INITIAL_STOCK',
    'Initial stock setup'
);

InventoryUomService::addInventory(
    $bottleProduct->id,
    1, // department
    1000, // 1000 units
    Uom::where('code', 'UNIT')->first()->id,
    1,
    'manual',
    null,
    'INITIAL_STOCK',
    'Initial stock setup'
);
```

#### Stock Checking
```php
// Check stock levels for each product
$flourStock = InventoryUomService::getStockSummary($flourProduct->id, 1);
$milkStock = InventoryUomService::getStockSummary($milkProduct->id, 1);
$bottleStock = InventoryUomService::getStockSummary($bottleProduct->id, 1);

// Display stock levels
echo "Flour: " . $flourStock['stock_levels'][0]['quantity'] . " kg\n";
echo "Milk: " . $milkStock['stock_levels'][1]['quantity'] . " liters\n";
echo "Bottles: " . $bottleStock['stock_levels'][0]['quantity'] . " units\n";
```

## Key Benefits Demonstrated

1. **Flexibility**: Receive and sell in different units
2. **Accuracy**: Precise conversions maintain data integrity
3. **Scalability**: Easy to add new products and UOMs
4. **User-Friendly**: Display quantities in appropriate units
5. **Consistency**: All calculations use base units internally

## Best Practices

1. **Choose appropriate base units** for each product type
2. **Use consistent UOMs** for similar products
3. **Validate conversions** before processing
4. **Display quantities** in user-friendly units
5. **Maintain audit trails** for all conversions

This system ensures accurate inventory management regardless of the units used for receiving, storing, or selling products.
