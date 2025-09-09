# Audit Trail

## Overview
This document defines the audit trail system for the Z5 Distribution System. It includes activity logging, data change tracking, and compliance requirements for maintaining a complete record of system activities.

## Audit Trail Design Principles

### Core Principles
- **Complete Tracking**: Log all significant system activities
- **Data Integrity**: Ensure audit logs cannot be tampered with
- **Performance**: Minimal impact on system performance
- **Compliance**: Meet regulatory and business requirements
- **Searchability**: Easy to search and filter audit logs
- **Retention**: Proper data retention and archival

### Audit Requirements
- **User Actions**: Track all user-initiated actions
- **Data Changes**: Log all data modifications
- **System Events**: Record system-level events
- **Access Control**: Track authentication and authorization
- **Data Access**: Monitor data access patterns
- **Configuration Changes**: Log system configuration updates

## Audit Trail Implementation

### Database Schema

#### Audit Trail Table
```sql
CREATE TABLE audit_trail (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    action VARCHAR(100) NOT NULL,
    resource_type VARCHAR(100) NOT NULL,
    resource_id BIGINT UNSIGNED NULL,
    resource_uuid VARCHAR(40) NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    session_id VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    
    INDEX idx_audit_trail_user (user_id),
    INDEX idx_audit_trail_action (action),
    INDEX idx_audit_trail_resource (resource_type, resource_id),
    INDEX idx_audit_trail_created_at (created_at),
    INDEX idx_audit_trail_session (session_id)
);
```

#### Login History Table
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

### Laravel Implementation

#### Audit Trail Model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditTrail extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'action',
        'resource_type',
        'resource_id',
        'resource_uuid',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'session_id'
    ];
    
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime'
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function getResourceAttribute()
    {
        if ($this->resource_type && $this->resource_id) {
            $modelClass = 'App\\Models\\' . $this->resource_type;
            return $modelClass::find($this->resource_id);
        }
        return null;
    }
}
```

#### Audit Trail Service
```php
<?php

namespace App\Services;

use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuditTrailService
{
    public function log(
        string $action,
        string $resourceType,
        $resourceId = null,
        array $oldValues = null,
        array $newValues = null,
        Request $request = null
    ): void {
        $user = Auth::user();
        $request = $request ?? request();
        
        AuditTrail::create([
            'uuid' => Str::uuid(),
            'user_id' => $user ? $user->id : null,
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'resource_uuid' => $this->getResourceUuid($resourceType, $resourceId),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $request->session()->getId()
        ]);
    }
    
    private function getResourceUuid(string $resourceType, $resourceId): ?string
    {
        if (!$resourceId) {
            return null;
        }
        
        $modelClass = 'App\\Models\\' . $resourceType;
        $model = $modelClass::find($resourceId);
        
        return $model ? $model->uuid : null;
    }
    
    public function logUserAction(string $action, array $data = []): void
    {
        $this->log($action, 'User', Auth::id(), null, $data);
    }
    
    public function logDataChange(
        string $action,
        string $resourceType,
        $resourceId,
        array $oldValues,
        array $newValues
    ): void {
        $this->log($action, $resourceType, $resourceId, $oldValues, $newValues);
    }
    
    public function logSystemEvent(string $event, array $data = []): void
    {
        $this->log($event, 'System', null, null, $data);
    }
}
```

#### Audit Trail Trait
```php
<?php

namespace App\Traits;

use App\Services\AuditTrailService;
use Illuminate\Support\Facades\App;

trait Auditable
{
    protected static function bootAuditable()
    {
        static::created(function ($model) {
            App::make(AuditTrailService::class)->log(
                'created',
                class_basename($model),
                $model->id,
                null,
                $model->getAttributes()
            );
        });
        
        static::updated(function ($model) {
            App::make(AuditTrailService::class)->log(
                'updated',
                class_basename($model),
                $model->id,
                $model->getOriginal(),
                $model->getChanges()
            );
        });
        
        static::deleted(function ($model) {
            App::make(AuditTrailService::class)->log(
                'deleted',
                class_basename($model),
                $model->id,
                $model->getAttributes(),
                null
            );
        });
    }
}
```

### Model Integration

#### Order Model with Audit Trail
```php
<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use Auditable;
    
    protected $fillable = [
        'uuid',
        'order_number',
        'customer_id',
        'order_date',
        'delivery_date',
        'order_status',
        'payment_status',
        'payment_method',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'shipping_amount',
        'total_amount',
        'currency',
        'notes',
        'internal_notes'
    ];
    
    protected $casts = [
        'order_date' => 'date',
        'delivery_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total_amount' => 'decimal:2'
    ];
    
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    
    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
    
    public function auditTrail(): HasMany
    {
        return $this->hasMany(AuditTrail::class, 'resource_id')
            ->where('resource_type', 'Order');
    }
}
```

### Controller Integration

#### Order Controller with Audit Trail
```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Services\AuditTrailService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $auditTrailService;
    
    public function __construct(AuditTrailService $auditTrailService)
    {
        $this->auditTrailService = $auditTrailService;
    }
    
    public function store(CreateOrderRequest $request)
    {
        $order = Order::create($request->validated());
        
        $this->auditTrailService->log(
            'order_created',
            'Order',
            $order->id,
            null,
            $order->toArray()
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $order
        ], 201);
    }
    
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $oldValues = $order->toArray();
        
        $order->update($request->validated());
        
        $this->auditTrailService->log(
            'order_updated',
            'Order',
            $order->id,
            $oldValues,
            $order->toArray()
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
            'data' => $order
        ]);
    }
    
    public function destroy(Order $order)
    {
        $oldValues = $order->toArray();
        
        $order->delete();
        
        $this->auditTrailService->log(
            'order_deleted',
            'Order',
            $order->id,
            $oldValues,
            null
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully'
        ]);
    }
    
    public function updateStatus(Request $request, Order $order)
    {
        $oldStatus = $order->order_status;
        $newStatus = $request->status;
        
        $order->update(['order_status' => $newStatus]);
        
        $this->auditTrailService->log(
            'order_status_changed',
            'Order',
            $order->id,
            ['order_status' => $oldStatus],
            ['order_status' => $newStatus]
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => $order
        ]);
    }
}
```

## Audit Trail Interface

### Audit Trail List View
```html
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Audit Trail</h3>
    </div>
    
    <!-- Filters -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">User</label>
                <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">All Users</option>
                    <option value="1">John Doe</option>
                    <option value="2">Jane Smith</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Action</label>
                <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">All Actions</option>
                    <option value="created">Created</option>
                    <option value="updated">Updated</option>
                    <option value="deleted">Deleted</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Resource</label>
                <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">All Resources</option>
                    <option value="Order">Orders</option>
                    <option value="Customer">Customers</option>
                    <option value="Product">Products</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Date Range</label>
                <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
        </div>
    </div>
    
    <!-- Audit Trail Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        User
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Action
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Resource
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Changes
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        IP Address
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Timestamp
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        John Doe
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Created
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        Order #ORD2024010001
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <button class="text-blue-600 hover:text-blue-800">View Changes</button>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        192.168.1.100
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        2024-01-15 10:30:00
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
```

### Audit Trail Detail View
```html
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Audit Trail Details</h3>
    </div>
    
    <div class="px-6 py-4 space-y-6">
        <!-- Basic Information -->
        <div>
            <h4 class="text-md font-medium text-gray-900 mb-3">Basic Information</h4>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">User</dt>
                    <dd class="text-sm text-gray-900">John Doe</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Action</dt>
                    <dd class="text-sm text-gray-900">Updated</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Resource</dt>
                    <dd class="text-sm text-gray-900">Order #ORD2024010001</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Timestamp</dt>
                    <dd class="text-sm text-gray-900">2024-01-15 10:30:00</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">IP Address</dt>
                    <dd class="text-sm text-gray-900">192.168.1.100</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">User Agent</dt>
                    <dd class="text-sm text-gray-900">Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36</dd>
                </div>
            </dl>
        </div>
        
        <!-- Changes -->
        <div>
            <h4 class="text-md font-medium text-gray-900 mb-3">Changes</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Old Values -->
                <div>
                    <h5 class="text-sm font-medium text-gray-700 mb-2">Before</h5>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <pre class="text-sm text-red-800">{{ json_encode($auditTrail->old_values, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
                
                <!-- New Values -->
                <div>
                    <h5 class="text-sm font-medium text-gray-700 mb-2">After</h5>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <pre class="text-sm text-green-800">{{ json_encode($auditTrail->new_values, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
```

## Audit Trail API

### Get Audit Trail
```http
GET /api/v1/audit-trail
```

**Query Parameters:**
- `user_id` (int): Filter by user
- `action` (string): Filter by action
- `resource_type` (string): Filter by resource type
- `resource_id` (int): Filter by resource ID
- `date_from` (date): Filter from date
- `date_to` (date): Filter to date
- `page` (int): Page number
- `per_page` (int): Items per page

**Response:**
```json
{
  "success": true,
  "message": "Audit trail retrieved successfully",
  "data": [
    {
      "id": 1,
      "uuid": "550e8400-e29b-41d4-a716-446655440000",
      "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
      },
      "action": "updated",
      "resource_type": "Order",
      "resource_id": 1,
      "resource_uuid": "550e8400-e29b-41d4-a716-446655440001",
      "old_values": {
        "order_status": "pending",
        "total_amount": "1000.00"
      },
      "new_values": {
        "order_status": "confirmed",
        "total_amount": "1000.00"
      },
      "ip_address": "192.168.1.100",
      "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36",
      "created_at": "2024-01-15T10:30:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7
  }
}
```

### Get Audit Trail Details
```http
GET /api/v1/audit-trail/{uuid}
```

**Response:**
```json
{
  "success": true,
  "message": "Audit trail details retrieved successfully",
  "data": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "action": "updated",
    "resource_type": "Order",
    "resource_id": 1,
    "resource_uuid": "550e8400-e29b-41d4-a716-446655440001",
    "old_values": {
      "order_status": "pending",
      "total_amount": "1000.00"
    },
    "new_values": {
      "order_status": "confirmed",
      "total_amount": "1000.00"
    },
    "ip_address": "192.168.1.100",
    "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36",
    "session_id": "abc123def456",
    "created_at": "2024-01-15T10:30:00Z"
  }
}
```

## Compliance and Security

### Data Retention
- **Audit Logs**: Retain for 7 years
- **Login History**: Retain for 2 years
- **System Events**: Retain for 1 year
- **Automated Archival**: Move old logs to cold storage
- **Secure Deletion**: Permanently delete expired logs

### Access Control
- **Admin Only**: Audit trail access restricted to administrators
- **Read-Only**: Audit logs cannot be modified
- **Encryption**: Sensitive data encrypted in audit logs
- **Audit Access**: Track who accesses audit logs

### Compliance Requirements
- **GDPR**: Right to be forgotten implementation
- **SOX**: Financial data audit requirements
- **HIPAA**: Healthcare data protection (if applicable)
- **PCI DSS**: Payment card data security
- **ISO 27001**: Information security management

## Performance Optimization

### Database Optimization
```sql
-- Partition audit trail table by date
ALTER TABLE audit_trail PARTITION BY RANGE (YEAR(created_at)) (
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p2026 VALUES LESS THAN (2027),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);

-- Add indexes for performance
CREATE INDEX idx_audit_trail_user_action ON audit_trail(user_id, action);
CREATE INDEX idx_audit_trail_resource_created ON audit_trail(resource_type, resource_id, created_at);
CREATE INDEX idx_audit_trail_created_at ON audit_trail(created_at);
```

### Caching Strategy
```php
// Cache frequently accessed audit data
$auditSummary = Cache::remember('audit.summary', 3600, function () {
    return [
        'total_actions' => AuditTrail::count(),
        'recent_actions' => AuditTrail::latest()->take(10)->get(),
        'top_users' => AuditTrail::selectRaw('user_id, COUNT(*) as action_count')
            ->with('user')
            ->groupBy('user_id')
            ->orderBy('action_count', 'desc')
            ->take(5)
            ->get()
    ];
});
```

### Background Processing
```php
// Queue audit trail creation for performance
class LogAuditTrail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public function handle()
    {
        AuditTrail::create($this->auditData);
    }
}

// Dispatch audit trail logging
LogAuditTrail::dispatch($auditData);
```

This comprehensive audit trail system ensures complete tracking of all system activities while maintaining performance and compliance requirements.
