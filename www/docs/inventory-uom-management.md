# Inventory Management with UOMs

## Overview

The system uses a sophisticated UOM (Unit of Measure) system to handle different types of products and their inventory management. Here's how it works:

## Current Implementation

### 1. Product Setup with UOM

Each product has a base UOM defined:
```php
// Product model has uom_id field
$product = Product::create([
    'name' => 'Cooking Oil',
    'uom_id' => Uom::where('code', 'L')->first()->id, // Liters
    'cost_price' => 15.99,
    'selling_price' => 19.99,
    // ... other fields
]);
```

### 2. Goods Receipt with UOM Conversion

When receiving products, the system converts to base units:

```php
// In GoodsReceiptController
$baseQty = UomService::toBaseUnits((int) $item->quantity, $item->uom_id, $item->uom_quantity);
$inventory->increment('current_stock', $baseQty);
```

**Example Scenarios:**

#### Scenario 1: Receiving Oil in Liters
```php
// Receiving 100 liters of cooking oil
$receiptItem = GoodsReceiptItem::create([
    'product_id' => $oilProduct->id,
    'quantity' => 100,           // 100 liters
    'uom_id' => $literUom->id,   // Liter UOM
    'uom_quantity' => 1,         // 1 liter per UOM
    'unit_cost' => 15.99,
]);

// System converts to base units (milliliters)
$baseQty = UomService::toBaseUnits(100, $literUom->id, 1);
// Result: 100,000 milliliters (base unit for volume)
```

#### Scenario 2: Receiving Oil in Gallons
```php
// Receiving 50 gallons of cooking oil
$receiptItem = GoodsReceiptItem::create([
    'product_id' => $oilProduct->id,
    'quantity' => 50,            // 50 gallons
    'uom_id' => $gallonUom->id,  // Gallon UOM
    'uom_quantity' => 1,         // 1 gallon per UOM
    'unit_cost' => 60.00,
]);

// System converts to base units (milliliters)
$baseQty = UomService::toBaseUnits(50, $gallonUom->id, 1);
// Result: 189,270.5 milliliters (50 * 3785.41)
```

#### Scenario 3: Receiving Boxes of Items
```php
// Receiving 20 boxes of 10 units each
$receiptItem = GoodsReceiptItem::create([
    'product_id' => $widgetProduct->id,
    'quantity' => 20,            // 20 boxes
    'uom_id' => $boxUom->id,     // Box of 10 UOM
    'uom_quantity' => 1,         // 1 box per UOM
    'unit_cost' => 5.00,
]);

// System converts to base units (individual units)
$baseQty = UomService::toBaseUnits(20, $boxUom->id, 1);
// Result: 200 individual units (20 * 10)
```

### 3. Inventory Storage

All inventory is stored in **base units** for consistency:

```php
// Inventory table stores quantities in base units
$inventory = Inventory::create([
    'product_id' => $product->id,
    'department_id' => $department->id,
    'current_stock' => 100000,  // 100,000 milliliters (base unit)
    'min_stock_level' => 10000, // 10,000 milliliters minimum
    'reorder_point' => 5000,    // 5,000 milliliters reorder point
]);
```

### 4. Stock Movements

All stock movements are recorded in base units:

```php
StockMovement::create([
    'product_id' => $product->id,
    'department_id' => $department->id,
    'movement_type' => 'in',
    'quantity' => 100000,  // 100,000 milliliters (base unit)
    'reference_type' => 'goods_receipt',
    'reference_id' => $receiptItem->id,
    'notes' => 'GRN approved',
]);
```

## Best Practices for UOM Management

### 1. Product Definition
- **Always define a base UOM for each product**
- **Choose the most common unit for that product type**
- **Use consistent dimensions** (don't mix weight and volume for same product)

### 2. Receiving Goods
- **Use the UOM that matches your supplier's delivery**
- **System automatically converts to base units**
- **Maintain consistency in your receiving process**

### 3. Inventory Tracking
- **All inventory quantities are in base units**
- **Use UOMService for any conversions**
- **Display quantities in user-friendly units**

### 4. Sales and Orders
- **Allow customers to order in their preferred units**
- **Convert to base units for inventory deduction**
- **Display prices per appropriate unit**

## Example: Complete Inventory Flow

### Step 1: Product Setup
```php
// Create a product for cooking oil
$oilProduct = Product::create([
    'name' => 'Premium Cooking Oil',
    'uom_id' => Uom::where('code', 'L')->first()->id, // Base unit: Liter
    'cost_price' => 15.99,
    'selling_price' => 19.99,
]);
```

### Step 2: Receiving Inventory
```php
// Receive 1000 liters from supplier
$receiptItem = GoodsReceiptItem::create([
    'product_id' => $oilProduct->id,
    'quantity' => 1000,
    'uom_id' => Uom::where('code', 'L')->first()->id,
    'uom_quantity' => 1,
    'unit_cost' => 15.99,
]);

// System converts: 1000 liters = 1,000,000 milliliters (base unit)
$baseQty = UomService::toBaseUnits(1000, $literUom->id, 1);
// Inventory updated with 1,000,000 milliliters
```

### Step 3: Sales
```php
// Customer orders 5 gallons
$saleItem = SaleItem::create([
    'product_id' => $oilProduct->id,
    'quantity' => 5,
    'uom_id' => Uom::where('code', 'GAL')->first()->id,
    'uom_quantity' => 1,
    'unit_price' => 75.00, // $75 per gallon
]);

// System converts: 5 gallons = 18,927.05 milliliters (base unit)
$baseQty = UomService::toBaseUnits(5, $gallonUom->id, 1);
// Inventory reduced by 18,927.05 milliliters
```

### Step 4: Inventory Display
```php
// Display current stock in liters
$currentStock = $inventory->current_stock; // 981,072.95 milliliters
$literUom = Uom::where('code', 'L')->first();
$stockInLiters = UomService::fromBaseValue($currentStock, $literUom->id);
// Result: 981.07 liters
```

## UOM Conversion Examples

### Weight Products
```php
// Product: Rice (base unit: gram)
$riceProduct = Product::create([
    'name' => 'Basmati Rice',
    'uom_id' => Uom::where('code', 'G')->first()->id, // Base: gram
]);

// Receiving 50 kg of rice
$baseQty = UomService::toBaseUnits(50, $kgUom->id, 1);
// Result: 50,000 grams

// Selling 2 pounds of rice
$baseQty = UomService::toBaseUnits(2, $poundUom->id, 1);
// Result: 907.184 grams
```

### Volume Products
```php
// Product: Milk (base unit: milliliter)
$milkProduct = Product::create([
    'name' => 'Fresh Milk',
    'uom_id' => Uom::where('code', 'ML')->first()->id, // Base: milliliter
]);

// Receiving 100 liters of milk
$baseQty = UomService::toBaseUnits(100, $literUom->id, 1);
// Result: 100,000 milliliters

// Selling 1 gallon of milk
$baseQty = UomService::toBaseUnits(1, $gallonUom->id, 1);
// Result: 3,785.41 milliliters
```

## Key Benefits

1. **Consistency**: All inventory in base units
2. **Flexibility**: Receive and sell in different units
3. **Accuracy**: Precise conversions using factors
4. **Scalability**: Easy to add new UOMs
5. **Auditability**: Clear conversion trail

## Important Notes

- **Always use base units for inventory calculations**
- **Convert at the point of entry/exit**
- **Maintain UOM relationships properly**
- **Use the UomService for all conversions**
- **Validate UOM compatibility before operations**

This system ensures accurate inventory management regardless of the units used for receiving, storing, or selling products.
