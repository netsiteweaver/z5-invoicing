# User Management System

## Overview
The User Management system handles comprehensive user authentication, authorization, role-based access control, and user profile management. It provides secure access control with granular permissions and audit trails.

## Core Features

### User Management
- **User Creation**: Add new users with comprehensive details
- **User Editing**: Modify existing user information
- **User Deletion**: Soft delete users with audit trail
- **User Activation/Deactivation**: Enable/disable user accounts
- **Password Management**: Secure password handling and reset
- **Profile Management**: User profile and preferences

### Authentication System
- **Login/Logout**: Secure user authentication
- **Session Management**: Secure session handling
- **Password Reset**: Secure password reset functionality
- **Remember Me**: Optional persistent login
- **Account Lockout**: Protection against brute force attacks

### Authorization System
- **Role-based Access Control**: Three user levels (Normal, Admin, Root)
- **Permission Management**: Granular permissions for each feature
- **Menu Access Control**: Dynamic menu based on permissions
- **Resource Protection**: Protect sensitive operations and data

### User Roles and Permissions
- **Normal Users**: Basic access to assigned features
- **Administrators**: Extended access to most features
- **Root Users**: Full system access and user management
- **Custom Permissions**: Granular permission assignment per user

## Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    name VARCHAR(50) NOT NULL,
    job_title VARCHAR(50) NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    username VARCHAR(30) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_level ENUM('Normal','Admin','Root') DEFAULT 'Normal',
    last_login DATETIME NULL,
    ip VARCHAR(45) NULL,
    photo VARCHAR(100) NULL,
    store_id INT NULL,
    landing_page VARCHAR(100) DEFAULT 'dashboard',
    token VARCHAR(100) NULL,
    token_valid_until DATETIME NULL,
    created_by INT NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_by INT NULL,
    updated_date DATETIME NULL,
    status TINYINT DEFAULT 1 COMMENT '1-Active,2-Inactive,0-deleted',
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);
```

### Permissions Table
```sql
CREATE TABLE permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    menu_id INT NOT NULL,
    create TINYINT DEFAULT 0,
    read TINYINT DEFAULT 0,
    update TINYINT DEFAULT 0,
    delete TINYINT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (menu_id) REFERENCES menu(id)
);
```

### Menu Table
```sql
CREATE TABLE menu (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type ENUM('menu','section','divider') DEFAULT 'menu',
    nom VARCHAR(30) NULL,
    controller VARCHAR(30) NULL,
    action VARCHAR(30) NULL,
    color VARCHAR(7) DEFAULT '#FFFFFF',
    url VARCHAR(50) NULL,
    class VARCHAR(50) NULL,
    display_order INT DEFAULT 50,
    parent_menu INT NULL,
    visible TINYINT DEFAULT 1,
    Normal TINYINT DEFAULT 0,
    Admin TINYINT DEFAULT 0,
    Root TINYINT DEFAULT 1,
    module INT NOT NULL,
    status TINYINT DEFAULT 1,
    backoffice TINYINT DEFAULT 0,
    FOREIGN KEY (parent_menu) REFERENCES menu(id)
);
```

### Login History Table
```sql
CREATE TABLE login_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NULL,
    user_id INT NULL,
    datetime DATETIME NOT NULL,
    result ENUM('SUCCESS','FAILED','OTHER') NOT NULL,
    ip VARCHAR(45) NOT NULL,
    result_other TEXT NULL,
    os VARCHAR(100) NULL,
    browser VARCHAR(100) NULL,
    store_id INT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Audit Trail Table
```sql
CREATE TABLE audit_trail (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(40) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    resource_type VARCHAR(50) NOT NULL,
    resource_id INT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## API Endpoints

### User Management
- `GET /api/users` - List all users with filtering
- `POST /api/users` - Create new user
- `GET /api/users/{uuid}` - Get user details
- `PUT /api/users/{uuid}` - Update user
- `DELETE /api/users/{uuid}` - Soft delete user
- `PATCH /api/users/{uuid}/status` - Update user status
- `PATCH /api/users/{uuid}/password` - Update user password

### Authentication
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `POST /api/auth/refresh` - Refresh authentication token
- `POST /api/auth/forgot-password` - Request password reset
- `POST /api/auth/reset-password` - Reset password with token

### Permissions
- `GET /api/users/{uuid}/permissions` - Get user permissions
- `PUT /api/users/{uuid}/permissions` - Update user permissions
- `GET /api/menu` - Get menu structure with permissions
- `GET /api/permissions/check` - Check user permission for resource

### User Profile
- `GET /api/profile` - Get current user profile
- `PUT /api/profile` - Update current user profile
- `POST /api/profile/avatar` - Upload user avatar
- `GET /api/profile/activity` - Get user activity history

## Business Logic

### User Creation
```php
public function createUser(array $data): User
{
    // Validate email and username uniqueness
    if (User::where('email', $data['email'])->exists()) {
        throw new ValidationException('Email already exists');
    }
    
    if (User::where('username', $data['username'])->exists()) {
        throw new ValidationException('Username already exists');
    }
    
    $user = User::create([
        'uuid' => Str::uuid(),
        'name' => $data['name'],
        'job_title' => $data['job_title'],
        'email' => $data['email'],
        'username' => $data['username'],
        'password' => Hash::make($data['password']),
        'user_level' => $data['user_level'] ?? 'Normal',
        'landing_page' => $data['landing_page'] ?? 'dashboard',
        'created_by' => auth()->id(),
        'status' => 1
    ]);
    
    // Assign default permissions based on user level
    $this->assignDefaultPermissions($user);
    
    // Send welcome email
    $this->sendWelcomeEmail($user, $data['password']);
    
    return $user;
}
```

### User Authentication
```php
public function authenticate(array $credentials): array
{
    // Validate credentials
    if (!Auth::attempt($credentials)) {
        $this->logLoginAttempt($credentials['email'], 'FAILED', request()->ip());
        throw new AuthenticationException('Invalid credentials');
    }
    
    $user = Auth::user();
    
    // Check if user is active
    if ($user->status !== 1) {
        Auth::logout();
        $this->logLoginAttempt($user->email, 'OTHER', request()->ip(), 'Account inactive');
        throw new AuthenticationException('Account is inactive');
    }
    
    // Update last login
    $user->update([
        'last_login' => now(),
        'ip' => request()->ip()
    ]);
    
    // Log successful login
    $this->logLoginAttempt($user->email, 'SUCCESS', request()->ip());
    
    // Generate token for API access
    $token = $user->createToken('auth-token')->plainTextToken;
    
    return [
        'user' => $user,
        'token' => $token,
        'permissions' => $this->getUserPermissions($user)
    ];
}
```

### Permission Checking
```php
public function checkPermission(User $user, string $resource, string $action): bool
{
    // Root users have all permissions
    if ($user->user_level === 'Root') {
        return true;
    }
    
    // Get menu item for resource
    $menu = Menu::where('controller', $resource)->first();
    if (!$menu) {
        return false;
    }
    
    // Check user level access
    if ($user->user_level === 'Normal' && !$menu->Normal) {
        return false;
    }
    if ($user->user_level === 'Admin' && !$menu->Admin) {
        return false;
    }
    
    // Check specific permissions
    $permission = Permission::where('user_id', $user->id)
        ->where('menu_id', $menu->id)
        ->first();
    
    if (!$permission) {
        return false;
    }
    
    return match($action) {
        'create' => $permission->create,
        'read' => $permission->read,
        'update' => $permission->update,
        'delete' => $permission->delete,
        default => false
    };
}
```

### Password Reset
```php
public function requestPasswordReset(string $email): void
{
    $user = User::where('email', $email)->first();
    if (!$user) {
        throw new ValidationException('Email not found');
    }
    
    // Generate reset token
    $token = Str::random(64);
    $user->update([
        'token' => $token,
        'token_valid_until' => now()->addHours(24)
    ]);
    
    // Send reset email
    Mail::to($user->email)->send(new PasswordResetMail($user, $token));
}
```

### Audit Logging
```php
public function logAuditTrail(User $user, string $action, string $resourceType, int $resourceId = null, array $oldValues = null, array $newValues = null): void
{
    AuditTrail::create([
        'uuid' => Str::uuid(),
        'user_id' => $user->id,
        'action' => $action,
        'resource_type' => $resourceType,
        'resource_id' => $resourceId,
        'old_values' => $oldValues ? json_encode($oldValues) : null,
        'new_values' => $newValues ? json_encode($newValues) : null,
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent()
    ]);
}
```

## User Interface Components

### User List View
- **Data Table**: Sortable, filterable user listing
- **Status Filters**: Quick filter by user status
- **Role Filters**: Quick filter by user level
- **Search**: Search by name, email, username
- **Actions**: View, Edit, Delete, Activate/Deactivate buttons
- **Pagination**: Efficient handling of large user databases

### User Form
- **Basic Information**: Name, job title, email, username
- **Authentication**: Password, user level, landing page
- **Profile**: Photo upload, preferences
- **Permissions**: Permission assignment interface
- **Validation**: Real-time validation feedback

### User Detail View
- **User Information**: Complete user details
- **Permission Matrix**: Visual permission assignment
- **Login History**: Recent login attempts and history
- **Activity Log**: User activity and audit trail
- **Actions**: Edit, Delete, Reset Password, Manage Permissions

### Permission Management
- **Permission Matrix**: Grid view of all permissions
- **Role Templates**: Pre-defined permission sets
- **Bulk Assignment**: Assign permissions to multiple users
- **Permission Inheritance**: Inherit permissions from roles

### User Profile
- **Personal Information**: Name, job title, contact details
- **Account Settings**: Username, email, password
- **Preferences**: Landing page, display preferences
- **Security**: Password change, two-factor authentication
- **Activity**: Personal activity history

## Integration Points

### Authentication Middleware
- **Route Protection**: Protect routes based on permissions
- **API Authentication**: Secure API endpoints
- **Session Management**: Handle user sessions
- **Token Management**: Manage authentication tokens

### Menu System
- **Dynamic Menus**: Generate menus based on permissions
- **Navigation**: Provide navigation based on user access
- **Breadcrumbs**: Generate breadcrumbs for navigation
- **Access Control**: Hide/show menu items based on permissions

### Audit System
- **Activity Logging**: Log all user activities
- **Change Tracking**: Track all data changes
- **Security Monitoring**: Monitor security events
- **Compliance**: Support compliance requirements

### Email System
- **Welcome Emails**: Send welcome emails to new users
- **Password Reset**: Send password reset emails
- **Account Notifications**: Send account status notifications
- **Security Alerts**: Send security-related notifications

## Business Rules

### User Creation Rules
- Email must be unique and valid
- Username must be unique
- Password must meet security requirements
- User level must be valid
- Created by must be authorized

### User Update Rules
- Email uniqueness must be maintained
- Username uniqueness must be maintained
- Password changes require current password
- Status changes should validate business rules
- Update timestamps must be maintained

### User Deletion Rules
- Users cannot delete themselves
- Root users cannot be deleted
- Users with active sessions cannot be deleted
- Soft delete only (status = 0)
- Maintain referential integrity

### Permission Rules
- Users can only assign permissions they have
- Root users have all permissions
- Permission changes should be logged
- Default permissions should be assigned based on user level

## Security Considerations

### Authentication Security
- **Password Hashing**: Use secure password hashing
- **Session Security**: Secure session management
- **Token Security**: Secure token generation and validation
- **Brute Force Protection**: Protection against brute force attacks

### Authorization Security
- **Permission Validation**: Validate all permissions
- **Resource Protection**: Protect sensitive resources
- **Access Control**: Implement proper access control
- **Privilege Escalation**: Prevent privilege escalation

### Data Protection
- **Input Validation**: Validate all user input
- **SQL Injection**: Prevent SQL injection attacks
- **XSS Protection**: Prevent cross-site scripting
- **CSRF Protection**: Prevent cross-site request forgery

### Audit and Monitoring
- **Activity Logging**: Log all user activities
- **Security Events**: Log security-related events
- **Access Monitoring**: Monitor user access patterns
- **Anomaly Detection**: Detect suspicious activities

## Performance Considerations

### Database Optimization
- **Indexing**: Proper indexes on frequently queried fields
- **Pagination**: Efficient pagination for large user lists
- **Caching**: Cache frequently accessed user data

### Query Optimization
- **Eager Loading**: Load related data efficiently
- **Selective Loading**: Only load necessary data
- **Search Optimization**: Optimized search queries

### Session Management
- **Session Storage**: Efficient session storage
- **Token Management**: Efficient token management
- **Cache Optimization**: Optimize authentication cache

## Testing Requirements

### Unit Tests
- User creation and validation
- Authentication functionality
- Permission checking
- Password reset functionality
- Audit logging

### Integration Tests
- Authentication middleware
- Permission system integration
- Menu system integration
- Email system integration

### Security Tests
- Authentication security
- Authorization security
- Input validation
- SQL injection prevention
- XSS prevention

### User Acceptance Tests
- User creation workflow
- Authentication workflow
- Permission management
- User profile management
- Security features

## Migration Notes

### From CodeIgniter
- **Model Structure**: Convert CI models to Laravel Eloquent models
- **Controller Logic**: Adapt CI controllers to Laravel controllers
- **Authentication**: Replace CI authentication with Laravel Sanctum
- **Validation**: Replace CI validation with Laravel validation

### Data Migration
- **User Data**: Migrate existing users with UUID preservation
- **Permissions**: Migrate user permissions and menu structure
- **Login History**: Migrate login history if available
- **Audit Trail**: Migrate audit trail if available
- **Password Security**: Ensure password security during migration
