# Inventory Management System

## Overview
The Inventory Management system handles comprehensive stock tracking, inventory control, and warehouse management across multiple departments/locations. It provides real-time inventory visibility and automated stock management.

## Core Features

### Inventory Tracking
- **Stock Levels**: Track current stock levels per product per department
- **Stock Movements**: Track all stock movements (in/out/transfer)
- **Stock Reservations**: Reserve stock for orders and sales
- **Stock Alerts**: Low stock and reorder point alerts
- **Stock Valuation**: Calculate inventory value and costs

### Department Management
- **Department Creation**: Create and manage departments/locations
- **Department Assignment**: Assign products to departments
- **Department Transfer**: Transfer stock between departments
- **Department Reporting**: Generate department-specific reports

### Stock Operations
- **Stock Receipt**: Record incoming stock (purchases, returns)
- **Stock Issue**: Record outgoing stock (sales, transfers)
- **Stock Transfer**: Transfer stock between departments
- **Stock Adjustment**: Adjust stock levels (corrections, damages)
- **Stock Count**: Physical stock counting and reconciliation

### Inventory Reporting
- **Stock Reports**: Current stock levels and valuations
- **Movement Reports**: Stock movement history and trends
- **Department Reports**: Department-specific inventory reports
- **Alert Reports**: Low stock and reorder point reports
- **Valuation Reports**: Inventory valuation and costing reports

## Database Schema

### Departments Table
```sql
CREATE TABLE departments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    address TEXT NULL,
    phone_number VARCHAR(50) NULL,
    email VARCHAR(100) NULL,
    manager_id INT NULL,
    is_main TINYINT DEFAULT 0,
    created_by INT NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_by INT NULL,
    updated_date DATETIME NULL,
    status TINYINT DEFAULT 1,
    FOREIGN KEY (manager_id) REFERENCES users(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);
```

### Inventory Table
```sql
CREATE TABLE inventory (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    product_id INT NOT NULL,
    department_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    reserved INT NOT NULL DEFAULT 0,
    onorder INT NOT NULL DEFAULT 0,
    reorder_point INT DEFAULT 0,
    reorder_quantity INT DEFAULT 0,
    bin_location VARCHAR(50) NULL,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_by INT NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

### Stock Movements Table
```sql
CREATE TABLE stock_movements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    product_id INT NOT NULL,
    department_id INT NOT NULL,
    movement_type ENUM('in','out','transfer','adjustment','count') NOT NULL,
    quantity INT NOT NULL,
    reference_type ENUM('order','sale','purchase','transfer','adjustment','count') NULL,
    reference_id INT NULL,
    reference_number VARCHAR(50) NULL,
    notes TEXT NULL,
    movement_date DATETIME NOT NULL,
    created_by INT NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

### Stock Transfers Table
```sql
CREATE TABLE stock_transfers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    transfer_number VARCHAR(20) UNIQUE NOT NULL,
    from_department_id INT NOT NULL,
    to_department_id INT NOT NULL,
    transfer_date DATE NOT NULL,
    status ENUM('pending','approved','shipped','received','cancelled') DEFAULT 'pending',
    notes TEXT NULL,
    created_by INT NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    approved_by INT NULL,
    approved_date DATETIME NULL,
    shipped_by INT NULL,
    shipped_date DATETIME NULL,
    received_by INT NULL,
    received_date DATETIME NULL,
    FOREIGN KEY (from_department_id) REFERENCES departments(id),
    FOREIGN KEY (to_department_id) REFERENCES departments(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    FOREIGN KEY (shipped_by) REFERENCES users(id),
    FOREIGN KEY (received_by) REFERENCES users(id)
);
```

### Stock Transfer Items Table
```sql
CREATE TABLE stock_transfer_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    transfer_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    notes TEXT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transfer_id) REFERENCES stock_transfers(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

## API Endpoints

### Inventory Management
- `GET /api/inventory` - List all inventory with filtering
- `GET /api/inventory/{product_id}` - Get inventory for specific product
- `GET /api/inventory/department/{department_id}` - Get inventory for department
- `POST /api/inventory/adjustment` - Create stock adjustment
- `GET /api/inventory/movements` - Get stock movements
- `GET /api/inventory/alerts` - Get low stock alerts

### Department Management
- `GET /api/departments` - List all departments
- `POST /api/departments` - Create new department
- `GET /api/departments/{id}` - Get department details
- `PUT /api/departments/{id}` - Update department
- `DELETE /api/departments/{id}` - Delete department

### Stock Transfers
- `GET /api/stock-transfers` - List all stock transfers
- `POST /api/stock-transfers` - Create new stock transfer
- `GET /api/stock-transfers/{uuid}` - Get transfer details
- `PUT /api/stock-transfers/{uuid}` - Update transfer
- `PATCH /api/stock-transfers/{uuid}/status` - Update transfer status

### Inventory Reports
- `GET /api/inventory/reports/stock-levels` - Get stock levels report
- `GET /api/inventory/reports/movements` - Get movements report
- `GET /api/inventory/reports/valuation` - Get valuation report
- `GET /api/inventory/reports/alerts` - Get alerts report

## Business Logic

### Stock Movement Recording
```php
public function recordStockMovement(array $data): StockMovement
{
    $movement = StockMovement::create([
        'uuid' => Str::uuid(),
        'product_id' => $data['product_id'],
        'department_id' => $data['department_id'],
        'movement_type' => $data['movement_type'],
        'quantity' => $data['quantity'],
        'reference_type' => $data['reference_type'] ?? null,
        'reference_id' => $data['reference_id'] ?? null,
        'reference_number' => $data['reference_number'] ?? null,
        'notes' => $data['notes'],
        'movement_date' => $data['movement_date'] ?? now(),
        'created_by' => auth()->id()
    ]);
    
    // Update inventory levels
    $this->updateInventoryLevels($data['product_id'], $data['department_id'], $data['movement_type'], $data['quantity']);
    
    return $movement;
}
```

### Inventory Level Update
```php
private function updateInventoryLevels(int $productId, int $departmentId, string $movementType, int $quantity): void
{
    $inventory = Inventory::where('product_id', $productId)
        ->where('department_id', $departmentId)
        ->first();
    
    if (!$inventory) {
        throw new Exception('Inventory record not found');
    }
    
    $newQuantity = match($movementType) {
        'in' => $inventory->quantity + $quantity,
        'out' => $inventory->quantity - $quantity,
        'transfer' => $inventory->quantity - $quantity,
        'adjustment' => $quantity, // Direct set for adjustments
        default => $inventory->quantity
    };
    
    $inventory->update([
        'quantity' => $newQuantity,
        'last_updated' => now()
    ]);
    
    // Check for low stock alerts
    $this->checkLowStockAlert($inventory);
}
```

### Stock Transfer Creation
```php
public function createStockTransfer(array $data): StockTransfer
{
    $transfer = StockTransfer::create([
        'uuid' => Str::uuid(),
        'transfer_number' => $this->generateTransferNumber(),
        'from_department_id' => $data['from_department_id'],
        'to_department_id' => $data['to_department_id'],
        'transfer_date' => $data['transfer_date'] ?? now()->toDateString(),
        'status' => 'pending',
        'notes' => $data['notes'],
        'created_by' => auth()->id()
    ]);
    
    // Add transfer items
    foreach ($data['items'] as $item) {
        $transfer->items()->create([
            'uuid' => Str::uuid(),
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
            'notes' => $item['notes'] ?? null
        ]);
    }
    
    return $transfer;
}
```

### Transfer Number Generation
```php
private function generateTransferNumber(): string
{
    $prefix = 'TRF';
    $year = date('Y');
    $month = date('m');
    
    $lastTransfer = StockTransfer::whereYear('created_date', $year)
        ->whereMonth('created_date', $month)
        ->orderBy('id', 'desc')
        ->first();
    
    $sequence = $lastTransfer ? 
        intval(substr($lastTransfer->transfer_number, -4)) + 1 : 1;
    
    return $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
}
```

### Low Stock Alert Check
```php
private function checkLowStockAlert(Inventory $inventory): void
{
    if ($inventory->quantity <= $inventory->reorder_point) {
        // Create or update low stock alert
        LowStockAlert::updateOrCreate(
            [
                'product_id' => $inventory->product_id,
                'department_id' => $inventory->department_id
            ],
            [
                'current_quantity' => $inventory->quantity,
                'reorder_point' => $inventory->reorder_point,
                'reorder_quantity' => $inventory->reorder_quantity,
                'alert_date' => now(),
                'status' => 'active'
            ]
        );
    } else {
        // Remove alert if stock is above reorder point
        LowStockAlert::where('product_id', $inventory->product_id)
            ->where('department_id', $inventory->department_id)
            ->delete();
    }
}
```

### Inventory Valuation
```php
public function calculateInventoryValuation(int $departmentId = null): array
{
    $query = Inventory::with(['product', 'department'])
        ->where('status', 1);
    
    if ($departmentId) {
        $query->where('department_id', $departmentId);
    }
    
    $inventory = $query->get();
    
    $totalValue = 0;
    $totalQuantity = 0;
    $productCount = 0;
    
    foreach ($inventory as $item) {
        $itemValue = $item->quantity * $item->product->cost_price;
        $totalValue += $itemValue;
        $totalQuantity += $item->quantity;
        $productCount++;
    }
    
    return [
        'total_value' => $totalValue,
        'total_quantity' => $totalQuantity,
        'product_count' => $productCount,
        'average_value_per_item' => $productCount > 0 ? $totalValue / $productCount : 0,
        'average_quantity_per_item' => $productCount > 0 ? $totalQuantity / $productCount : 0
    ];
}
```

## User Interface Components

### Inventory Dashboard
- **Stock Overview**: Total stock levels and values
- **Low Stock Alerts**: Products below reorder point
- **Recent Movements**: Latest stock movements
- **Department Summary**: Stock levels by department
- **Quick Actions**: Create transfer, adjustment, count

### Inventory List View
- **Data Table**: Sortable, filterable inventory listing
- **Department Filters**: Quick filter by department
- **Product Filters**: Quick filter by product category
- **Search**: Search by product name, SKU, or location
- **Actions**: View, Adjust, Transfer, Count buttons
- **Pagination**: Efficient handling of large inventories

### Stock Transfer Interface
- **Transfer Form**: Create new stock transfers
- **Item Selection**: Select products and quantities
- **Department Selection**: Choose source and destination
- **Approval Workflow**: Multi-step approval process
- **Status Tracking**: Track transfer status and progress

### Inventory Adjustment
- **Adjustment Form**: Record stock adjustments
- **Reason Codes**: Categorize adjustment reasons
- **Approval Process**: Require approval for large adjustments
- **Audit Trail**: Complete audit trail of adjustments

### Stock Count Interface
- **Count Sheets**: Generate count sheets for physical counts
- **Count Entry**: Enter counted quantities
- **Variance Analysis**: Compare counted vs. system quantities
- **Adjustment Creation**: Create adjustments for variances

## Integration Points

### Orders System
- **Stock Reservation**: Reserve stock for confirmed orders
- **Stock Allocation**: Allocate stock when orders are processed
- **Stock Release**: Release reserved stock for cancelled orders
- **Delivery Tracking**: Track stock movements for deliveries

### Sales System
- **Stock Check**: Verify stock availability for sales
- **Stock Deduction**: Deduct stock when sales are processed
- **Stock Allocation**: Allocate stock to specific sales
- **Delivery Tracking**: Track stock movements for deliveries

### Product Management
- **Product Creation**: Create inventory records for new products
- **Product Updates**: Update inventory when products change
- **Product Deletion**: Handle inventory when products are deleted
- **Product Information**: Display inventory info in product details

### Purchase System
- **Stock Receipt**: Record incoming stock from purchases
- **Purchase Orders**: Link stock receipts to purchase orders
- **Supplier Tracking**: Track stock from specific suppliers
- **Quality Control**: Handle quality issues with received stock

## Business Rules

### Stock Movement Rules
- Stock movements must have valid product and department
- Outgoing movements cannot exceed available stock
- All movements must be authorized by valid user
- Movement quantities must be positive
- Reference numbers must be unique if provided

### Transfer Rules
- Transfers cannot be from department to itself
- Transfer quantities cannot exceed available stock
- Transfers require approval before execution
- Transfers must be received to complete
- Transfer status must follow workflow

### Adjustment Rules
- Adjustments require valid reason codes
- Large adjustments require approval
- Adjustments must be documented with notes
- Adjustment reasons must be categorized
- All adjustments must be audited

### Alert Rules
- Low stock alerts trigger at reorder point
- Alerts must be acknowledged and resolved
- Reorder quantities must be specified
- Alert thresholds must be realistic
- Alerts must be prioritized by importance

## Security Considerations

### Access Control
- **Role-based Access**: Different permissions for different user levels
- **Department Access**: Users can only access assigned departments
- **Operation Authorization**: Require authorization for sensitive operations
- **Audit Requirements**: Log all inventory operations

### Data Validation
- **Input Validation**: Validate all inventory data before saving
- **Quantity Validation**: Ensure quantities are valid and realistic
- **Business Rules**: Enforce business rules (e.g., positive quantities)
- **Reference Validation**: Validate reference numbers and IDs

### Audit Trail
- **Change Tracking**: Log all inventory modifications
- **User Attribution**: Track who made changes and when
- **Movement History**: Complete history of stock movements
- **Access Logging**: Log inventory data access

## Performance Considerations

### Database Optimization
- **Indexing**: Proper indexes on frequently queried fields
- **Pagination**: Efficient pagination for large inventory lists
- **Caching**: Cache frequently accessed inventory data

### Query Optimization
- **Eager Loading**: Load related data efficiently
- **Selective Loading**: Only load necessary data for list views
- **Search Optimization**: Optimized search queries with full-text indexes

### Real-time Updates
- **WebSocket Integration**: Real-time inventory updates
- **Cache Invalidation**: Efficient cache invalidation
- **Background Processing**: Process large operations in background

## Testing Requirements

### Unit Tests
- Stock movement recording
- Inventory level updates
- Transfer creation and processing
- Alert generation
- Valuation calculations

### Integration Tests
- Order system integration
- Sales system integration
- Product system integration
- Purchase system integration

### User Acceptance Tests
- Inventory management workflow
- Stock transfer process
- Adjustment procedures
- Count and reconciliation
- Reporting functionality

## Migration Notes

### From CodeIgniter
- **Model Structure**: Convert CI models to Laravel Eloquent models
- **Controller Logic**: Adapt CI controllers to Laravel controllers
- **Database Queries**: Convert CI query builder to Eloquent
- **Validation**: Replace CI validation with Laravel validation

### Data Migration
- **Inventory Data**: Migrate existing inventory with UUID preservation
- **Departments**: Migrate departments with relationships
- **Stock Movements**: Migrate stock movement history
- **Transfers**: Migrate stock transfers if available
- **Audit Trail**: Maintain audit trail continuity
