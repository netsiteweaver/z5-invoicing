# Sales Management System

## Overview
The Sales Management system handles the conversion of orders to sales, tracks sales transactions, and manages the sales lifecycle. It provides comprehensive sales tracking, status management, and integration with orders and payment systems.

## Core Features

### Sales Creation
- **Direct Sales**: Create sales directly without orders
- **Order Conversion**: Convert confirmed orders to sales
- **Sales Number Generation**: Auto-generated with format `SAL{YYYY}{MM}{####}` (e.g., SAL2025010001)
- **Customer Selection**: Integration with customer database
- **Product Selection**: Multi-product sales with quantity and pricing
- **Sales Details**: Date, delivery date, notes, internal notes

### Sales Items Management
- **Product Selection**: Search and select from product catalog
- **Quantity Management**: Set quantities for each product
- **Pricing**: Unit price, discount percentage/amount, tax calculation
- **Line Totals**: Automatic calculation of line item totals
- **Order Item Linking**: Link sales items to original order items
- **Notes**: Per-item notes and special instructions

### Sales Status Workflow
- **Draft**: Initial sales creation state
- **Pending**: Sales submitted and awaiting confirmation
- **Confirmed**: Sales confirmed and ready for processing
- **Processing**: Sales being prepared/fulfilled
- **Shipped**: Sales dispatched for delivery
- **Delivered**: Sales successfully delivered
- **Cancelled**: Sales cancelled (with reason tracking)

### Sales Management Operations
- **View Sales**: List all sales with filtering and search
- **Edit Sales**: Modify sales details and items
- **Delete Sales**: Soft delete with audit trail
- **Status Updates**: Change sales status with history tracking
- **Sales History**: Complete audit trail of sales changes

## Database Schema

### Sales Table
```sql
CREATE TABLE sales (
    sale_id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    sale_number VARCHAR(20) UNIQUE NOT NULL,
    order_id INT NULL,
    customer_id INT NOT NULL,
    sale_date DATE NOT NULL,
    delivery_date DATE NULL,
    sale_status ENUM('draft','pending','confirmed','processing','shipped','delivered','cancelled') DEFAULT 'draft',
    payment_status ENUM('pending','partial','paid','overdue') DEFAULT 'pending',
    payment_method VARCHAR(50) NULL,
    subtotal DECIMAL(10,2) DEFAULT 0.00,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    shipping_amount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) DEFAULT 0.00,
    currency VARCHAR(3) DEFAULT 'MUR',
    notes TEXT NULL,
    internal_notes TEXT NULL,
    created_by INT NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_by INT NULL,
    updated_date DATETIME NULL,
    status TINYINT DEFAULT 1,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);
```

### Sales Items Table
```sql
CREATE TABLE sales_items (
    sale_item_id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    sale_id INT NOT NULL,
    order_item_id INT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    discount_percent DECIMAL(5,2) DEFAULT 0.00,
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    tax_percent DECIMAL(5,2) DEFAULT 0.00,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    line_total DECIMAL(10,2) NOT NULL,
    notes TEXT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    FOREIGN KEY (sale_id) REFERENCES sales(sale_id),
    FOREIGN KEY (order_item_id) REFERENCES order_items(order_item_id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

### Sales Status History Table
```sql
CREATE TABLE sales_status_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    sale_id INT NOT NULL,
    status_from VARCHAR(20) NULL,
    status_to VARCHAR(20) NOT NULL,
    notes TEXT NULL,
    created_by INT NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES sales(sale_id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

## API Endpoints

### Sales Management
- `GET /api/sales` - List all sales with filtering
- `POST /api/sales` - Create new sale
- `POST /api/sales/from-order/{order_uuid}` - Create sale from order
- `GET /api/sales/{uuid}` - Get sale details
- `PUT /api/sales/{uuid}` - Update sale
- `DELETE /api/sales/{uuid}` - Soft delete sale
- `PATCH /api/sales/{uuid}/status` - Update sale status

### Sales Items
- `GET /api/sales/{uuid}/items` - Get sales items
- `POST /api/sales/{uuid}/items` - Add sales item
- `PUT /api/sales/{uuid}/items/{item_id}` - Update sales item
- `DELETE /api/sales/{uuid}/items/{item_id}` - Remove sales item

### Sales Status
- `GET /api/sales/{uuid}/status-history` - Get status history
- `POST /api/sales/{uuid}/status` - Update sale status

## Business Logic

### Sales Number Generation
```php
private function generateSaleNumber(): string
{
    $prefix = 'SAL';
    $year = date('Y');
    $month = date('m');
    
    $lastSale = Sale::whereYear('created_date', $year)
        ->whereMonth('created_date', $month)
        ->orderBy('sale_id', 'desc')
        ->first();
    
    $sequence = $lastSale ? 
        intval(substr($lastSale->sale_number, -4)) + 1 : 1;
    
    return $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
}
```

### Order to Sales Conversion
```php
public function createSaleFromOrder(Order $order, array $selectedItems = null): Sale
{
    $sale = Sale::create([
        'uuid' => Str::uuid(),
        'sale_number' => $this->generateSaleNumber(),
        'order_id' => $order->order_id,
        'customer_id' => $order->customer_id,
        'sale_date' => now()->toDateString(),
        'delivery_date' => $order->delivery_date,
        'sale_status' => 'draft',
        'payment_status' => 'pending',
        'subtotal' => $order->subtotal,
        'tax_amount' => $order->tax_amount,
        'discount_amount' => $order->discount_amount,
        'shipping_amount' => $order->shipping_amount,
        'total_amount' => $order->total_amount,
        'currency' => $order->currency,
        'notes' => $order->notes,
        'internal_notes' => $order->internal_notes,
        'created_by' => auth()->id(),
        'status' => 1
    ]);
    
    // Convert order items to sales items
    $orderItems = $selectedItems ? 
        $order->items()->whereIn('order_item_id', $selectedItems)->get() :
        $order->items;
    
    foreach ($orderItems as $orderItem) {
        $sale->items()->create([
            'uuid' => Str::uuid(),
            'order_item_id' => $orderItem->order_item_id,
            'product_id' => $orderItem->product_id,
            'quantity' => $orderItem->quantity,
            'unit_price' => $orderItem->unit_price,
            'discount_percent' => $orderItem->discount_percent,
            'discount_amount' => $orderItem->discount_amount,
            'tax_percent' => $orderItem->tax_percent,
            'tax_amount' => $orderItem->tax_amount,
            'line_total' => $orderItem->line_total,
            'notes' => $orderItem->notes,
            'status' => 1
        ]);
    }
    
    return $sale;
}
```

### Sales Total Calculation
```php
public function calculateTotals(array $items): array
{
    $subtotal = 0;
    $totalTax = 0;
    $totalDiscount = 0;
    
    foreach ($items as $item) {
        $lineTotal = $item['quantity'] * $item['unit_price'];
        $discountAmount = $lineTotal * ($item['discount_percent'] / 100);
        $taxableAmount = $lineTotal - $discountAmount;
        $taxAmount = $taxableAmount * ($item['tax_percent'] / 100);
        
        $subtotal += $lineTotal;
        $totalDiscount += $discountAmount;
        $totalTax += $taxAmount;
    }
    
    return [
        'subtotal' => $subtotal,
        'discount_amount' => $totalDiscount,
        'tax_amount' => $totalTax,
        'total_amount' => $subtotal - $totalDiscount + $totalTax
    ];
}
```

## User Interface Components

### Sales List View
- **Data Table**: Sortable, filterable sales listing
- **Status Filters**: Quick filter by sales status
- **Search**: Search by sales number, customer name, or notes
- **Actions**: View, Edit, Delete, Status Update buttons
- **Pagination**: Efficient handling of large sales volumes

### Sales Form
- **Customer Selection**: Autocomplete customer search
- **Product Selection**: Search and add products with pricing
- **Item Management**: Add/remove/edit sales items
- **Pricing Summary**: Real-time total calculation
- **Status Management**: Status dropdown with workflow validation

### Order to Sales Conversion
- **Order Selection**: Select order for conversion
- **Item Selection**: Choose which order items to include
- **Pricing Review**: Review and adjust pricing if needed
- **Conversion Confirmation**: Confirm conversion with summary

### Sales Detail View
- **Sales Header**: Customer info, dates, status, totals
- **Sales Items**: Detailed item listing with pricing
- **Status History**: Timeline of status changes
- **Actions**: Edit, Status Update, Generate PDF, Process Payment

## Integration Points

### Orders System
- **Order Conversion**: Convert orders to sales
- **Item Linking**: Link sales items to original order items
- **Status Sync**: Update order status when sales are processed
- **Data Consistency**: Maintain data consistency between orders and sales

### Payment System
- **Payment Tracking**: Link payments to sales
- **Status Updates**: Update payment status on sales
- **Outstanding**: Track outstanding payments
- **Payment Methods**: Track payment methods used

### Customer Management
- **Customer Data**: Pull customer information for sales
- **Sales History**: Track customer sales history
- **Communication**: Send sales updates to customers

### Inventory System
- **Stock Check**: Verify product availability
- **Reservation**: Reserve stock for confirmed sales
- **Updates**: Update inventory when sales are processed
- **Stock Allocation**: Allocate stock to specific sales

## Business Rules

### Sales Creation Rules
- Sales can be created directly or from orders
- Sales numbers must be unique
- Customer must be selected and active
- At least one product must be included
- Quantities must be positive

### Status Transition Rules
- Draft → Pending: Basic validation passed
- Pending → Confirmed: Customer confirmation received
- Confirmed → Processing: Stock allocated and preparation started
- Processing → Shipped: Items dispatched
- Shipped → Delivered: Customer received items
- Any Status → Cancelled: With valid cancellation reason

### Order Conversion Rules
- Only confirmed orders can be converted to sales
- Partial conversion allowed (select specific items)
- Original order status updated when sales created
- Sales inherit order pricing and customer data

## Security Considerations

### Access Control
- **Role-based Access**: Different permissions for different user levels
- **Sales Ownership**: Users can only modify sales they created (unless admin)
- **Status Restrictions**: Only authorized users can change certain statuses

### Data Validation
- **Input Validation**: Validate all sales data before saving
- **Business Rules**: Enforce business rules (e.g., positive quantities)
- **Status Transitions**: Validate status change workflows

### Audit Trail
- **Change Tracking**: Log all sales modifications
- **User Attribution**: Track who made changes and when
- **Status History**: Complete history of status changes

## Performance Considerations

### Database Optimization
- **Indexing**: Proper indexes on frequently queried fields
- **Pagination**: Efficient pagination for large sales lists
- **Caching**: Cache frequently accessed sales data

### Query Optimization
- **Eager Loading**: Load related data efficiently
- **Selective Loading**: Only load necessary data for list views
- **Search Optimization**: Optimized search queries

## Testing Requirements

### Unit Tests
- Sales creation and validation
- Sales number generation
- Order to sales conversion
- Total calculations
- Status transitions

### Integration Tests
- Order integration
- Payment integration
- Customer data integration
- Inventory updates

### User Acceptance Tests
- Sales creation workflow
- Order to sales conversion
- Sales management operations
- Status update workflows
- Search and filtering

## Migration Notes

### From CodeIgniter
- **Model Structure**: Convert CI models to Laravel Eloquent models
- **Controller Logic**: Adapt CI controllers to Laravel controllers
- **Database Queries**: Convert CI query builder to Eloquent
- **Validation**: Replace CI validation with Laravel validation

### Data Migration
- **Sales Data**: Migrate existing sales with UUID preservation
- **Sales Items**: Migrate sales items with relationships
- **Status History**: Preserve complete status history
- **Order Links**: Maintain order-sales relationships
- **Audit Trail**: Maintain audit trail continuity
