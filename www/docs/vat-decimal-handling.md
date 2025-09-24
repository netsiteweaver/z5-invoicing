# VAT Decimal Handling Guide

## Problem Description

When users enter prices, they often think in VAT-inclusive terms (e.g., "I want to sell this for Rs 1000 including VAT"). However, the system stores cost prices as VAT-exclusive. This creates decimal values that need proper handling.

**Example:**
- User enters: Rs 1000 (VAT-inclusive)
- System stores: Rs 869.57 (VAT-exclusive)
- VAT amount: Rs 130.43 (15% of 869.57)

## Solution Implementation

### 1. VAT Calculation Service

The `VatCalculationService` handles all VAT calculations with proper decimal rounding:

```php
use App\Services\VatCalculationService;

// Convert user input (VAT-inclusive) to storage format (VAT-exclusive)
$userInput = 1000.00; // User enters Rs 1000
$exclusivePrice = VatCalculationService::convertUserInputToExclusive(
    $userInput, 
    'inclusive', // User entered VAT-inclusive price
    'standard'  // 15% VAT rate
);
// Result: 869.57 (stored in database)

// Convert stored price to display format
$displayPrice = VatCalculationService::convertExclusiveToDisplay(
    $exclusivePrice, 
    'inclusive', // Show VAT-inclusive price to user
    'standard'
);
// Result: 1000.00 (displayed to user)
```

### 2. Product Model Enhancements

The Product model now includes VAT calculation methods:

```php
$product = Product::find(1);

// Get VAT breakdown
$breakdown = $product->vat_breakdown;
/*
Result:
[
    'exclusive_price' => 869.57,
    'vat_rate' => 0.15,
    'vat_rate_percent' => 15.0,
    'vat_amount' => 130.43,
    'inclusive_price' => 1000.00
]
*/

// Get display price
$displayPrice = $product->getDisplayPrice('inclusive'); // 1000.00
$exclusivePrice = $product->getDisplayPrice('exclusive'); // 869.57
```

### 3. Real-World Examples

#### Example 1: Product Creation
```php
// User enters: Rs 1000 (VAT-inclusive)
$userInput = 1000.00;
$taxType = 'standard'; // 15% VAT

// Convert to VAT-exclusive for storage
$exclusivePrice = Product::convertUserInputToExclusive(
    $userInput, 
    'inclusive', 
    $taxType
);
// Result: 869.57

// Create product
$product = Product::create([
    'name' => 'Chocolate Cake',
    'cost_price' => $exclusivePrice, // 869.57
    'selling_price' => $exclusivePrice,
    'tax_type' => $taxType,
    // ... other fields
]);
```

#### Example 2: Display to User
```php
// Display VAT-inclusive price to user
$product = Product::find(1);
$displayPrice = $product->getDisplayPrice('inclusive');
echo "Price: Rs " . number_format($displayPrice, 2); // Rs 1,000.00

// Show VAT breakdown
$breakdown = $product->vat_breakdown;
echo "Exclusive: Rs " . number_format($breakdown['exclusive_price'], 2);
echo "VAT (" . $breakdown['vat_rate_percent'] . "%): Rs " . number_format($breakdown['vat_amount'], 2);
echo "Inclusive: Rs " . number_format($breakdown['inclusive_price'], 2);
```

#### Example 3: Goods Receipt Calculation
```php
use App\Services\VatCalculationService;

// Calculate line total for goods receipt
$unitCost = 869.57; // VAT-exclusive
$quantity = 10;
$taxType = 'standard';

$lineTotal = VatCalculationService::calculateLineTotal($unitCost, $quantity, $taxType);
/*
Result:
[
    'gross_amount' => 8695.70,    // 869.57 * 10
    'vat_rate' => 0.15,
    'vat_amount' => 1304.36,      // 8695.70 * 0.15
    'total_with_vat' => 10000.06  // 8695.70 + 1304.36
]
*/
```

### 4. Frontend JavaScript Integration

Update the goods receipt form to handle VAT calculations properly:

```javascript
// VAT calculation with proper decimal handling
function calculateVatAmount(exclusivePrice, taxType = 'standard') {
    const vatRates = {
        'standard': 0.15,
        'zero': 0.00,
        'exempt': 0.00
    };
    
    const vatRate = vatRates[taxType] || 0.15;
    const vatAmount = exclusivePrice * vatRate;
    
    // Round to 2 decimal places to avoid floating point issues
    return Math.round(vatAmount * 100) / 100;
}

function calculateInclusivePrice(exclusivePrice, taxType = 'standard') {
    const vatAmount = calculateVatAmount(exclusivePrice, taxType);
    return Math.round((exclusivePrice + vatAmount) * 100) / 100;
}

function calculateExclusivePrice(inclusivePrice, taxType = 'standard') {
    const vatRates = {
        'standard': 0.15,
        'zero': 0.00,
        'exempt': 0.00
    };
    
    const vatRate = vatRates[taxType] || 0.15;
    if (vatRate === 0) return inclusivePrice;
    
    // Formula: Exclusive = Inclusive / (1 + VAT Rate)
    const exclusivePrice = inclusivePrice / (1 + vatRate);
    return Math.round(exclusivePrice * 100) / 100;
}
```

### 5. Database Storage Best Practices

```php
// Always store VAT-exclusive prices in database
$product = Product::create([
    'name' => 'Chocolate Cake',
    'cost_price' => 869.57,        // VAT-exclusive (stored)
    'selling_price' => 869.57,     // VAT-exclusive (stored)
    'tax_type' => 'standard',     // 15% VAT
    // ... other fields
]);

// Display VAT-inclusive prices to users
$displayPrice = $product->getDisplayPrice('inclusive'); // 1000.00
```

### 6. Common Scenarios

#### Scenario 1: User enters VAT-inclusive price
```php
// User input: Rs 1000 (including VAT)
$userInput = 1000.00;
$exclusivePrice = VatCalculationService::convertUserInputToExclusive(
    $userInput, 
    'inclusive', 
    'standard'
);
// Stored: 869.57
// Displayed: 1000.00
```

#### Scenario 2: User enters VAT-exclusive price
```php
// User input: Rs 869.57 (excluding VAT)
$userInput = 869.57;
$exclusivePrice = VatCalculationService::convertUserInputToExclusive(
    $userInput, 
    'exclusive', 
    'standard'
);
// Stored: 869.57
// Displayed: 1000.00 (if showing inclusive)
```

#### Scenario 3: Zero-rated products
```php
// Zero-rated product
$product = Product::create([
    'name' => 'Zero-rated Item',
    'cost_price' => 1000.00,       // No VAT
    'tax_type' => 'zero',          // 0% VAT
]);

$displayPrice = $product->getDisplayPrice('inclusive'); // 1000.00 (same as exclusive)
```

## Key Benefits

✅ **Accurate Calculations**: Proper decimal handling prevents rounding errors
✅ **Flexible Input**: Handle both VAT-inclusive and VAT-exclusive user input
✅ **Consistent Storage**: Always store VAT-exclusive prices in database
✅ **User-Friendly Display**: Show appropriate prices to users
✅ **Audit Trail**: Clear VAT breakdown for compliance

## Best Practices

1. **Always store VAT-exclusive prices** in the database
2. **Use the VatCalculationService** for all VAT calculations
3. **Round to 2 decimal places** to avoid floating point issues
4. **Display VAT-inclusive prices** to users by default
5. **Provide VAT breakdown** for transparency
6. **Handle different tax types** (standard, zero, exempt)

This solution ensures accurate VAT handling while maintaining user-friendly interfaces and proper decimal precision.
