# Orders Management System

## Overview
The Orders Management system handles the complete lifecycle of customer orders from creation to delivery. It provides comprehensive order tracking, status management, and integration with sales and payment systems.

## Core Features

### Order Creation
- **Order Number Generation**: Auto-generated with format `ORD{YYYY}{MM}{####}` (e.g., ORD2025010001)
- **Customer Selection**: Integration with customer database
- **Product Selection**: Multi-product order with quantity and pricing
- **Order Details**: Date, delivery date, notes, internal notes
- **Pricing Calculation**: Subtotal, tax, discount, shipping, total amount
- **Currency Support**: Multi-currency support (default: MUR)

### Order Items Management
- **Product Selection**: Search and select from product catalog
- **Quantity Management**: Set quantities for each product
- **Pricing**: Unit price, discount percentage/amount, tax calculation
- **Line Totals**: Automatic calculation of line item totals
- **Notes**: Per-item notes and special instructions

### Order Status Workflow
- **Draft**: Initial order creation state
- **Pending**: Order submitted and awaiting confirmation
- **Confirmed**: Order confirmed and ready for processing
- **Processing**: Order being prepared/fulfilled
- **Shipped**: Order dispatched for delivery
- **Delivered**: Order successfully delivered
- **Cancelled**: Order cancelled (with reason tracking)

### Order Management Operations
- **View Orders**: List all orders with filtering and search
- **Edit Orders**: Modify order details and items
- **Delete Orders**: Soft delete with audit trail
- **Status Updates**: Change order status with history tracking
- **Order History**: Complete audit trail of order changes

## Database Schema

### Orders Table
```sql
CREATE TABLE orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    order_number VARCHAR(20) UNIQUE NOT NULL,
    customer_id INT NOT NULL,
    order_date DATE NOT NULL,
    delivery_date DATE NULL,
    order_status ENUM('draft','pending','confirmed','processing','shipped','delivered','cancelled') DEFAULT 'draft',
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
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);
```

### Order Items Table
```sql
CREATE TABLE order_items (
    order_item_id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    order_id INT NOT NULL,
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
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

### Order Status History Table
```sql
CREATE TABLE order_status_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    order_id INT NOT NULL,
    status_from VARCHAR(20) NULL,
    status_to VARCHAR(20) NOT NULL,
    notes TEXT NULL,
    created_by INT NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

## API Endpoints

### Order Management
- `GET /api/orders` - List all orders with filtering
- `POST /api/orders` - Create new order
- `GET /api/orders/{uuid}` - Get order details
- `PUT /api/orders/{uuid}` - Update order
- `DELETE /api/orders/{uuid}` - Soft delete order
- `PATCH /api/orders/{uuid}/status` - Update order status

### Order Items
- `GET /api/orders/{uuid}/items` - Get order items
- `POST /api/orders/{uuid}/items` - Add order item
- `PUT /api/orders/{uuid}/items/{item_id}` - Update order item
- `DELETE /api/orders/{uuid}/items/{item_id}` - Remove order item

### Order Status
- `GET /api/orders/{uuid}/status-history` - Get status history
- `POST /api/orders/{uuid}/status` - Update order status

## Business Logic

### Order Number Generation
```php
private function generateOrderNumber(): string
{
    $prefix = 'ORD';
    $year = date('Y');
    $month = date('m');
    
    $lastOrder = Order::whereYear('created_date', $year)
        ->whereMonth('created_date', $month)
        ->orderBy('order_id', 'desc')
        ->first();
    
    $sequence = $lastOrder ? 
        intval(substr($lastOrder->order_number, -4)) + 1 : 1;
    
    return $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
}
```

### Order Total Calculation
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

### Order List View
- **Data Table**: Sortable, filterable order listing
- **Status Filters**: Quick filter by order status
- **Search**: Search by order number, customer name, or notes
- **Actions**: View, Edit, Delete, Status Update buttons
- **Pagination**: Efficient handling of large order volumes

### Order Form
- **Customer Selection**: Autocomplete customer search
- **Product Selection**: Search and add products with pricing
- **Item Management**: Add/remove/edit order items
- **Pricing Summary**: Real-time total calculation
- **Status Management**: Status dropdown with workflow validation

### Order Detail View
- **Order Header**: Customer info, dates, status, totals
- **Order Items**: Detailed item listing with pricing
- **Status History**: Timeline of status changes
- **Actions**: Edit, Status Update, Convert to Sale, Generate PDF

## Integration Points

### Sales System
- **Order to Sale**: Convert confirmed orders to sales
- **Status Sync**: Update order status when sale is processed
- **Payment Tracking**: Link payments to orders

### Customer Management
- **Customer Data**: Pull customer information for orders
- **Order History**: Track customer order history
- **Communication**: Send order updates to customers

### Inventory System
- **Stock Check**: Verify product availability
- **Reservation**: Reserve stock for confirmed orders
- **Updates**: Update inventory when orders are processed

### Payment System
- **Payment Tracking**: Link payments to orders
- **Status Updates**: Update payment status on orders
- **Outstanding**: Track outstanding payments

## Security Considerations

### Access Control
- **Role-based Access**: Different permissions for different user levels
- **Order Ownership**: Users can only modify orders they created (unless admin)
- **Status Restrictions**: Only authorized users can change certain statuses

### Data Validation
- **Input Validation**: Validate all order data before saving
- **Business Rules**: Enforce business rules (e.g., positive quantities)
- **Status Transitions**: Validate status change workflows

### Audit Trail
- **Change Tracking**: Log all order modifications
- **User Attribution**: Track who made changes and when
- **Status History**: Complete history of status changes

## Performance Considerations

### Database Optimization
- **Indexing**: Proper indexes on frequently queried fields
- **Pagination**: Efficient pagination for large order lists
- **Caching**: Cache frequently accessed order data

### Query Optimization
- **Eager Loading**: Load related data efficiently
- **Selective Loading**: Only load necessary data for list views
- **Search Optimization**: Optimized search queries

## Testing Requirements

### Unit Tests
- Order creation and validation
- Order number generation
- Total calculations
- Status transitions

### Integration Tests
- Order to sales conversion
- Payment integration
- Customer data integration
- Inventory updates

### User Acceptance Tests
- Order creation workflow
- Order management operations
- Status update workflows
- Search and filtering

## Migration Notes

### From CodeIgniter
- **Model Structure**: Convert CI models to Laravel Eloquent models
- **Controller Logic**: Adapt CI controllers to Laravel controllers
- **Database Queries**: Convert CI query builder to Eloquent
- **Validation**: Replace CI validation with Laravel validation

### Data Migration
- **Order Data**: Migrate existing orders with UUID preservation
- **Order Items**: Migrate order items with relationships
- **Status History**: Preserve complete status history
- **Audit Trail**: Maintain audit trail continuity
