# Product Catalog Management System

## Overview
The Product Catalog Management system handles comprehensive product information, categorization, pricing, and inventory tracking. It provides a centralized repository for all product data used across orders, sales, and inventory management.

## Core Features

### Product Management
- **Product Creation**: Add new products with comprehensive details
- **Product Editing**: Modify existing product information
- **Product Deletion**: Soft delete products with audit trail
- **Product Search**: Advanced search and filtering capabilities
- **Bulk Operations**: Import/export products via CSV
- **Product Images**: Upload and manage product photos

### Product Information
- **Basic Details**: Name, description, stock reference
- **Pricing**: Cost price, selling price, margin calculation
- **Categorization**: Product categories and subcategories
- **Branding**: Product brands and manufacturers
- **Specifications**: Size, compartments, color, type
- **Status Management**: Active/inactive product status

### Product Categories
- **Category Management**: Create and manage product categories
- **Hierarchical Structure**: Support for category hierarchies
- **Category Assignment**: Assign products to categories
- **Category Filtering**: Filter products by category

### Product Brands
- **Brand Management**: Create and manage product brands
- **Brand Assignment**: Assign products to brands
- **Brand Filtering**: Filter products by brand

## Database Schema

### Products Table
```sql
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    stockref VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    cost_price DECIMAL(10,2) NOT NULL,
    selling_price DECIMAL(10,2) NOT NULL,
    category_id INT NOT NULL,
    brand_id INT NOT NULL,
    type ENUM('finished','semi','raw') DEFAULT 'finished',
    size VARCHAR(50) NULL,
    compartments VARCHAR(50) NULL,
    color VARCHAR(50) NULL,
    photo VARCHAR(100) NULL,
    uom_id INT DEFAULT 1,
    created_by INT NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_by INT NULL,
    updated_date DATETIME NULL,
    status TINYINT DEFAULT 1,
    FOREIGN KEY (category_id) REFERENCES product_categories(id),
    FOREIGN KEY (brand_id) REFERENCES product_brands(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);
```

### Product Categories Table
```sql
CREATE TABLE product_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    parent_id INT NULL,
    display_order INT DEFAULT 0,
    created_by INT NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    FOREIGN KEY (parent_id) REFERENCES product_categories(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

### Product Brands Table
```sql
CREATE TABLE product_brands (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    logo VARCHAR(100) NULL,
    website VARCHAR(255) NULL,
    created_by INT NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

### Product Images Table
```sql
CREATE TABLE product_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    product_id INT NOT NULL,
    filename VARCHAR(200) NOT NULL,
    file_type VARCHAR(20) NULL,
    width INT NOT NULL,
    height INT NOT NULL,
    file_size FLOAT NOT NULL,
    is_primary TINYINT DEFAULT 0,
    display_order INT DEFAULT 0,
    created_by INT NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

## API Endpoints

### Product Management
- `GET /api/products` - List all products with filtering
- `POST /api/products` - Create new product
- `GET /api/products/{uuid}` - Get product details
- `PUT /api/products/{uuid}` - Update product
- `DELETE /api/products/{uuid}` - Soft delete product
- `POST /api/products/bulk-import` - Import products from CSV
- `GET /api/products/export` - Export products to CSV

### Product Categories
- `GET /api/categories` - List all categories
- `POST /api/categories` - Create new category
- `GET /api/categories/{id}` - Get category details
- `PUT /api/categories/{id}` - Update category
- `DELETE /api/categories/{id}` - Delete category

### Product Brands
- `GET /api/brands` - List all brands
- `POST /api/brands` - Create new brand
- `GET /api/brands/{id}` - Get brand details
- `PUT /api/brands/{id}` - Update brand
- `DELETE /api/brands/{id}` - Delete brand

### Product Images
- `GET /api/products/{uuid}/images` - Get product images
- `POST /api/products/{uuid}/images` - Upload product image
- `PUT /api/products/{uuid}/images/{image_id}` - Update image
- `DELETE /api/products/{uuid}/images/{image_id}` - Delete image

## Business Logic

### Product Creation
```php
public function createProduct(array $data): Product
{
    // Validate stock reference uniqueness
    if (Product::where('stockref', $data['stockref'])->exists()) {
        throw new ValidationException('Stock reference already exists');
    }
    
    $product = Product::create([
        'uuid' => Str::uuid(),
        'stockref' => $data['stockref'],
        'name' => $data['name'],
        'description' => $data['description'],
        'cost_price' => $data['cost_price'],
        'selling_price' => $data['selling_price'],
        'category_id' => $data['category_id'],
        'brand_id' => $data['brand_id'],
        'type' => $data['type'] ?? 'finished',
        'size' => $data['size'],
        'compartments' => $data['compartments'],
        'color' => $data['color'],
        'created_by' => auth()->id(),
        'status' => 1
    ]);
    
    // Create inventory records for all departments
    $this->createInventoryRecords($product);
    
    return $product;
}
```

### Bulk Import
```php
public function importProductsFromCSV(UploadedFile $file): array
{
    $results = [
        'success' => 0,
        'errors' => [],
        'skipped' => 0
    ];
    
    $csvData = array_map('str_getcsv', file($file->getPathname()));
    $headers = array_shift($csvData);
    
    foreach ($csvData as $index => $row) {
        try {
            $data = array_combine($headers, $row);
            
            // Validate required fields
            $this->validateImportData($data);
            
            // Check if product already exists
            if (Product::where('stockref', $data['stockref'])->exists()) {
                $results['skipped']++;
                continue;
            }
            
            // Create product
            $this->createProduct($data);
            $results['success']++;
            
        } catch (Exception $e) {
            $results['errors'][] = [
                'row' => $index + 2,
                'error' => $e->getMessage()
            ];
        }
    }
    
    return $results;
}
```

### Product Search
```php
public function searchProducts(string $query, array $filters = []): Collection
{
    $products = Product::with(['category', 'brand'])
        ->where('status', 1);
    
    // Text search
    if ($query) {
        $products->where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%")
              ->orWhere('stockref', 'like', "%{$query}%");
        });
    }
    
    // Category filter
    if (isset($filters['category_id'])) {
        $products->where('category_id', $filters['category_id']);
    }
    
    // Brand filter
    if (isset($filters['brand_id'])) {
        $products->where('brand_id', $filters['brand_id']);
    }
    
    // Type filter
    if (isset($filters['type'])) {
        $products->where('type', $filters['type']);
    }
    
    // Price range filter
    if (isset($filters['min_price'])) {
        $products->where('selling_price', '>=', $filters['min_price']);
    }
    if (isset($filters['max_price'])) {
        $products->where('selling_price', '<=', $filters['max_price']);
    }
    
    return $products->orderBy('name')->get();
}
```

### Inventory Record Creation
```php
private function createInventoryRecords(Product $product): void
{
    $departments = Department::where('status', 1)->get();
    
    foreach ($departments as $department) {
        Inventory::create([
            'uuid' => Str::uuid(),
            'product_id' => $product->id,
            'department_id' => $department->id,
            'quantity' => 0,
            'reserved' => 0,
            'onorder' => 0,
            'status' => 1
        ]);
    }
}
```

## User Interface Components

### Product List View
- **Data Table**: Sortable, filterable product listing
- **Category Filters**: Quick filter by product category
- **Brand Filters**: Quick filter by product brand
- **Search**: Search by name, description, or stock reference
- **Actions**: View, Edit, Delete, Manage Images buttons
- **Pagination**: Efficient handling of large product catalogs

### Product Form
- **Basic Information**: Name, description, stock reference
- **Pricing**: Cost price and selling price with margin display
- **Categorization**: Category and brand selection
- **Specifications**: Size, compartments, color, type
- **Image Upload**: Drag-and-drop image upload
- **Validation**: Real-time validation feedback

### Product Detail View
- **Product Information**: Complete product details
- **Images**: Product image gallery with primary image
- **Inventory**: Current inventory levels across departments
- **Sales History**: Recent sales and order history
- **Actions**: Edit, Delete, Manage Images, View Inventory

### Bulk Import Interface
- **CSV Upload**: File upload with validation
- **Preview**: Preview of imported data before processing
- **Mapping**: Map CSV columns to product fields
- **Results**: Import results with success/error summary
- **Error Handling**: Detailed error reporting for failed imports

## Integration Points

### Orders System
- **Product Selection**: Select products for orders
- **Pricing**: Use current selling prices
- **Availability**: Check product availability
- **Stock Updates**: Update inventory when orders are processed

### Sales System
- **Product Selection**: Select products for sales
- **Pricing**: Use current selling prices
- **Availability**: Check product availability
- **Stock Updates**: Update inventory when sales are processed

### Inventory System
- **Stock Tracking**: Track inventory levels per product
- **Reservations**: Reserve stock for orders/sales
- **Updates**: Update stock levels when products are sold
- **Alerts**: Low stock alerts and reorder points

### Customer Management
- **Product History**: Track customer product preferences
- **Recommendations**: Suggest products based on history
- **Pricing**: Customer-specific pricing if applicable

## Business Rules

### Product Creation Rules
- Stock reference must be unique
- Product name is required
- Cost price and selling price must be positive
- Category and brand must be selected and active
- Selling price should be greater than cost price

### Product Update Rules
- Stock reference cannot be changed if product has orders/sales
- Price changes should be logged for audit purposes
- Category/brand changes should update related records
- Status changes should validate business rules

### Product Deletion Rules
- Products with existing orders/sales cannot be deleted
- Soft delete only (status = 0)
- Maintain referential integrity
- Preserve audit trail

## Security Considerations

### Access Control
- **Role-based Access**: Different permissions for different user levels
- **Product Ownership**: Users can only modify products they created (unless admin)
- **Bulk Operations**: Only authorized users can perform bulk operations

### Data Validation
- **Input Validation**: Validate all product data before saving
- **File Upload Security**: Validate uploaded images and CSV files
- **Business Rules**: Enforce business rules (e.g., positive prices)

### Audit Trail
- **Change Tracking**: Log all product modifications
- **User Attribution**: Track who made changes and when
- **Price History**: Track price changes over time

## Performance Considerations

### Database Optimization
- **Indexing**: Proper indexes on frequently queried fields
- **Pagination**: Efficient pagination for large product lists
- **Caching**: Cache frequently accessed product data

### Query Optimization
- **Eager Loading**: Load related data efficiently
- **Selective Loading**: Only load necessary data for list views
- **Search Optimization**: Optimized search queries with full-text indexes

### File Management
- **Image Optimization**: Compress and optimize uploaded images
- **CDN Integration**: Use CDN for image delivery
- **Storage Optimization**: Efficient file storage and retrieval

## Testing Requirements

### Unit Tests
- Product creation and validation
- Stock reference uniqueness
- Price calculations
- Category and brand assignment
- Bulk import functionality

### Integration Tests
- Order system integration
- Sales system integration
- Inventory system integration
- Image upload and management

### User Acceptance Tests
- Product creation workflow
- Product management operations
- Search and filtering
- Bulk import process
- Image management

## Migration Notes

### From CodeIgniter
- **Model Structure**: Convert CI models to Laravel Eloquent models
- **Controller Logic**: Adapt CI controllers to Laravel controllers
- **Database Queries**: Convert CI query builder to Eloquent
- **File Upload**: Replace CI upload library with Laravel Storage

### Data Migration
- **Product Data**: Migrate existing products with UUID preservation
- **Categories**: Migrate product categories with relationships
- **Brands**: Migrate product brands with relationships
- **Images**: Migrate product images with file preservation
- **Inventory**: Create inventory records for existing products
- **Audit Trail**: Maintain audit trail continuity
