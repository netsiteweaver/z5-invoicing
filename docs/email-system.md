# Email System

## Overview
This document defines the email system for the Z5 Distribution System. It includes automated notifications, email templates, queue management, and delivery tracking for all business communications.

## Email System Design Principles

### Core Principles
- **Reliability**: Ensure email delivery with retry mechanisms
- **Performance**: Use queue system for non-blocking email sending
- **Templates**: Consistent, professional email templates
- **Tracking**: Monitor email delivery and engagement
- **Compliance**: Follow email marketing best practices
- **Scalability**: Handle high volume email sending

### Email Types
- **Transactional**: Order confirmations, payment receipts, status updates
- **Notification**: System alerts, low stock warnings, overdue payments
- **Marketing**: Promotional emails, newsletters, product updates
- **Administrative**: User account updates, password resets, system notifications

## Email System Implementation

### Database Schema

#### Email Queue Table
```sql
CREATE TABLE email_queue (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    to_email VARCHAR(255) NOT NULL,
    to_name VARCHAR(255) NULL,
    from_email VARCHAR(255) NOT NULL,
    from_name VARCHAR(255) NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    template VARCHAR(100) NULL,
    template_data JSON NULL,
    priority ENUM('low','normal','high','urgent') DEFAULT 'normal',
    status ENUM('pending','processing','sent','failed','cancelled') DEFAULT 'pending',
    attempts INT DEFAULT 0,
    max_attempts INT DEFAULT 3,
    scheduled_at TIMESTAMP NULL,
    sent_at TIMESTAMP NULL,
    failed_at TIMESTAMP NULL,
    error_message TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_email_queue_status (status),
    INDEX idx_email_queue_priority (priority),
    INDEX idx_email_queue_scheduled (scheduled_at),
    INDEX idx_email_queue_created (created_at)
);
```

#### Email Templates Table
```sql
CREATE TABLE email_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    variables JSON NULL,
    type ENUM('transactional','notification','marketing','administrative') DEFAULT 'transactional',
    is_active TINYINT DEFAULT 1,
    created_by BIGINT UNSIGNED NOT NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    
    INDEX idx_email_templates_name (name),
    INDEX idx_email_templates_type (type),
    INDEX idx_email_templates_active (is_active)
);
```

#### Email History Table
```sql
CREATE TABLE email_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    email_queue_id BIGINT UNSIGNED NOT NULL,
    action ENUM('sent','delivered','opened','clicked','bounced','complained','unsubscribed') NOT NULL,
    details JSON NULL,
    created_at TIMESTAMP NULL,
    
    FOREIGN KEY (email_queue_id) REFERENCES email_queue(id) ON DELETE CASCADE,
    
    INDEX idx_email_history_queue (email_queue_id),
    INDEX idx_email_history_action (action),
    INDEX idx_email_history_created (created_at)
);
```

### Laravel Implementation

#### Email Queue Model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailQueue extends Model
{
    protected $fillable = [
        'uuid',
        'to_email',
        'to_name',
        'from_email',
        'from_name',
        'subject',
        'body',
        'template',
        'template_data',
        'priority',
        'status',
        'attempts',
        'max_attempts',
        'scheduled_at',
        'sent_at',
        'failed_at',
        'error_message'
    ];
    
    protected $casts = [
        'template_data' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime'
    ];
    
    public function history(): HasMany
    {
        return $this->hasMany(EmailHistory::class);
    }
    
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
    
    public function isSent(): bool
    {
        return $this->status === 'sent';
    }
    
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
    
    public function canRetry(): bool
    {
        return $this->attempts < $this->max_attempts && $this->status === 'failed';
    }
}
```

#### Email Template Model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTemplate extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'subject',
        'body',
        'variables',
        'type',
        'is_active'
    ];
    
    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean'
    ];
    
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    public function render(array $data = []): array
    {
        $subject = $this->subject;
        $body = $this->body;
        
        foreach ($data as $key => $value) {
            $subject = str_replace("{{$key}}", $value, $subject);
            $body = str_replace("{{$key}}", $value, $body);
        }
        
        return [
            'subject' => $subject,
            'body' => $body
        ];
    }
}
```

#### Email Service
```php
<?php

namespace App\Services;

use App\Models\EmailQueue;
use App\Models\EmailTemplate;
use App\Models\EmailHistory;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EmailService
{
    public function queueEmail(
        string $toEmail,
        string $subject,
        string $body,
        array $options = []
    ): EmailQueue {
        return EmailQueue::create([
            'uuid' => Str::uuid(),
            'to_email' => $toEmail,
            'to_name' => $options['to_name'] ?? null,
            'from_email' => $options['from_email'] ?? config('mail.from.address'),
            'from_name' => $options['from_name'] ?? config('mail.from.name'),
            'subject' => $subject,
            'body' => $body,
            'template' => $options['template'] ?? null,
            'template_data' => $options['template_data'] ?? null,
            'priority' => $options['priority'] ?? 'normal',
            'scheduled_at' => $options['scheduled_at'] ?? now(),
            'max_attempts' => $options['max_attempts'] ?? 3
        ]);
    }
    
    public function sendFromTemplate(
        string $templateName,
        string $toEmail,
        array $data = [],
        array $options = []
    ): EmailQueue {
        $template = EmailTemplate::where('name', $templateName)
            ->where('is_active', true)
            ->firstOrFail();
        
        $rendered = $template->render($data);
        
        return $this->queueEmail(
            $toEmail,
            $rendered['subject'],
            $rendered['body'],
            array_merge($options, [
                'template' => $templateName,
                'template_data' => $data
            ])
        );
    }
    
    public function sendOrderConfirmation(Order $order): EmailQueue
    {
        $data = [
            'order_number' => $order->order_number,
            'customer_name' => $order->customer->company_name,
            'order_date' => $order->order_date->format('Y-m-d'),
            'total_amount' => number_format($order->total_amount, 2),
            'currency' => $order->currency,
            'items' => $order->items->map(function ($item) {
                return [
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'unit_price' => number_format($item->unit_price, 2),
                    'line_total' => number_format($item->line_total, 2)
                ];
            })->toArray()
        ];
        
        return $this->sendFromTemplate(
            'order_confirmation',
            $order->customer->email,
            $data,
            ['priority' => 'high']
        );
    }
    
    public function sendPaymentReceipt(Payment $payment): EmailQueue
    {
        $data = [
            'payment_number' => $payment->payment_number,
            'payment_date' => $payment->payment_date->format('Y-m-d'),
            'amount' => number_format($payment->amount, 2),
            'payment_method' => $payment->payment_method,
            'reference_number' => $payment->reference_number,
            'customer_name' => $payment->customer->company_name
        ];
        
        return $this->sendFromTemplate(
            'payment_receipt',
            $payment->customer->email,
            $data,
            ['priority' => 'high']
        );
    }
    
    public function sendLowStockAlert(Product $product, int $currentStock): EmailQueue
    {
        $data = [
            'product_name' => $product->name,
            'stockref' => $product->stockref,
            'current_stock' => $currentStock,
            'reorder_point' => $product->reorder_point ?? 0,
            'reorder_quantity' => $product->reorder_quantity ?? 0
        ];
        
        return $this->sendFromTemplate(
            'low_stock_alert',
            config('mail.admin_email'),
            $data,
            ['priority' => 'urgent']
        );
    }
    
    public function processQueue(): void
    {
        $emails = EmailQueue::where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->limit(50)
            ->get();
        
        foreach ($emails as $email) {
            $this->processEmail($email);
        }
    }
    
    private function processEmail(EmailQueue $email): void
    {
        try {
            $email->update(['status' => 'processing', 'attempts' => $email->attempts + 1]);
            
            Mail::raw($email->body, function ($message) use ($email) {
                $message->to($email->to_email, $email->to_name)
                    ->subject($email->subject)
                    ->from($email->from_email, $email->from_name);
            });
            
            $email->update([
                'status' => 'sent',
                'sent_at' => now()
            ]);
            
            $this->logEmailHistory($email, 'sent');
            
        } catch (\Exception $e) {
            Log::error('Email sending failed', [
                'email_id' => $email->id,
                'error' => $e->getMessage()
            ]);
            
            if ($email->canRetry()) {
                $email->update(['status' => 'pending']);
            } else {
                $email->update([
                    'status' => 'failed',
                    'failed_at' => now(),
                    'error_message' => $e->getMessage()
                ]);
            }
            
            $this->logEmailHistory($email, 'failed', ['error' => $e->getMessage()]);
        }
    }
    
    private function logEmailHistory(EmailQueue $email, string $action, array $details = []): void
    {
        EmailHistory::create([
            'uuid' => Str::uuid(),
            'email_queue_id' => $email->id,
            'action' => $action,
            'details' => $details
        ]);
    }
}
```

### Email Templates

#### Order Confirmation Template
```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #2563eb; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9fafb; }
        .order-details { background-color: white; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .item-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
        .total { font-weight: bold; font-size: 18px; color: #2563eb; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Confirmation</h1>
            <p>Thank you for your order!</p>
        </div>
        
        <div class="content">
            <p>Dear {{customer_name}},</p>
            
            <p>We have received your order and are processing it. Here are the details:</p>
            
            <div class="order-details">
                <h3>Order Information</h3>
                <p><strong>Order Number:</strong> {{order_number}}</p>
                <p><strong>Order Date:</strong> {{order_date}}</p>
                <p><strong>Total Amount:</strong> {{currency}} {{total_amount}}</p>
                
                <h4>Order Items</h4>
                @foreach($items as $item)
                <div class="item-row">
                    <span>{{item.product_name}} (Qty: {{item.quantity}})</span>
                    <span>{{currency}} {{item.line_total}}</span>
                </div>
                @endforeach
                
                <div class="item-row total">
                    <span>Total</span>
                    <span>{{currency}} {{total_amount}}</span>
                </div>
            </div>
            
            <p>We will send you another email when your order is shipped.</p>
            
            <p>If you have any questions, please contact us.</p>
            
            <p>Best regards,<br>Z5 Distribution Team</p>
        </div>
        
        <div class="footer">
            <p>This is an automated email. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
```

#### Payment Receipt Template
```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #059669; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9fafb; }
        .payment-details { background-color: white; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payment Receipt</h1>
            <p>Payment received successfully!</p>
        </div>
        
        <div class="content">
            <p>Dear {{customer_name}},</p>
            
            <p>We have received your payment. Here are the details:</p>
            
            <div class="payment-details">
                <h3>Payment Information</h3>
                <p><strong>Payment Number:</strong> {{payment_number}}</p>
                <p><strong>Payment Date:</strong> {{payment_date}}</p>
                <p><strong>Amount:</strong> {{currency}} {{amount}}</p>
                <p><strong>Payment Method:</strong> {{payment_method}}</p>
                @if($reference_number)
                <p><strong>Reference Number:</strong> {{reference_number}}</p>
                @endif
            </div>
            
            <p>Thank you for your payment!</p>
            
            <p>Best regards,<br>Z5 Distribution Team</p>
        </div>
        
        <div class="footer">
            <p>This is an automated email. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
```

#### Low Stock Alert Template
```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Low Stock Alert</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #dc2626; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9fafb; }
        .alert-details { background-color: white; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #dc2626; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Low Stock Alert</h1>
            <p>Immediate attention required!</p>
        </div>
        
        <div class="content">
            <p>Dear Admin,</p>
            
            <p>The following product is running low on stock:</p>
            
            <div class="alert-details">
                <h3>Product Information</h3>
                <p><strong>Product Name:</strong> {{product_name}}</p>
                <p><strong>Stock Reference:</strong> {{stockref}}</p>
                <p><strong>Current Stock:</strong> {{current_stock}} units</p>
                <p><strong>Reorder Point:</strong> {{reorder_point}} units</p>
                <p><strong>Suggested Reorder Quantity:</strong> {{reorder_quantity}} units</p>
            </div>
            
            <p>Please consider reordering this product to avoid stockouts.</p>
            
            <p>Best regards,<br>Z5 Distribution System</p>
        </div>
        
        <div class="footer">
            <p>This is an automated alert. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
```

## Email Queue Management

### Queue Processing Command
```php
<?php

namespace App\Console\Commands;

use App\Services\EmailService;
use Illuminate\Console\Command;

class ProcessEmailQueue extends Command
{
    protected $signature = 'email:process {--limit=50 : Number of emails to process}';
    protected $description = 'Process pending emails in the queue';
    
    public function handle(EmailService $emailService)
    {
        $this->info('Processing email queue...');
        
        $emailService->processQueue();
        
        $this->info('Email queue processing completed.');
    }
}
```

### Scheduled Task
```php
// In app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('email:process')
        ->everyMinute()
        ->withoutOverlapping();
}
```

### Email Queue Interface
```html
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Email Queue</h3>
    </div>
    
    <!-- Filters -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="sent">Sent</option>
                    <option value="failed">Failed</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Priority</label>
                <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">All Priorities</option>
                    <option value="low">Low</option>
                    <option value="normal">Normal</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Date Range</label>
                <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
        </div>
    </div>
    
    <!-- Email Queue Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        To
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Subject
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Priority
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Attempts
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Created
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        customer@example.com
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        Order Confirmation - ORD2024010001
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Sent
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            High
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        1
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

## Email API Endpoints

### Send Email
```http
POST /api/v1/emails/send
```

**Request Body:**
```json
{
  "to_email": "customer@example.com",
  "to_name": "John Doe",
  "subject": "Order Confirmation",
  "body": "Thank you for your order!",
  "template": "order_confirmation",
  "template_data": {
    "order_number": "ORD2024010001",
    "customer_name": "John Doe"
  },
  "priority": "high"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Email queued successfully",
  "data": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "to_email": "customer@example.com",
    "subject": "Order Confirmation",
    "status": "pending",
    "priority": "high"
  }
}
```

### Get Email Queue
```http
GET /api/v1/emails/queue
```

**Query Parameters:**
- `status` (string): Filter by status
- `priority` (string): Filter by priority
- `page` (int): Page number
- `per_page` (int): Items per page

### Get Email History
```http
GET /api/v1/emails/history
```

**Response:**
```json
{
  "success": true,
  "message": "Email history retrieved successfully",
  "data": [
    {
      "id": 1,
      "email_queue_id": 1,
      "action": "sent",
      "details": {},
      "created_at": "2024-01-15T10:30:00Z"
    }
  ]
}
```

## Email Configuration

### Environment Variables
```bash
# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@z5distribution.com
MAIL_FROM_NAME="Z5 Distribution"

# Queue Configuration
QUEUE_CONNECTION=database
```

### Mail Configuration
```php
// In config/mail.php
'default' => env('MAIL_MAILER', 'smtp'),

'mailers' => [
    'smtp' => [
        'transport' => 'smtp',
        'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
        'port' => env('MAIL_PORT', 587),
        'encryption' => env('MAIL_ENCRYPTION', 'tls'),
        'username' => env('MAIL_USERNAME'),
        'password' => env('MAIL_PASSWORD'),
        'timeout' => null,
        'auth_mode' => null,
    ],
],

'from' => [
    'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
    'name' => env('MAIL_FROM_NAME', 'Example'),
],
```

This comprehensive email system provides reliable, scalable, and professional email communication for the Z5 Distribution System with proper queue management, template system, and delivery tracking.
