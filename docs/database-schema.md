# Database Schema

## Overview
This document defines the complete database schema for the Z5 Distribution System. It includes all tables, relationships, indexes, and constraints needed for the application.

## Database Design Principles

### Core Principles
- **Normalization**: Proper database normalization to avoid redundancy
- **Referential Integrity**: Foreign key constraints to maintain data consistency
- **Performance**: Optimized indexes for fast queries
- **Scalability**: Design that supports growth and expansion
- **Audit Trail**: Complete tracking of data changes

### Naming Conventions
- **Tables**: snake_case plural (e.g., `orders`, `order_items`)
- **Columns**: snake_case (e.g., `order_date`, `customer_id`)
- **Foreign Keys**: `{table}_id` (e.g., `customer_id`, `product_id`)
- **UUIDs**: All entities have UUID fields for external references
- **Timestamps**: `created_at`, `updated_at` for all tables

## Core Tables

### Users Table
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    username VARCHAR(255) UNIQUE NOT NULL,
    user_level ENUM('Normal','Admin','Root') DEFAULT 'Normal',
    job_title VARCHAR(255) NULL,
    photo VARCHAR(255) NULL,
    store_id BIGINT UNSIGNED NULL,
    landing_page VARCHAR(255) DEFAULT 'dashboard',
    last_login TIMESTAMP NULL,
    ip VARCHAR(45) NULL,
    token VARCHAR(100) NULL,
    token_valid_until TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    status TINYINT DEFAULT 1 COMMENT '1-Active,2-Inactive,0-deleted',
    
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    
    INDEX idx_users_email (email),
    INDEX idx_users_username (username),
    INDEX idx_users_user_level (user_level),
    INDEX idx_users_status (status),
    INDEX idx_users_created_at (created_at)
);
```

### Customers Table
```sql
CREATE TABLE customers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    company_name VARCHAR(255) NOT NULL,
    legal_name VARCHAR(255) NULL,
    brn VARCHAR(50) NULL,
    vat VARCHAR(50) NULL,
    full_name VARCHAR(255) NULL,
    address TEXT NULL,
    city VARCHAR(255) NULL,
    phone_number1 VARCHAR(50) NOT NULL,
    phone_number2 VARCHAR(50) NULL,
    email VARCHAR(255) NULL,
    customer_type ENUM('business','individual') DEFAULT 'business',
    remarks TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    status TINYINT DEFAULT 1,
    
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    
    INDEX idx_customers_company_name (company_name),
    INDEX idx_customers_email (email),
    INDEX idx_customers_phone (phone_number1),
    INDEX idx_customers_city (city),
    INDEX idx_customers_status (status),
    INDEX idx_customers_created_at (created_at)
);
```

### Products Table
```sql
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    stockref VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    cost_price DECIMAL(10,2) NOT NULL,
    selling_price DECIMAL(10,2) NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    brand_id BIGINT UNSIGNED NOT NULL,
    type ENUM('finished','semi','raw') DEFAULT 'finished',
    size VARCHAR(100) NULL,
    compartments VARCHAR(100) NULL,
    color VARCHAR(100) NULL,
    photo VARCHAR(255) NULL,
    uom_id BIGINT UNSIGNED DEFAULT 1,
    created_by BIGINT UNSIGNED NOT NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    status TINYINT DEFAULT 1,
    
    FOREIGN KEY (category_id) REFERENCES product_categories(id),
    FOREIGN KEY (brand_id) REFERENCES product_brands(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    
    INDEX idx_products_stockref (stockref),
    INDEX idx_products_name (name),
    INDEX idx_products_category (category_id),
    INDEX idx_products_brand (brand_id),
    INDEX idx_products_type (type),
    INDEX idx_products_status (status),
    INDEX idx_products_created_at (created_at)
);
```

### Product Categories Table
```sql
CREATE TABLE product_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    parent_id BIGINT UNSIGNED NULL,
    display_order INT DEFAULT 0,
    created_by BIGINT UNSIGNED NOT NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    status TINYINT DEFAULT 1,
    
    FOREIGN KEY (parent_id) REFERENCES product_categories(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    
    INDEX idx_categories_name (name),
    INDEX idx_categories_parent (parent_id),
    INDEX idx_categories_status (status)
);
```

### Product Brands Table
```sql
CREATE TABLE product_brands (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    logo VARCHAR(255) NULL,
    website VARCHAR(255) NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    status TINYINT DEFAULT 1,
    
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    
    INDEX idx_brands_name (name),
    INDEX idx_brands_status (status)
);
```

## Order Management Tables

### Orders Table
```sql
CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    customer_id BIGINT UNSIGNED NOT NULL,
    order_date DATE NOT NULL,
    delivery_date DATE NULL,
    order_status ENUM('draft','pending','confirmed','processing','shipped','delivered','cancelled') DEFAULT 'draft',
    payment_status ENUM('pending','partial','paid','overdue') DEFAULT 'pending',
    payment_method VARCHAR(100) NULL,
    subtotal DECIMAL(10,2) DEFAULT 0.00,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    shipping_amount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) DEFAULT 0.00,
    currency VARCHAR(3) DEFAULT 'MUR',
    notes TEXT NULL,
    internal_notes TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    status TINYINT DEFAULT 1,
    
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    
    INDEX idx_orders_order_number (order_number),
    INDEX idx_orders_customer (customer_id),
    INDEX idx_orders_status (order_status),
    INDEX idx_orders_payment_status (payment_status),
    INDEX idx_orders_date (order_date),
    INDEX idx_orders_created_at (created_at)
);
```

### Order Items Table
```sql
CREATE TABLE order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    discount_percent DECIMAL(5,2) DEFAULT 0.00,
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    tax_percent DECIMAL(5,2) DEFAULT 0.00,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    line_total DECIMAL(10,2) NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    status TINYINT DEFAULT 1,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    
    INDEX idx_order_items_order (order_id),
    INDEX idx_order_items_product (product_id),
    INDEX idx_order_items_status (status)
);
```

### Order Status History Table
```sql
CREATE TABLE order_status_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    order_id BIGINT UNSIGNED NOT NULL,
    status_from VARCHAR(50) NULL,
    status_to VARCHAR(50) NOT NULL,
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id),
    
    INDEX idx_order_status_history_order (order_id),
    INDEX idx_order_status_history_created_at (created_at)
);
```

## Sales Management Tables

### Sales Table
```sql
CREATE TABLE sales (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    sale_number VARCHAR(50) UNIQUE NOT NULL,
    order_id BIGINT UNSIGNED NULL,
    customer_id BIGINT UNSIGNED NOT NULL,
    sale_date DATE NOT NULL,
    delivery_date DATE NULL,
    sale_status ENUM('draft','pending','confirmed','processing','shipped','delivered','cancelled') DEFAULT 'draft',
    payment_status ENUM('pending','partial','paid','overdue') DEFAULT 'pending',
    payment_method VARCHAR(100) NULL,
    subtotal DECIMAL(10,2) DEFAULT 0.00,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    shipping_amount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) DEFAULT 0.00,
    currency VARCHAR(3) DEFAULT 'MUR',
    notes TEXT NULL,
    internal_notes TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    status TINYINT DEFAULT 1,
    
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    
    INDEX idx_sales_sale_number (sale_number),
    INDEX idx_sales_order (order_id),
    INDEX idx_sales_customer (customer_id),
    INDEX idx_sales_status (sale_status),
    INDEX idx_sales_payment_status (payment_status),
    INDEX idx_sales_date (sale_date),
    INDEX idx_sales_created_at (created_at)
);
```

### Sales Items Table
```sql
CREATE TABLE sales_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    sale_id BIGINT UNSIGNED NOT NULL,
    order_item_id BIGINT UNSIGNED NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    discount_percent DECIMAL(5,2) DEFAULT 0.00,
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    tax_percent DECIMAL(5,2) DEFAULT 0.00,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    line_total DECIMAL(10,2) NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    status TINYINT DEFAULT 1,
    
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (order_item_id) REFERENCES order_items(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    
    INDEX idx_sales_items_sale (sale_id),
    INDEX idx_sales_items_order_item (order_item_id),
    INDEX idx_sales_items_product (product_id),
    INDEX idx_sales_items_status (status)
);
```

### Sales Status History Table
```sql
CREATE TABLE sales_status_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    sale_id BIGINT UNSIGNED NOT NULL,
    status_from VARCHAR(50) NULL,
    status_to VARCHAR(50) NOT NULL,
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id),
    
    INDEX idx_sales_status_history_sale (sale_id),
    INDEX idx_sales_status_history_created_at (created_at)
);
```

## Payment Management Tables

### Payment Types Table
```sql
CREATE TABLE payment_types (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    is_default TINYINT DEFAULT 0,
    display_order INT DEFAULT 0,
    created_by BIGINT UNSIGNED NOT NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    status TINYINT DEFAULT 1,
    
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    
    INDEX idx_payment_types_name (name),
    INDEX idx_payment_types_status (status)
);
```

### Payments Table
```sql
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    payment_date DATE NOT NULL,
    payment_number VARCHAR(50) UNIQUE NOT NULL,
    payment_type ENUM('disbursement','receipt','refund','adjustment','other') DEFAULT 'receipt',
    payment_method VARCHAR(100) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_type_id BIGINT UNSIGNED NOT NULL,
    order_id BIGINT UNSIGNED NULL,
    sale_id BIGINT UNSIGNED NULL,
    customer_id BIGINT UNSIGNED NULL,
    reference_number VARCHAR(100) NULL,
    notes TEXT NULL,
    due_date DATE NULL,
    payment_status ENUM('pending','paid','partial','overdue','cancelled') DEFAULT 'pending',
    created_by BIGINT UNSIGNED NOT NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    status TINYINT DEFAULT 1,
    
    FOREIGN KEY (payment_type_id) REFERENCES payment_types(id),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    
    INDEX idx_payments_payment_number (payment_number),
    INDEX idx_payments_payment_type (payment_type_id),
    INDEX idx_payments_order (order_id),
    INDEX idx_payments_sale (sale_id),
    INDEX idx_payments_customer (customer_id),
    INDEX idx_payments_status (payment_status),
    INDEX idx_payments_date (payment_date),
    INDEX idx_payments_created_at (created_at)
);
```

### Payment History Table
```sql
CREATE TABLE payment_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    payment_id BIGINT UNSIGNED NOT NULL,
    action ENUM('created','updated','status_changed','deleted') NOT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id),
    
    INDEX idx_payment_history_payment (payment_id),
    INDEX idx_payment_history_action (action),
    INDEX idx_payment_history_created_at (created_at)
);
```

## Inventory Management Tables

### Departments Table
```sql
CREATE TABLE departments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    address TEXT NULL,
    phone_number VARCHAR(50) NULL,
    email VARCHAR(255) NULL,
    manager_id BIGINT UNSIGNED NULL,
    is_main TINYINT DEFAULT 0,
    created_by BIGINT UNSIGNED NOT NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    status TINYINT DEFAULT 1,
    
    FOREIGN KEY (manager_id) REFERENCES users(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    
    INDEX idx_departments_name (name),
    INDEX idx_departments_manager (manager_id),
    INDEX idx_departments_status (status)
);
```

### Inventory Table
```sql
CREATE TABLE inventory (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    department_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    reserved INT NOT NULL DEFAULT 0,
    onorder INT NOT NULL DEFAULT 0,
    reorder_point INT DEFAULT 0,
    reorder_quantity INT DEFAULT 0,
    bin_location VARCHAR(100) NULL,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    status TINYINT DEFAULT 1,
    
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    
    UNIQUE KEY unique_product_department (product_id, department_id),
    INDEX idx_inventory_product (product_id),
    INDEX idx_inventory_department (department_id),
    INDEX idx_inventory_quantity (quantity),
    INDEX idx_inventory_reorder_point (reorder_point)
);
```

### Stock Movements Table
```sql
CREATE TABLE stock_movements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    department_id BIGINT UNSIGNED NOT NULL,
    movement_type ENUM('in','out','transfer','adjustment','count') NOT NULL,
    quantity INT NOT NULL,
    reference_type ENUM('order','sale','purchase','transfer','adjustment','count') NULL,
    reference_id BIGINT UNSIGNED NULL,
    reference_number VARCHAR(100) NULL,
    notes TEXT NULL,
    movement_date TIMESTAMP NOT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    
    INDEX idx_stock_movements_product (product_id),
    INDEX idx_stock_movements_department (department_id),
    INDEX idx_stock_movements_type (movement_type),
    INDEX idx_stock_movements_reference (reference_type, reference_id),
    INDEX idx_stock_movements_date (movement_date)
);
```

### Stock Transfers Table
```sql
CREATE TABLE stock_transfers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    transfer_number VARCHAR(50) UNIQUE NOT NULL,
    from_department_id BIGINT UNSIGNED NOT NULL,
    to_department_id BIGINT UNSIGNED NOT NULL,
    transfer_date DATE NOT NULL,
    status ENUM('pending','approved','shipped','received','cancelled') DEFAULT 'pending',
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    approved_by BIGINT UNSIGNED NULL,
    shipped_by BIGINT UNSIGNED NULL,
    received_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    approved_at TIMESTAMP NULL,
    shipped_at TIMESTAMP NULL,
    received_at TIMESTAMP NULL,
    
    FOREIGN KEY (from_department_id) REFERENCES departments(id),
    FOREIGN KEY (to_department_id) REFERENCES departments(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    FOREIGN KEY (shipped_by) REFERENCES users(id),
    FOREIGN KEY (received_by) REFERENCES users(id),
    
    INDEX idx_stock_transfers_number (transfer_number),
    INDEX idx_stock_transfers_from (from_department_id),
    INDEX idx_stock_transfers_to (to_department_id),
    INDEX idx_stock_transfers_status (status),
    INDEX idx_stock_transfers_date (transfer_date)
);
```

### Stock Transfer Items Table
```sql
CREATE TABLE stock_transfer_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    transfer_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    
    FOREIGN KEY (transfer_id) REFERENCES stock_transfers(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    
    INDEX idx_transfer_items_transfer (transfer_id),
    INDEX idx_transfer_items_product (product_id)
);
```

## System Tables

### Permissions Table
```sql
CREATE TABLE permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    menu_id BIGINT UNSIGNED NOT NULL,
    create TINYINT DEFAULT 0,
    read TINYINT DEFAULT 0,
    update TINYINT DEFAULT 0,
    delete TINYINT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menu(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_user_menu (user_id, menu_id),
    INDEX idx_permissions_user (user_id),
    INDEX idx_permissions_menu (menu_id)
);
```

### Menu Table
```sql
CREATE TABLE menu (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type ENUM('menu','section','divider') DEFAULT 'menu',
    nom VARCHAR(100) NULL,
    controller VARCHAR(100) NULL,
    action VARCHAR(100) NULL,
    color VARCHAR(7) DEFAULT '#FFFFFF',
    url VARCHAR(255) NULL,
    class VARCHAR(100) NULL,
    display_order INT DEFAULT 50,
    parent_menu BIGINT UNSIGNED NULL,
    visible TINYINT DEFAULT 1,
    Normal TINYINT DEFAULT 0,
    Admin TINYINT DEFAULT 0,
    Root TINYINT DEFAULT 1,
    module INT NOT NULL,
    status TINYINT DEFAULT 1,
    backoffice TINYINT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (parent_menu) REFERENCES menu(id),
    
    INDEX idx_menu_parent (parent_menu),
    INDEX idx_menu_display_order (display_order),
    INDEX idx_menu_status (status)
);
```

### Login History Table
```sql
CREATE TABLE login_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NULL,
    user_id BIGINT UNSIGNED NULL,
    datetime TIMESTAMP NOT NULL,
    result ENUM('SUCCESS','FAILED','OTHER') NOT NULL,
    ip VARCHAR(45) NOT NULL,
    result_other TEXT NULL,
    os VARCHAR(100) NULL,
    browser VARCHAR(100) NULL,
    store_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    
    INDEX idx_login_history_user (user_id),
    INDEX idx_login_history_datetime (datetime),
    INDEX idx_login_history_result (result),
    INDEX idx_login_history_ip (ip)
);
```

### Audit Trail Table
```sql
CREATE TABLE audit_trail (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    action VARCHAR(100) NOT NULL,
    resource_type VARCHAR(100) NOT NULL,
    resource_id BIGINT UNSIGNED NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    
    INDEX idx_audit_trail_user (user_id),
    INDEX idx_audit_trail_action (action),
    INDEX idx_audit_trail_resource (resource_type, resource_id),
    INDEX idx_audit_trail_created_at (created_at)
);
```

## Data Seeding

### Initial Data Setup
```sql
-- Insert default payment types
INSERT INTO payment_types (uuid, name, description, is_default, display_order, created_by, created_at, updated_at, status) VALUES
(UUID(), 'Cash', 'Cash payment', 1, 1, 1, NOW(), NOW(), 1),
(UUID(), 'Bank Transfer', 'Bank transfer payment', 1, 2, 1, NOW(), NOW(), 1),
(UUID(), 'MCB Juice', 'MCB Juice mobile payment', 1, 3, 1, NOW(), NOW(), 1),
(UUID(), 'MyT Money', 'MyT Money mobile payment', 1, 4, 1, NOW(), NOW(), 1),
(UUID(), 'Blink', 'Blink mobile payment', 1, 5, 1, NOW(), NOW(), 1);

-- Insert default department
INSERT INTO departments (uuid, name, description, address, phone_number, email, created_by, created_at, status) VALUES
(UUID(), 'Main Store', 'Main store location', 'Main Address', '12345678', 'info@company.com', 1, NOW(), 1);

-- Insert default product categories
INSERT INTO product_categories (uuid, name, description, created_by, created_at, status) VALUES
(UUID(), 'Electronics', 'Electronic products', 1, NOW(), 1),
(UUID(), 'Clothing', 'Clothing and apparel', 1, NOW(), 1),
(UUID(), 'Home & Garden', 'Home and garden products', 1, NOW(), 1);

-- Insert default product brands
INSERT INTO product_brands (uuid, name, description, created_by, created_at, status) VALUES
(UUID(), 'Generic', 'Generic brand', 1, NOW(), 1),
(UUID(), 'Premium', 'Premium brand', 1, NOW(), 1);
```

## Performance Optimization

### Indexes for Performance
```sql
-- Additional indexes for performance
CREATE INDEX idx_orders_customer_status ON orders(customer_id, order_status);
CREATE INDEX idx_orders_date_status ON orders(order_date, order_status);
CREATE INDEX idx_sales_customer_status ON sales(customer_id, sale_status);
CREATE INDEX idx_sales_date_status ON sales(sale_date, sale_status);
CREATE INDEX idx_payments_customer_date ON payments(customer_id, payment_date);
CREATE INDEX idx_inventory_product_quantity ON inventory(product_id, quantity);
CREATE INDEX idx_stock_movements_product_date ON stock_movements(product_id, movement_date);
```

### Partitioning Strategy
```sql
-- Partition large tables by date for better performance
ALTER TABLE audit_trail PARTITION BY RANGE (YEAR(created_at)) (
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p2026 VALUES LESS THAN (2027),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);

ALTER TABLE login_history PARTITION BY RANGE (YEAR(datetime)) (
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p2026 VALUES LESS THAN (2027),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);
```

This database schema provides a comprehensive foundation for the Z5 Distribution System with proper relationships, indexes, and constraints to ensure data integrity and performance.
