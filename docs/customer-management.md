# Customer Management System

## Overview
The Customer Management system handles comprehensive customer information, relationship tracking, and integration with orders, sales, and payment systems. It provides a centralized repository for all customer data and interactions.

## Core Features

### Customer Management
- **Customer Creation**: Add new customers with comprehensive details
- **Customer Editing**: Modify existing customer information
- **Customer Deletion**: Soft delete customers with audit trail
- **Customer Search**: Advanced search and filtering capabilities
- **Customer Import**: Bulk import customers via CSV
- **Customer Export**: Export customer data for external use

### Customer Information
- **Company Details**: Company name, legal name, BRN, VAT number
- **Contact Information**: Full name, address, city, phone numbers, email
- **Additional Details**: Remarks, notes, special instructions
- **Status Management**: Active/inactive customer status
- **Customer Type**: Business/individual customer classification

### Customer Relationships
- **Order History**: Track all customer orders
- **Sales History**: Track all customer sales
- **Payment History**: Track all customer payments
- **Communication Log**: Track all customer communications
- **Customer Notes**: Internal notes and observations

## Database Schema

### Customers Table
```sql
CREATE TABLE customers (
    customer_id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    company_name VARCHAR(100) NOT NULL,
    legal_name VARCHAR(100) NULL,
    brn VARCHAR(20) NULL,
    vat VARCHAR(20) NULL,
    full_name VARCHAR(100) NULL,
    address TEXT NULL,
    city VARCHAR(100) NULL,
    phone_number1 VARCHAR(50) NOT NULL,
    phone_number2 VARCHAR(50) NULL,
    email VARCHAR(100) NULL,
    customer_type ENUM('business','individual') DEFAULT 'business',
    remarks TEXT NULL,
    created_by INT NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_by INT NULL,
    updated_date DATETIME NULL,
    status TINYINT DEFAULT 1,
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);
```

### Customer Communications Table
```sql
CREATE TABLE customer_communications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    customer_id INT NOT NULL,
    communication_type ENUM('email','phone','meeting','note') NOT NULL,
    subject VARCHAR(255) NULL,
    content TEXT NOT NULL,
    communication_date DATETIME NOT NULL,
    created_by INT NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

### Customer Notes Table
```sql
CREATE TABLE customer_notes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    customer_id INT NOT NULL,
    note_type ENUM('general','payment','order','sales','other') DEFAULT 'general',
    title VARCHAR(255) NULL,
    content TEXT NOT NULL,
    is_important TINYINT DEFAULT 0,
    created_by INT NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

## API Endpoints

### Customer Management
- `GET /api/customers` - List all customers with filtering
- `POST /api/customers` - Create new customer
- `GET /api/customers/{uuid}` - Get customer details
- `PUT /api/customers/{uuid}` - Update customer
- `DELETE /api/customers/{uuid}` - Soft delete customer
- `POST /api/customers/bulk-import` - Import customers from CSV
- `GET /api/customers/export` - Export customers to CSV

### Customer Relationships
- `GET /api/customers/{uuid}/orders` - Get customer orders
- `GET /api/customers/{uuid}/sales` - Get customer sales
- `GET /api/customers/{uuid}/payments` - Get customer payments
- `GET /api/customers/{uuid}/communications` - Get customer communications
- `POST /api/customers/{uuid}/communications` - Add communication
- `GET /api/customers/{uuid}/notes` - Get customer notes
- `POST /api/customers/{uuid}/notes` - Add customer note

### Customer Analytics
- `GET /api/customers/{uuid}/summary` - Get customer summary
- `GET /api/customers/{uuid}/history` - Get customer history
- `GET /api/customers/top-customers` - Get top customers by revenue
- `GET /api/customers/recent-activity` - Get recent customer activity

## Business Logic

### Customer Creation
```php
public function createCustomer(array $data): Customer
{
    // Validate email uniqueness if provided
    if (isset($data['email']) && Customer::where('email', $data['email'])->exists()) {
        throw new ValidationException('Email already exists');
    }
    
    $customer = Customer::create([
        'uuid' => Str::uuid(),
        'company_name' => $data['company_name'],
        'legal_name' => $data['legal_name'],
        'brn' => $data['brn'],
        'vat' => $data['vat'],
        'full_name' => $data['full_name'],
        'address' => $data['address'],
        'city' => $data['city'],
        'phone_number1' => $data['phone_number1'],
        'phone_number2' => $data['phone_number2'],
        'email' => $data['email'],
        'customer_type' => $data['customer_type'] ?? 'business',
        'remarks' => $data['remarks'],
        'created_by' => auth()->id(),
        'status' => 1
    ]);
    
    return $customer;
}
```

### Customer Search
```php
public function searchCustomers(string $query, array $filters = []): Collection
{
    $customers = Customer::where('status', 1);
    
    // Text search
    if ($query) {
        $customers->where(function ($q) use ($query) {
            $q->where('company_name', 'like', "%{$query}%")
              ->orWhere('full_name', 'like', "%{$query}%")
              ->orWhere('phone_number1', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%")
              ->orWhere('brn', 'like', "%{$query}%");
        });
    }
    
    // Customer type filter
    if (isset($filters['customer_type'])) {
        $customers->where('customer_type', $filters['customer_type']);
    }
    
    // City filter
    if (isset($filters['city'])) {
        $customers->where('city', 'like', "%{$filters['city']}%");
    }
    
    // Status filter
    if (isset($filters['status'])) {
        $customers->where('status', $filters['status']);
    }
    
    return $customers->orderBy('company_name')->get();
}
```

### Customer Summary
```php
public function getCustomerSummary(Customer $customer): array
{
    $orders = $customer->orders()->where('status', 1)->get();
    $sales = $customer->sales()->where('status', 1)->get();
    $payments = $customer->payments()->where('status', 1)->get();
    
    return [
        'total_orders' => $orders->count(),
        'total_sales' => $sales->count(),
        'total_payments' => $payments->count(),
        'total_revenue' => $sales->sum('total_amount'),
        'total_paid' => $payments->sum('amount'),
        'outstanding_amount' => $sales->sum('total_amount') - $payments->sum('amount'),
        'last_order_date' => $orders->max('created_date'),
        'last_sale_date' => $sales->max('created_date'),
        'last_payment_date' => $payments->max('created_date'),
        'average_order_value' => $orders->avg('total_amount'),
        'average_sale_value' => $sales->avg('total_amount')
    ];
}
```

### Customer Communication Logging
```php
public function logCommunication(Customer $customer, array $data): CustomerCommunication
{
    return CustomerCommunication::create([
        'uuid' => Str::uuid(),
        'customer_id' => $customer->customer_id,
        'communication_type' => $data['type'],
        'subject' => $data['subject'],
        'content' => $data['content'],
        'communication_date' => $data['date'] ?? now(),
        'created_by' => auth()->id(),
        'status' => 1
    ]);
}
```

## User Interface Components

### Customer List View
- **Data Table**: Sortable, filterable customer listing
- **Type Filters**: Quick filter by customer type
- **Search**: Search by company name, contact name, phone, email
- **Actions**: View, Edit, Delete, Add Note buttons
- **Pagination**: Efficient handling of large customer databases

### Customer Form
- **Company Information**: Company name, legal name, BRN, VAT
- **Contact Details**: Full name, address, city, phone numbers, email
- **Customer Type**: Business or individual selection
- **Additional Information**: Remarks and special notes
- **Validation**: Real-time validation feedback

### Customer Detail View
- **Customer Information**: Complete customer details
- **Order History**: List of all customer orders
- **Sales History**: List of all customer sales
- **Payment History**: List of all customer payments
- **Communication Log**: Timeline of customer communications
- **Notes**: Internal notes and observations
- **Summary Statistics**: Key metrics and totals

### Customer Dashboard
- **Quick Stats**: Total orders, sales, payments, outstanding
- **Recent Activity**: Latest orders, sales, payments
- **Communication Timeline**: Recent communications and notes
- **Quick Actions**: Create order, record payment, add note

## Integration Points

### Orders System
- **Customer Selection**: Select customers for orders
- **Order History**: Track all customer orders
- **Customer Data**: Use customer information in orders
- **Status Updates**: Update customer status based on order activity

### Sales System
- **Customer Selection**: Select customers for sales
- **Sales History**: Track all customer sales
- **Customer Data**: Use customer information in sales
- **Revenue Tracking**: Track customer revenue

### Payment System
- **Payment Tracking**: Link payments to customers
- **Outstanding Tracking**: Track customer outstanding amounts
- **Payment History**: Complete payment history per customer
- **Payment Methods**: Track preferred payment methods

### Communication System
- **Email Integration**: Send emails to customers
- **Communication Logging**: Log all customer communications
- **Notification System**: Notify customers of order/sales updates
- **Document Generation**: Generate customer-specific documents

## Business Rules

### Customer Creation Rules
- Company name is required
- At least one phone number is required
- Email must be unique if provided
- BRN and VAT must be unique if provided
- Customer type must be specified

### Customer Update Rules
- Email uniqueness must be maintained
- BRN/VAT uniqueness must be maintained
- Status changes should validate business rules
- Update timestamps should be maintained

### Customer Deletion Rules
- Customers with existing orders/sales cannot be deleted
- Soft delete only (status = 0)
- Maintain referential integrity
- Preserve audit trail and history

## Security Considerations

### Access Control
- **Role-based Access**: Different permissions for different user levels
- **Customer Ownership**: Users can only modify customers they created (unless admin)
- **Data Privacy**: Protect sensitive customer information

### Data Validation
- **Input Validation**: Validate all customer data before saving
- **Email Validation**: Validate email format and uniqueness
- **Phone Validation**: Validate phone number formats
- **Business Rules**: Enforce business rules (e.g., required fields)

### Audit Trail
- **Change Tracking**: Log all customer modifications
- **User Attribution**: Track who made changes and when
- **Communication Logging**: Log all customer communications
- **Access Logging**: Log customer data access

## Performance Considerations

### Database Optimization
- **Indexing**: Proper indexes on frequently queried fields
- **Pagination**: Efficient pagination for large customer lists
- **Caching**: Cache frequently accessed customer data

### Query Optimization
- **Eager Loading**: Load related data efficiently
- **Selective Loading**: Only load necessary data for list views
- **Search Optimization**: Optimized search queries with full-text indexes

### Data Management
- **Bulk Operations**: Efficient bulk import/export
- **Data Archiving**: Archive old customer data
- **Cleanup**: Regular cleanup of soft-deleted records

## Testing Requirements

### Unit Tests
- Customer creation and validation
- Email and phone uniqueness
- Customer search functionality
- Summary calculations
- Communication logging

### Integration Tests
- Order system integration
- Sales system integration
- Payment system integration
- Communication system integration

### User Acceptance Tests
- Customer creation workflow
- Customer management operations
- Search and filtering
- Customer detail views
- Communication logging

## Migration Notes

### From CodeIgniter
- **Model Structure**: Convert CI models to Laravel Eloquent models
- **Controller Logic**: Adapt CI controllers to Laravel controllers
- **Database Queries**: Convert CI query builder to Eloquent
- **Validation**: Replace CI validation with Laravel validation

### Data Migration
- **Customer Data**: Migrate existing customers with UUID preservation
- **Communications**: Migrate customer communications if available
- **Notes**: Migrate customer notes if available
- **Relationships**: Maintain customer-order-sales relationships
- **Audit Trail**: Maintain audit trail continuity
