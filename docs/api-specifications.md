# API Specifications

## Overview
This document defines the complete RESTful API specifications for the Z5 Distribution System. It includes all endpoints, request/response formats, authentication, and error handling.

## API Design Principles

### Core Principles
- **RESTful Design**: Follow REST conventions and HTTP methods
- **Consistent Response Format**: Standardized response structure
- **Comprehensive Error Handling**: Clear error messages and status codes
- **Authentication**: Secure API access with Laravel Sanctum
- **Rate Limiting**: Prevent abuse and ensure fair usage
- **Versioning**: API versioning for backward compatibility

### Base URL
```
Production: https://api.z5distribution.com/v1
Development: http://localhost:8000/api/v1
```

### Authentication
All API endpoints require authentication using Laravel Sanctum tokens.

```http
Authorization: Bearer {token}
```

## Response Format

### Success Response
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": {
    // Response data
  },
  "meta": {
    "timestamp": "2024-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field_name": ["Validation error message"]
  },
  "meta": {
    "timestamp": "2024-01-15T10:30:00Z",
    "version": "1.0"
  }
}
```

### Paginated Response
```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": [
    // Array of items
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7,
    "from": 1,
    "to": 15,
    "links": {
      "first": "http://api.example.com/v1/orders?page=1",
      "last": "http://api.example.com/v1/orders?page=7",
      "prev": null,
      "next": "http://api.example.com/v1/orders?page=2"
    }
  }
}
```

## Authentication Endpoints

### Login
```http
POST /api/v1/auth/login
```

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "uuid": "550e8400-e29b-41d4-a716-446655440000",
      "name": "John Doe",
      "email": "user@example.com",
      "user_level": "Admin",
      "photo": "avatar.jpg"
    },
    "token": "1|abcdef123456789",
    "permissions": {
      "orders": ["create", "read", "update", "delete"],
      "sales": ["create", "read", "update", "delete"]
    }
  }
}
```

### Logout
```http
POST /api/v1/auth/logout
```

**Response:**
```json
{
  "success": true,
  "message": "Logout successful"
}
```

### Refresh Token
```http
POST /api/v1/auth/refresh
```

**Response:**
```json
{
  "success": true,
  "message": "Token refreshed successfully",
  "data": {
    "token": "2|abcdef123456789"
  }
}
```

## User Management Endpoints

### Get Users
```http
GET /api/v1/users
```

**Query Parameters:**
- `page` (int): Page number
- `per_page` (int): Items per page (max 100)
- `search` (string): Search term
- `user_level` (string): Filter by user level
- `status` (int): Filter by status

**Response:**
```json
{
  "success": true,
  "message": "Users retrieved successfully",
  "data": [
    {
      "id": 1,
      "uuid": "550e8400-e29b-41d4-a716-446655440000",
      "name": "John Doe",
      "email": "john@example.com",
      "username": "johndoe",
      "user_level": "Admin",
      "job_title": "Manager",
      "photo": "avatar.jpg",
      "last_login": "2024-01-15T09:30:00Z",
      "status": 1,
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-15T09:30:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 1,
    "last_page": 1
  }
}
```

### Create User
```http
POST /api/v1/users
```

**Request Body:**
```json
{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "username": "janedoe",
  "password": "password123",
  "user_level": "Normal",
  "job_title": "Sales Representative",
  "landing_page": "dashboard"
}
```

**Response:**
```json
{
  "success": true,
  "message": "User created successfully",
  "data": {
    "id": 2,
    "uuid": "550e8400-e29b-41d4-a716-446655440001",
    "name": "Jane Doe",
    "email": "jane@example.com",
    "username": "janedoe",
    "user_level": "Normal",
    "job_title": "Sales Representative",
    "landing_page": "dashboard",
    "status": 1,
    "created_at": "2024-01-15T10:30:00Z"
  }
}
```

### Get User Details
```http
GET /api/v1/users/{uuid}
```

**Response:**
```json
{
  "success": true,
  "message": "User retrieved successfully",
  "data": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "name": "John Doe",
    "email": "john@example.com",
    "username": "johndoe",
    "user_level": "Admin",
    "job_title": "Manager",
    "photo": "avatar.jpg",
    "last_login": "2024-01-15T09:30:00Z",
    "status": 1,
    "permissions": {
      "orders": ["create", "read", "update", "delete"],
      "sales": ["create", "read", "update", "delete"]
    },
    "created_at": "2024-01-01T00:00:00Z",
    "updated_at": "2024-01-15T09:30:00Z"
  }
}
```

### Update User
```http
PUT /api/v1/users/{uuid}
```

**Request Body:**
```json
{
  "name": "John Smith",
  "email": "johnsmith@example.com",
  "job_title": "Senior Manager",
  "user_level": "Admin"
}
```

### Delete User
```http
DELETE /api/v1/users/{uuid}
```

**Response:**
```json
{
  "success": true,
  "message": "User deleted successfully"
}
```

## Customer Management Endpoints

### Get Customers
```http
GET /api/v1/customers
```

**Query Parameters:**
- `page` (int): Page number
- `per_page` (int): Items per page
- `search` (string): Search term
- `customer_type` (string): Filter by type
- `city` (string): Filter by city
- `status` (int): Filter by status

**Response:**
```json
{
  "success": true,
  "message": "Customers retrieved successfully",
  "data": [
    {
      "id": 1,
      "uuid": "550e8400-e29b-41d4-a716-446655440000",
      "company_name": "ABC Company Ltd",
      "legal_name": "ABC Company Limited",
      "brn": "C123456789",
      "vat": "VAT123456789",
      "full_name": "John Smith",
      "address": "123 Main Street",
      "city": "Port Louis",
      "phone_number1": "+23012345678",
      "phone_number2": "+23087654321",
      "email": "contact@abccompany.com",
      "customer_type": "business",
      "remarks": "Important client",
      "status": 1,
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-15T09:30:00Z"
    }
  ]
}
```

### Create Customer
```http
POST /api/v1/customers
```

**Request Body:**
```json
{
  "company_name": "XYZ Corporation",
  "legal_name": "XYZ Corporation Ltd",
  "brn": "C987654321",
  "vat": "VAT987654321",
  "full_name": "Jane Doe",
  "address": "456 Business Avenue",
  "city": "Curepipe",
  "phone_number1": "+23011223344",
  "phone_number2": "+23055667788",
  "email": "info@xyzcorp.com",
  "customer_type": "business",
  "remarks": "New customer"
}
```

### Get Customer Details
```http
GET /api/v1/customers/{uuid}
```

**Response:**
```json
{
  "success": true,
  "message": "Customer retrieved successfully",
  "data": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "company_name": "ABC Company Ltd",
    "legal_name": "ABC Company Limited",
    "brn": "C123456789",
    "vat": "VAT123456789",
    "full_name": "John Smith",
    "address": "123 Main Street",
    "city": "Port Louis",
    "phone_number1": "+23012345678",
    "phone_number2": "+23087654321",
    "email": "contact@abccompany.com",
    "customer_type": "business",
    "remarks": "Important client",
    "status": 1,
    "summary": {
      "total_orders": 15,
      "total_sales": 12,
      "total_payments": 8,
      "total_revenue": 125000.00,
      "total_paid": 100000.00,
      "outstanding_amount": 25000.00,
      "last_order_date": "2024-01-10T00:00:00Z",
      "last_sale_date": "2024-01-12T00:00:00Z",
      "last_payment_date": "2024-01-14T00:00:00Z"
    },
    "created_at": "2024-01-01T00:00:00Z",
    "updated_at": "2024-01-15T09:30:00Z"
  }
}
```

### Get Customer Orders
```http
GET /api/v1/customers/{uuid}/orders
```

### Get Customer Sales
```http
GET /api/v1/customers/{uuid}/sales
```

### Get Customer Payments
```http
GET /api/v1/customers/{uuid}/payments
```

## Product Management Endpoints

### Get Products
```http
GET /api/v1/products
```

**Query Parameters:**
- `page` (int): Page number
- `per_page` (int): Items per page
- `search` (string): Search term
- `category_id` (int): Filter by category
- `brand_id` (int): Filter by brand
- `type` (string): Filter by type
- `min_price` (decimal): Minimum price filter
- `max_price` (decimal): Maximum price filter

**Response:**
```json
{
  "success": true,
  "message": "Products retrieved successfully",
  "data": [
    {
      "id": 1,
      "uuid": "550e8400-e29b-41d4-a716-446655440000",
      "stockref": "PROD001",
      "name": "Laptop Computer",
      "description": "High-performance laptop",
      "cost_price": 800.00,
      "selling_price": 1200.00,
      "category": {
        "id": 1,
        "name": "Electronics"
      },
      "brand": {
        "id": 1,
        "name": "TechBrand"
      },
      "type": "finished",
      "size": "15.6 inch",
      "photo": "laptop.jpg",
      "status": 1,
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-15T09:30:00Z"
    }
  ]
}
```

### Create Product
```http
POST /api/v1/products
```

**Request Body:**
```json
{
  "stockref": "PROD002",
  "name": "Wireless Mouse",
  "description": "Ergonomic wireless mouse",
  "cost_price": 25.00,
  "selling_price": 45.00,
  "category_id": 1,
  "brand_id": 1,
  "type": "finished",
  "size": "Standard",
  "color": "Black"
}
```

### Get Product Details
```http
GET /api/v1/products/{uuid}
```

**Response:**
```json
{
  "success": true,
  "message": "Product retrieved successfully",
  "data": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "stockref": "PROD001",
    "name": "Laptop Computer",
    "description": "High-performance laptop",
    "cost_price": 800.00,
    "selling_price": 1200.00,
    "category": {
      "id": 1,
      "name": "Electronics"
    },
    "brand": {
      "id": 1,
      "name": "TechBrand"
    },
    "type": "finished",
    "size": "15.6 inch",
    "compartments": "1",
    "color": "Silver",
    "photo": "laptop.jpg",
    "status": 1,
    "inventory": [
      {
        "department": {
          "id": 1,
          "name": "Main Store"
        },
        "quantity": 50,
        "reserved": 5,
        "onorder": 10,
        "reorder_point": 20,
        "reorder_quantity": 50
      }
    ],
    "created_at": "2024-01-01T00:00:00Z",
    "updated_at": "2024-01-15T09:30:00Z"
  }
}
```

### Bulk Import Products
```http
POST /api/v1/products/bulk-import
```

**Request Body (multipart/form-data):**
- `file`: CSV file with product data

**Response:**
```json
{
  "success": true,
  "message": "Products imported successfully",
  "data": {
    "success": 95,
    "errors": [
      {
        "row": 5,
        "error": "Invalid stock reference format"
      }
    ],
    "skipped": 3
  }
}
```

## Order Management Endpoints

### Get Orders
```http
GET /api/v1/orders
```

**Query Parameters:**
- `page` (int): Page number
- `per_page` (int): Items per page
- `search` (string): Search term
- `status` (string): Filter by order status
- `customer_id` (int): Filter by customer
- `date_from` (date): Filter from date
- `date_to` (date): Filter to date
- `payment_status` (string): Filter by payment status

**Response:**
```json
{
  "success": true,
  "message": "Orders retrieved successfully",
  "data": [
    {
      "id": 1,
      "uuid": "550e8400-e29b-41d4-a716-446655440000",
      "order_number": "ORD2024010001",
      "customer": {
        "id": 1,
        "company_name": "ABC Company Ltd",
        "full_name": "John Smith"
      },
      "order_date": "2024-01-15",
      "delivery_date": "2024-01-20",
      "order_status": "confirmed",
      "payment_status": "pending",
      "subtotal": 1000.00,
      "tax_amount": 150.00,
      "discount_amount": 50.00,
      "shipping_amount": 25.00,
      "total_amount": 1125.00,
      "currency": "MUR",
      "created_at": "2024-01-15T10:30:00Z",
      "updated_at": "2024-01-15T10:30:00Z"
    }
  ]
}
```

### Create Order
```http
POST /api/v1/orders
```

**Request Body:**
```json
{
  "customer_id": 1,
  "order_date": "2024-01-15",
  "delivery_date": "2024-01-20",
  "payment_method": "Bank Transfer",
  "subtotal": 1000.00,
  "tax_amount": 150.00,
  "discount_amount": 50.00,
  "shipping_amount": 25.00,
  "total_amount": 1125.00,
  "currency": "MUR",
  "notes": "Urgent delivery required",
  "internal_notes": "VIP customer",
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "unit_price": 500.00,
      "discount_percent": 5.00,
      "discount_amount": 50.00,
      "tax_percent": 15.00,
      "tax_amount": 150.00,
      "line_total": 1050.00,
      "notes": "Special packaging"
    }
  ]
}
```

### Get Order Details
```http
GET /api/v1/orders/{uuid}
```

**Response:**
```json
{
  "success": true,
  "message": "Order retrieved successfully",
  "data": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "order_number": "ORD2024010001",
    "customer": {
      "id": 1,
      "company_name": "ABC Company Ltd",
      "legal_name": "ABC Company Limited",
      "full_name": "John Smith",
      "address": "123 Main Street",
      "city": "Port Louis",
      "phone_number1": "+23012345678",
      "email": "contact@abccompany.com"
    },
    "order_date": "2024-01-15",
    "delivery_date": "2024-01-20",
    "order_status": "confirmed",
    "payment_status": "pending",
    "payment_method": "Bank Transfer",
    "subtotal": 1000.00,
    "tax_amount": 150.00,
    "discount_amount": 50.00,
    "shipping_amount": 25.00,
    "total_amount": 1125.00,
    "currency": "MUR",
    "notes": "Urgent delivery required",
    "internal_notes": "VIP customer",
    "items": [
      {
        "id": 1,
        "product": {
          "id": 1,
          "name": "Laptop Computer",
          "stockref": "PROD001"
        },
        "quantity": 2,
        "unit_price": 500.00,
        "discount_percent": 5.00,
        "discount_amount": 50.00,
        "tax_percent": 15.00,
        "tax_amount": 150.00,
        "line_total": 1050.00,
        "notes": "Special packaging"
      }
    ],
    "status_history": [
      {
        "status_from": null,
        "status_to": "draft",
        "notes": "Order created",
        "created_by": {
          "name": "John Doe"
        },
        "created_at": "2024-01-15T10:30:00Z"
      }
    ],
    "created_at": "2024-01-15T10:30:00Z",
    "updated_at": "2024-01-15T10:30:00Z"
  }
}
```

### Update Order Status
```http
PATCH /api/v1/orders/{uuid}/status
```

**Request Body:**
```json
{
  "status": "processing",
  "notes": "Order is being prepared for shipment"
}
```

### Generate Order PDF
```http
GET /api/v1/orders/{uuid}/pdf
```

**Response:** PDF file download

### Email Order
```http
POST /api/v1/orders/{uuid}/email
```

**Request Body:**
```json
{
  "email": "customer@example.com",
  "subject": "Your Order Confirmation",
  "message": "Thank you for your order. Please find attached your order confirmation."
}
```

## Sales Management Endpoints

### Get Sales
```http
GET /api/v1/sales
```

### Create Sale
```http
POST /api/v1/sales
```

### Create Sale from Order
```http
POST /api/v1/sales/from-order/{order_uuid}
```

**Request Body:**
```json
{
  "selected_items": [1, 2, 3],
  "sale_date": "2024-01-15",
  "delivery_date": "2024-01-20",
  "notes": "Sale created from order"
}
```

### Get Sale Details
```http
GET /api/v1/sales/{uuid}
```

### Update Sale Status
```http
PATCH /api/v1/sales/{uuid}/status
```

## Payment Management Endpoints

### Get Payments
```http
GET /api/v1/payments
```

### Create Payment
```http
POST /api/v1/payments
```

**Request Body:**
```json
{
  "payment_date": "2024-01-15",
  "amount": 500.00,
  "payment_type_id": 1,
  "payment_method": "Bank Transfer",
  "order_id": 1,
  "customer_id": 1,
  "reference_number": "TXN123456789",
  "notes": "Payment for order ORD2024010001"
}
```

### Get Payment Details
```http
GET /api/v1/payments/{uuid}
```

### Get Payments by Order
```http
GET /api/v1/payments/by-order/{order_id}
```

### Get Payments by Customer
```http
GET /api/v1/payments/by-customer/{customer_id}
```

### Get Outstanding Payments
```http
GET /api/v1/payments/outstanding
```

## Inventory Management Endpoints

### Get Inventory
```http
GET /api/v1/inventory
```

**Query Parameters:**
- `product_id` (int): Filter by product
- `department_id` (int): Filter by department
- `low_stock` (boolean): Show only low stock items

### Get Inventory by Product
```http
GET /api/v1/inventory/product/{product_id}
```

### Get Inventory by Department
```http
GET /api/v1/inventory/department/{department_id}
```

### Create Stock Adjustment
```http
POST /api/v1/inventory/adjustment
```

**Request Body:**
```json
{
  "product_id": 1,
  "department_id": 1,
  "quantity": 10,
  "movement_type": "adjustment",
  "notes": "Stock count adjustment",
  "reference_number": "ADJ001"
}
```

### Get Stock Movements
```http
GET /api/v1/inventory/movements
```

### Get Low Stock Alerts
```http
GET /api/v1/inventory/alerts
```

## Stock Transfer Endpoints

### Get Stock Transfers
```http
GET /api/v1/stock-transfers
```

### Create Stock Transfer
```http
POST /api/v1/stock-transfers
```

**Request Body:**
```json
{
  "from_department_id": 1,
  "to_department_id": 2,
  "transfer_date": "2024-01-15",
  "notes": "Transfer to branch office",
  "items": [
    {
      "product_id": 1,
      "quantity": 10,
      "notes": "Urgent transfer"
    }
  ]
}
```

### Get Stock Transfer Details
```http
GET /api/v1/stock-transfers/{uuid}
```

### Update Transfer Status
```http
PATCH /api/v1/stock-transfers/{uuid}/status
```

**Request Body:**
```json
{
  "status": "approved",
  "notes": "Transfer approved by manager"
}
```

## Reporting Endpoints

### Get Dashboard Summary
```http
GET /api/v1/reports/dashboard
```

**Response:**
```json
{
  "success": true,
  "message": "Dashboard data retrieved successfully",
  "data": {
    "orders": {
      "total": 150,
      "pending": 25,
      "confirmed": 50,
      "processing": 30,
      "shipped": 20,
      "delivered": 20,
      "cancelled": 5
    },
    "sales": {
      "total": 120,
      "total_revenue": 250000.00,
      "this_month": 45000.00
    },
    "customers": {
      "total": 85,
      "new_this_month": 12
    },
    "inventory": {
      "total_products": 250,
      "low_stock": 15,
      "out_of_stock": 3
    },
    "payments": {
      "total_received": 200000.00,
      "outstanding": 50000.00,
      "overdue": 15000.00
    }
  }
}
```

### Get Sales Report
```http
GET /api/v1/reports/sales
```

**Query Parameters:**
- `date_from` (date): Start date
- `date_to` (date): End date
- `customer_id` (int): Filter by customer
- `format` (string): Response format (json, csv, pdf)

### Get Inventory Report
```http
GET /api/v1/reports/inventory
```

### Get Payment Report
```http
GET /api/v1/reports/payments
```

## Error Handling

### HTTP Status Codes
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `429` - Too Many Requests
- `500` - Internal Server Error

### Validation Errors
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### Rate Limiting
```json
{
  "success": false,
  "message": "Too many requests",
  "errors": {
    "rate_limit": ["Rate limit exceeded. Try again in 60 seconds."]
  }
}
```

## API Versioning

### Version Header
```http
Accept: application/vnd.z5distribution.v1+json
```

### Version in URL
```
/api/v1/orders
/api/v2/orders
```

## Webhooks

### Order Status Changed
```http
POST {webhook_url}
```

**Payload:**
```json
{
  "event": "order.status_changed",
  "data": {
    "order": {
      "uuid": "550e8400-e29b-41d4-a716-446655440000",
      "order_number": "ORD2024010001",
      "status": "shipped"
    }
  },
  "timestamp": "2024-01-15T10:30:00Z"
}
```

### Payment Received
```http
POST {webhook_url}
```

**Payload:**
```json
{
  "event": "payment.received",
  "data": {
    "payment": {
      "uuid": "550e8400-e29b-41d4-a716-446655440000",
      "amount": 500.00,
      "order_id": 1
    }
  },
  "timestamp": "2024-01-15T10:30:00Z"
}
```

This comprehensive API specification provides all the endpoints needed for the Z5 Distribution System with clear request/response formats, authentication, and error handling.
