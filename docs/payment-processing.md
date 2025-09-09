# Payment Processing System

## Overview
The Payment Processing system handles comprehensive payment tracking, multiple payment methods, and integration with orders and sales. It provides complete payment lifecycle management with status tracking and reporting capabilities.

## Core Features

### Payment Management
- **Payment Creation**: Create payments for orders, sales, or general transactions
- **Payment Editing**: Modify existing payment information
- **Payment Deletion**: Soft delete payments with audit trail
- **Payment Search**: Advanced search and filtering capabilities
- **Payment Import**: Bulk import payments via CSV
- **Payment Export**: Export payment data for accounting

### Payment Information
- **Payment Details**: Payment date, amount, payment type, reference number
- **Payment Methods**: Cash, bank transfer, digital wallets, credit cards
- **Payment Status**: Pending, paid, partial, overdue, cancelled
- **Payment Types**: Disbursement, receipt, refund, adjustment
- **Reference Tracking**: External reference numbers and notes

### Payment Types Management
- **Payment Type Creation**: Define custom payment types
- **Payment Type Editing**: Modify existing payment types
- **Payment Type Deletion**: Remove unused payment types
- **Default Types**: Pre-configured payment types (Cash, Bank Transfer, etc.)

### Payment Tracking
- **Order Payments**: Track payments against specific orders
- **Sales Payments**: Track payments against specific sales
- **Customer Payments**: Track all payments by customer
- **Payment History**: Complete payment history and audit trail
- **Outstanding Tracking**: Track outstanding amounts and overdue payments

## Database Schema

### Payments Table
```sql
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    payment_date DATE NOT NULL,
    payment_number VARCHAR(20) UNIQUE NOT NULL,
    payment_type ENUM('disbursement','receipt','refund','adjustment','other') DEFAULT 'receipt',
    payment_method VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_type_id INT NOT NULL,
    order_id INT NULL,
    sale_id INT NULL,
    customer_id INT NULL,
    reference_number VARCHAR(100) NULL,
    notes TEXT NULL,
    due_date DATE NULL,
    payment_status ENUM('pending','paid','partial','overdue','cancelled') DEFAULT 'pending',
    created_by INT NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_by INT NULL,
    updated_date DATETIME NULL,
    deleted_by INT NULL,
    deleted_date DATETIME NULL,
    status TINYINT DEFAULT 1,
    FOREIGN KEY (payment_type_id) REFERENCES payment_types(id),
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (sale_id) REFERENCES sales(sale_id),
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    FOREIGN KEY (deleted_by) REFERENCES users(id)
);
```

### Payment Types Table
```sql
CREATE TABLE payment_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    name VARCHAR(50) NOT NULL,
    description TEXT NULL,
    is_default TINYINT DEFAULT 0,
    display_order INT DEFAULT 0,
    created_by INT NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_by INT NULL,
    updated_date DATETIME NULL,
    deleted_by INT NULL,
    deleted_date DATETIME NULL,
    status TINYINT DEFAULT 1,
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    FOREIGN KEY (deleted_by) REFERENCES users(id)
);
```

### Payment History Table
```sql
CREATE TABLE payment_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    payment_id INT NOT NULL,
    action ENUM('created','updated','status_changed','deleted') NOT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    notes TEXT NULL,
    created_by INT NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payment_id) REFERENCES payments(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

## API Endpoints

### Payment Management
- `GET /api/payments` - List all payments with filtering
- `POST /api/payments` - Create new payment
- `GET /api/payments/{uuid}` - Get payment details
- `PUT /api/payments/{uuid}` - Update payment
- `DELETE /api/payments/{uuid}` - Soft delete payment
- `PATCH /api/payments/{uuid}/status` - Update payment status

### Payment Types
- `GET /api/payment-types` - List all payment types
- `POST /api/payment-types` - Create new payment type
- `GET /api/payment-types/{id}` - Get payment type details
- `PUT /api/payment-types/{id}` - Update payment type
- `DELETE /api/payment-types/{id}` - Delete payment type

### Payment Tracking
- `GET /api/payments/by-order/{order_id}` - Get payments for order
- `GET /api/payments/by-sale/{sale_id}` - Get payments for sale
- `GET /api/payments/by-customer/{customer_id}` - Get payments for customer
- `GET /api/payments/outstanding` - Get outstanding payments
- `GET /api/payments/overdue` - Get overdue payments

### Payment Analytics
- `GET /api/payments/summary` - Get payment summary statistics
- `GET /api/payments/by-date-range` - Get payments by date range
- `GET /api/payments/by-method` - Get payments grouped by method
- `GET /api/payments/revenue-report` - Get revenue report

## Business Logic

### Payment Creation
```php
public function createPayment(array $data): Payment
{
    $payment = Payment::create([
        'uuid' => Str::uuid(),
        'payment_number' => $this->generatePaymentNumber(),
        'payment_date' => $data['payment_date'],
        'payment_type' => $data['payment_type'] ?? 'receipt',
        'payment_method' => $data['payment_method'],
        'amount' => $data['amount'],
        'payment_type_id' => $data['payment_type_id'],
        'order_id' => $data['order_id'] ?? null,
        'sale_id' => $data['sale_id'] ?? null,
        'customer_id' => $data['customer_id'] ?? null,
        'reference_number' => $data['reference_number'],
        'notes' => $data['notes'],
        'due_date' => $data['due_date'] ?? null,
        'payment_status' => $data['payment_status'] ?? 'pending',
        'created_by' => auth()->id(),
        'status' => 1
    ]);
    
    // Log payment creation
    $this->logPaymentHistory($payment, 'created', null, $payment->toArray());
    
    // Update related order/sale payment status
    $this->updateRelatedPaymentStatus($payment);
    
    return $payment;
}
```

### Payment Number Generation
```php
private function generatePaymentNumber(): string
{
    $prefix = 'PAY';
    $year = date('Y');
    $month = date('m');
    
    $lastPayment = Payment::whereYear('created_date', $year)
        ->whereMonth('created_date', $month)
        ->orderBy('id', 'desc')
        ->first();
    
    $sequence = $lastPayment ? 
        intval(substr($lastPayment->payment_number, -4)) + 1 : 1;
    
    return $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
}
```

### Payment Status Update
```php
public function updatePaymentStatus(Payment $payment, string $newStatus, string $notes = null): Payment
{
    $oldStatus = $payment->payment_status;
    
    $payment->update([
        'payment_status' => $newStatus,
        'updated_by' => auth()->id(),
        'updated_date' => now()
    ]);
    
    // Log status change
    $this->logPaymentHistory($payment, 'status_changed', 
        ['payment_status' => $oldStatus], 
        ['payment_status' => $newStatus, 'notes' => $notes]
    );
    
    // Update related order/sale payment status
    $this->updateRelatedPaymentStatus($payment);
    
    return $payment;
}
```

### Outstanding Payment Calculation
```php
public function calculateOutstandingPayments($orderId = null, $saleId = null, $customerId = null): array
{
    $query = Payment::where('status', 1);
    
    if ($orderId) {
        $query->where('order_id', $orderId);
    }
    if ($saleId) {
        $query->where('sale_id', $saleId);
    }
    if ($customerId) {
        $query->where('customer_id', $customerId);
    }
    
    $payments = $query->get();
    
    $totalAmount = $payments->sum('amount');
    $paidAmount = $payments->where('payment_status', 'paid')->sum('amount');
    $pendingAmount = $payments->where('payment_status', 'pending')->sum('amount');
    $overdueAmount = $payments->where('payment_status', 'overdue')->sum('amount');
    
    return [
        'total_amount' => $totalAmount,
        'paid_amount' => $paidAmount,
        'pending_amount' => $pendingAmount,
        'overdue_amount' => $overdueAmount,
        'outstanding_amount' => $totalAmount - $paidAmount
    ];
}
```

### Payment History Logging
```php
private function logPaymentHistory(Payment $payment, string $action, array $oldValues = null, array $newValues = null): void
{
    PaymentHistory::create([
        'uuid' => Str::uuid(),
        'payment_id' => $payment->id,
        'action' => $action,
        'old_values' => $oldValues ? json_encode($oldValues) : null,
        'new_values' => $newValues ? json_encode($newValues) : null,
        'created_by' => auth()->id()
    ]);
}
```

## User Interface Components

### Payment List View
- **Data Table**: Sortable, filterable payment listing
- **Status Filters**: Quick filter by payment status
- **Method Filters**: Quick filter by payment method
- **Search**: Search by payment number, reference, customer name
- **Actions**: View, Edit, Delete, Update Status buttons
- **Pagination**: Efficient handling of large payment volumes

### Payment Form
- **Payment Details**: Date, amount, payment method, reference number
- **Payment Type**: Select from available payment types
- **Related Records**: Link to orders, sales, or customers
- **Notes**: Additional notes and comments
- **Due Date**: Set due date for pending payments
- **Validation**: Real-time validation feedback

### Payment Detail View
- **Payment Information**: Complete payment details
- **Related Records**: Linked orders, sales, or customers
- **Payment History**: Timeline of payment changes
- **Status Management**: Update payment status
- **Actions**: Edit, Delete, Generate Receipt

### Payment Dashboard
- **Quick Stats**: Total payments, outstanding, overdue
- **Recent Payments**: Latest payment transactions
- **Payment Methods**: Payment distribution by method
- **Outstanding Alerts**: Overdue payment alerts

## Integration Points

### Orders System
- **Payment Tracking**: Track payments against orders
- **Status Updates**: Update order payment status
- **Outstanding Calculation**: Calculate order outstanding amounts
- **Payment Allocation**: Allocate payments to specific orders

### Sales System
- **Payment Tracking**: Track payments against sales
- **Status Updates**: Update sale payment status
- **Outstanding Calculation**: Calculate sale outstanding amounts
- **Payment Allocation**: Allocate payments to specific sales

### Customer Management
- **Payment History**: Track all customer payments
- **Outstanding Tracking**: Track customer outstanding amounts
- **Payment Preferences**: Track customer payment preferences
- **Communication**: Notify customers of payment status

### Accounting System
- **Payment Export**: Export payments for accounting
- **Revenue Tracking**: Track revenue by payment method
- **Financial Reports**: Generate financial reports
- **Tax Reporting**: Support tax reporting requirements

## Business Rules

### Payment Creation Rules
- Payment amount must be positive
- Payment date cannot be in the future (unless specified)
- Payment method must be selected
- Reference number must be unique if provided
- Related records (order/sale) must exist and be valid

### Payment Update Rules
- Payment amount cannot be changed if payment is confirmed
- Payment status transitions must follow business rules
- Reference number uniqueness must be maintained
- Update timestamps must be maintained

### Payment Deletion Rules
- Confirmed payments cannot be deleted
- Soft delete only (status = 0)
- Maintain referential integrity
- Preserve audit trail and history

### Status Transition Rules
- Pending → Paid: Payment confirmed
- Pending → Partial: Partial payment received
- Pending → Overdue: Payment past due date
- Any Status → Cancelled: With valid cancellation reason

## Security Considerations

### Access Control
- **Role-based Access**: Different permissions for different user levels
- **Payment Ownership**: Users can only modify payments they created (unless admin)
- **Financial Data**: Protect sensitive financial information

### Data Validation
- **Input Validation**: Validate all payment data before saving
- **Amount Validation**: Ensure payment amounts are valid
- **Date Validation**: Validate payment dates and due dates
- **Business Rules**: Enforce business rules (e.g., positive amounts)

### Audit Trail
- **Change Tracking**: Log all payment modifications
- **User Attribution**: Track who made changes and when
- **Financial Audit**: Complete financial audit trail
- **Access Logging**: Log payment data access

## Performance Considerations

### Database Optimization
- **Indexing**: Proper indexes on frequently queried fields
- **Pagination**: Efficient pagination for large payment lists
- **Caching**: Cache frequently accessed payment data

### Query Optimization
- **Eager Loading**: Load related data efficiently
- **Selective Loading**: Only load necessary data for list views
- **Search Optimization**: Optimized search queries with full-text indexes

### Financial Calculations
- **Aggregation**: Efficient aggregation queries for reports
- **Caching**: Cache calculated financial data
- **Batch Processing**: Efficient batch processing for large datasets

## Testing Requirements

### Unit Tests
- Payment creation and validation
- Payment number generation
- Status transitions
- Outstanding calculations
- Payment history logging

### Integration Tests
- Order system integration
- Sales system integration
- Customer system integration
- Accounting system integration

### User Acceptance Tests
- Payment creation workflow
- Payment management operations
- Status update workflows
- Search and filtering
- Financial reporting

## Migration Notes

### From CodeIgniter
- **Model Structure**: Convert CI models to Laravel Eloquent models
- **Controller Logic**: Adapt CI controllers to Laravel controllers
- **Database Queries**: Convert CI query builder to Eloquent
- **Validation**: Replace CI validation with Laravel validation

### Data Migration
- **Payment Data**: Migrate existing payments with UUID preservation
- **Payment Types**: Migrate payment types with relationships
- **Payment History**: Migrate payment history if available
- **Relationships**: Maintain payment-order-sales relationships
- **Audit Trail**: Maintain audit trail continuity
