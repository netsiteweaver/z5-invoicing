# Technology Stack

## Overview
This document outlines the complete technology stack for migrating the Z5 Distribution System from CodeIgniter to Laravel with TailwindCSS. It provides detailed specifications for all technologies, frameworks, and tools used in the modern implementation.

## Backend Technologies

### Laravel Framework
- **Version**: Laravel 10.x (LTS)
- **PHP Version**: PHP 8.1+
- **Purpose**: Modern PHP framework for robust web applications
- **Key Features**:
  - Eloquent ORM for database operations
  - Artisan CLI for development tasks
  - Blade templating engine
  - Built-in authentication and authorization
  - Comprehensive testing framework
  - Queue system for background jobs
  - Event system for decoupled architecture

### PHP Configuration
```php
// php.ini requirements
memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 50M
post_max_size = 50M
max_input_vars = 3000
date.timezone = UTC
```

### Database
- **Primary Database**: MySQL 8.0+
- **Alternative**: PostgreSQL 14+ (if preferred)
- **Connection Pooling**: Laravel's built-in connection management
- **Migrations**: Laravel migration system for database versioning
- **Seeding**: Database seeding for initial data

### Database Configuration
```php
// config/database.php
'mysql' => [
    'driver' => 'mysql',
    'url' => env('DATABASE_URL'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'z5_distribution'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => true,
    'engine' => null,
    'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
    ]) : [],
],
```

## Frontend Technologies

### TailwindCSS
- **Version**: TailwindCSS 3.x
- **Purpose**: Utility-first CSS framework
- **Configuration**: Custom design system integration
- **Plugins**: Forms, Typography, Aspect Ratio

### TailwindCSS Configuration
```javascript
// tailwind.config.js
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#eff6ff',
          100: '#dbeafe',
          200: '#bfdbfe',
          300: '#93c5fd',
          400: '#60a5fa',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
          800: '#1e40af',
          900: '#1e3a8a',
        },
        // ... other colors
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        mono: ['JetBrains Mono', 'monospace'],
      },
      spacing: {
        '18': '4.5rem',
        '88': '22rem',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
```

### JavaScript Framework
- **Alpine.js**: Lightweight JavaScript framework
- **Purpose**: Reactive components and interactivity
- **Integration**: Seamless integration with Laravel Blade
- **Features**: Two-way data binding, event handling, component system

### Alpine.js Configuration
```javascript
// resources/js/app.js
import Alpine from 'alpinejs'
import './bootstrap'

// Register Alpine components
Alpine.data('orderForm', () => ({
  items: [],
  customer: null,
  total: 0,
  
  addItem() {
    this.items.push({
      product_id: null,
      quantity: 1,
      unit_price: 0,
      line_total: 0
    })
  },
  
  removeItem(index) {
    this.items.splice(index, 1)
    this.calculateTotal()
  },
  
  calculateTotal() {
    this.total = this.items.reduce((sum, item) => sum + item.line_total, 0)
  }
}))

window.Alpine = Alpine
Alpine.start()
```

## UI Component Libraries

### Radix UI
- **Purpose**: Headless UI components for accessibility
- **Integration**: Custom styling with TailwindCSS
- **Components**: Dialog, Dropdown, Select, Checkbox, Radio

### Radix UI Implementation
```javascript
// resources/js/components/radix-components.js
import * as Dialog from '@radix-ui/react-dialog'
import * as Select from '@radix-ui/react-select'
import * as DropdownMenu from '@radix-ui/react-dropdown-menu'

// Custom styled components
export const Modal = ({ children, open, onOpenChange }) => (
  <Dialog.Root open={open} onOpenChange={onOpenChange}>
    <Dialog.Portal>
      <Dialog.Overlay className="fixed inset-0 bg-black/50" />
      <Dialog.Content className="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg p-6 max-w-md w-full">
        {children}
      </Dialog.Content>
    </Dialog.Portal>
  </Dialog.Root>
)
```

### Icon Library
- **Lucide Icons**: Modern icon library
- **Purpose**: Consistent iconography throughout the application
- **Integration**: SVG-based icons with TailwindCSS styling

## Authentication & Security

### Laravel Sanctum
- **Purpose**: API authentication and SPA authentication
- **Features**: Token-based authentication, CSRF protection
- **Configuration**: SPA and API token management

### Sanctum Configuration
```php
// config/sanctum.php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
    '%s%s',
    'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
    env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
))),

'guard' => ['web'],

'expiration' => null,

'middleware' => [
    'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
    'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
],
```

### Security Middleware
```php
// app/Http/Middleware/SecurityHeaders.php
class SecurityHeaders
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('Content-Security-Policy', "default-src 'self'");
        
        return $response;
    }
}
```

## File Storage & Media

### Laravel Storage
- **Local Storage**: Development and small deployments
- **Cloud Storage**: AWS S3, Google Cloud Storage
- **File Processing**: Image optimization and resizing

### Storage Configuration
```php
// config/filesystems.php
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app'),
    ],
    
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
    
    's3' => [
        'driver' => 's3',
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
        'url' => env('AWS_URL'),
        'endpoint' => env('AWS_ENDPOINT'),
    ],
],
```

### Image Processing
```php
// Image processing with Intervention Image
use Intervention\Image\Facades\Image;

class ImageService
{
    public function processUpload($file, $path, $sizes = [])
    {
        $image = Image::make($file);
        
        foreach ($sizes as $size) {
            $resized = $image->resize($size['width'], $size['height'], function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            
            $resized->save(storage_path("app/public/{$path}/{$size['name']}.jpg"));
        }
        
        return $path;
    }
}
```

## Email & Notifications

### Laravel Mail
- **SMTP Configuration**: Configurable SMTP settings
- **Queue Integration**: Background email processing
- **Template System**: Blade-based email templates

### Mail Configuration
```php
// config/mail.php
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
```

### Notification System
```php
// app/Notifications/OrderCreated.php
class OrderCreated extends Notification
{
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }
    
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Order Created')
            ->greeting('Hello!')
            ->line('A new order has been created.')
            ->action('View Order', url('/orders/'.$this->order->uuid))
            ->line('Thank you for using our application!');
    }
}
```

## Queue & Background Jobs

### Laravel Queue
- **Driver**: Redis (recommended) or Database
- **Purpose**: Background job processing
- **Features**: Job retries, failed job handling, job monitoring

### Queue Configuration
```php
// config/queue.php
'connections' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => env('REDIS_QUEUE', 'default'),
        'retry_after' => 90,
        'block_for' => null,
    ],
],
```

### Background Jobs
```php
// app/Jobs/ProcessOrder.php
class ProcessOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public function handle()
    {
        // Process order logic
        $this->order->process();
        
        // Send notifications
        $this->order->customer->notify(new OrderProcessed($this->order));
    }
}
```

## Caching & Performance

### Redis Cache
- **Purpose**: Application caching and session storage
- **Configuration**: Redis server with Laravel integration
- **Features**: Cache tags, cache invalidation, session management

### Cache Configuration
```php
// config/cache.php
'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',
        'lock_connection' => 'default',
    ],
],
```

### Performance Optimization
```php
// app/Http/Middleware/CacheResponse.php
class CacheResponse
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            $response->header('Cache-Control', 'public, max-age=3600');
        }
        
        return $response;
    }
}
```

## Development Tools

### Laravel Development Tools
- **Laravel Telescope**: Debugging and monitoring
- **Laravel Debugbar**: Development debugging
- **Laravel IDE Helper**: IDE autocompletion

### Telescope Configuration
```php
// config/telescope.php
'watchers' => [
    Watchers\CacheWatcher::class => env('TELESCOPE_CACHE_WATCHER', true),
    Watchers\CommandWatcher::class => env('TELESCOPE_COMMAND_WATCHER', true),
    Watchers\DumpWatcher::class => env('TELESCOPE_DUMP_WATCHER', true),
    Watchers\EventWatcher::class => env('TELESCOPE_EVENT_WATCHER', true),
    Watchers\ExceptionWatcher::class => env('TELESCOPE_EXCEPTION_WATCHER', true),
    Watchers\JobWatcher::class => env('TELESCOPE_JOB_WATCHER', true),
    Watchers\LogWatcher::class => env('TELESCOPE_LOG_WATCHER', true),
    Watchers\MailWatcher::class => env('TELESCOPE_MAIL_WATCHER', true),
    Watchers\ModelWatcher::class => env('TELESCOPE_MODEL_WATCHER', true),
    Watchers\NotificationWatcher::class => env('TELESCOPE_NOTIFICATION_WATCHER', true),
    Watchers\QueryWatcher::class => env('TELESCOPE_QUERY_WATCHER', true),
    Watchers\RedisWatcher::class => env('TELESCOPE_REDIS_WATCHER', true),
    Watchers\RequestWatcher::class => env('TELESCOPE_REQUEST_WATCHER', true),
    Watchers\GateWatcher::class => env('TELESCOPE_GATE_WATCHER', true),
    Watchers\ScheduleWatcher::class => env('TELESCOPE_SCHEDULE_WATCHER', true),
    Watchers\ViewWatcher::class => env('TELESCOPE_VIEW_WATCHER', true),
],
```

## Testing Framework

### PHPUnit Testing
- **Unit Tests**: Individual component testing
- **Feature Tests**: End-to-end functionality testing
- **Integration Tests**: Database and external service testing

### Test Configuration
```php
// phpunit.xml
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="./vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory suffix=".php">./app</directory>
        </include>
    </source>
</phpunit>
```

### Test Examples
```php
// tests/Feature/OrderTest.php
class OrderTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_can_create_order()
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        
        $response = $this->post('/api/orders', [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_price' => 100.00
                ]
            ]
        ]);
        
        $response->assertStatus(201);
        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id
        ]);
    }
}
```

## Deployment & DevOps

### Server Requirements
- **Web Server**: Nginx or Apache
- **PHP**: PHP 8.1+ with required extensions
- **Database**: MySQL 8.0+ or PostgreSQL 14+
- **Cache**: Redis 6.0+
- **Queue**: Redis or Database queue

### Docker Configuration
```dockerfile
# Dockerfile
FROM php:8.1-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/storage

EXPOSE 9000
CMD ["php-fpm"]
```

### Environment Configuration
```bash
# .env.example
APP_NAME="Z5 Distribution System"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=z5_distribution
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Monitoring & Logging

### Application Monitoring
- **Laravel Telescope**: Application debugging and monitoring
- **Laravel Horizon**: Queue monitoring and management
- **Custom Logging**: Application-specific logging

### Logging Configuration
```php
// config/logging.php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single'],
        'ignore_exceptions' => false,
    ],
    
    'single' => [
        'driver' => 'single',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'debug'),
    ],
    
    'daily' => [
        'driver' => 'daily',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'debug'),
        'days' => 14,
    ],
],
```

This comprehensive technology stack provides a modern, scalable, and maintainable foundation for the Z5 Distribution System. It leverages industry-standard tools and frameworks to ensure optimal performance, security, and developer experience.
