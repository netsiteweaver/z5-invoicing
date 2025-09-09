# Accessibility

## Overview
This document defines the accessibility requirements and implementation guidelines for the Z5 Distribution System. It ensures the application is usable by people with disabilities and complies with WCAG 2.1 AA standards.

## Accessibility Standards

### WCAG 2.1 Compliance
- **Level AA**: Meet WCAG 2.1 Level AA standards
- **Keyboard Navigation**: Full keyboard accessibility
- **Screen Reader Support**: Compatible with assistive technologies
- **Color Contrast**: Sufficient color contrast ratios
- **Focus Management**: Clear focus indicators
- **Alternative Text**: Descriptive alt text for images

### Legal Compliance
- **ADA Compliance**: Americans with Disabilities Act
- **Section 508**: Federal accessibility requirements
- **EN 301 549**: European accessibility standard
- **Local Regulations**: Country-specific accessibility laws

## Implementation Guidelines

### Semantic HTML
```html
<!-- Use proper heading hierarchy -->
<h1>Dashboard</h1>
<h2>Recent Orders</h2>
<h3>Order Details</h3>

<!-- Use semantic elements -->
<main>
    <section>
        <article>
            <header>
                <h2>Order Information</h2>
            </header>
            <div class="content">
                <!-- Order content -->
            </div>
            <footer>
                <!-- Order actions -->
            </footer>
        </article>
    </section>
</main>

<!-- Use proper form labels -->
<label for="customer-name">Customer Name</label>
<input type="text" id="customer-name" name="customer_name" required>

<!-- Use fieldset for grouped inputs -->
<fieldset>
    <legend>Payment Information</legend>
    <label for="payment-method">Payment Method</label>
    <select id="payment-method" name="payment_method">
        <option value="cash">Cash</option>
        <option value="bank_transfer">Bank Transfer</option>
    </select>
</fieldset>
```

### ARIA Labels and Roles
```html
<!-- Use ARIA labels for complex interactions -->
<button aria-label="Close dialog" aria-expanded="false" aria-controls="order-modal">
    <svg aria-hidden="true">
        <path d="M6 18L18 6M6 6l12 12"></path>
    </svg>
</button>

<!-- Use ARIA live regions for dynamic content -->
<div aria-live="polite" aria-atomic="true" id="status-message">
    <!-- Status updates will be announced -->
</div>

<!-- Use ARIA landmarks -->
<nav aria-label="Main navigation">
    <ul role="menubar">
        <li role="none">
            <a href="/orders" role="menuitem">Orders</a>
        </li>
    </ul>
</nav>

<!-- Use ARIA for form validation -->
<input type="email" 
       id="email" 
       name="email" 
       aria-describedby="email-error"
       aria-invalid="true">
<div id="email-error" role="alert">
    Please enter a valid email address
</div>
```

### Keyboard Navigation
```html
<!-- Ensure all interactive elements are keyboard accessible -->
<button tabindex="0" onkeydown="handleKeyDown(event)">
    Add Item
</button>

<!-- Use proper tab order -->
<div tabindex="0" role="button" onkeydown="handleKeyDown(event)">
    Clickable div
</div>

<!-- Skip links for navigation -->
<a href="#main-content" class="skip-link">
    Skip to main content
</a>

<!-- Focus management -->
<div id="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <h2 id="modal-title">Order Details</h2>
    <button aria-label="Close modal" onclick="closeModal()">×</button>
    <div tabindex="-1" id="modal-content">
        <!-- Modal content -->
    </div>
</div>
```

### Color and Contrast
```css
/* Ensure sufficient color contrast */
.text-primary {
    color: #1e40af; /* 4.5:1 contrast ratio */
}

.text-secondary {
    color: #6b7280; /* 4.5:1 contrast ratio */
}

.text-error {
    color: #dc2626; /* 4.5:1 contrast ratio */
}

/* Don't rely solely on color for information */
.status-success {
    color: #059669;
}

.status-success::before {
    content: "✓ ";
    color: #059669;
}

.status-error {
    color: #dc2626;
}

.status-error::before {
    content: "✗ ";
    color: #dc2626;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .button {
        border: 2px solid currentColor;
    }
}
```

### Focus Management
```css
/* Clear focus indicators */
.focus-visible {
    outline: 2px solid #2563eb;
    outline-offset: 2px;
}

/* Remove default focus for mouse users */
.focus-visible:not(:focus-visible) {
    outline: none;
}

/* Custom focus styles */
.button:focus-visible {
    outline: 2px solid #2563eb;
    outline-offset: 2px;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
}

/* Focus trap for modals */
.modal:focus-within {
    outline: none;
}
```

### Screen Reader Support
```html
<!-- Provide alternative text for images -->
<img src="product.jpg" 
     alt="Laptop computer with silver finish and 15.6 inch screen"
     loading="lazy">

<!-- Use aria-hidden for decorative images -->
<img src="decoration.svg" 
     alt="" 
     aria-hidden="true">

<!-- Provide context for icons -->
<button aria-label="Delete order">
    <svg aria-hidden="true">
        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
    </svg>
</button>

<!-- Use aria-describedby for additional context -->
<input type="text" 
       id="search" 
       aria-describedby="search-help"
       placeholder="Search orders...">
<div id="search-help">
    Search by order number, customer name, or product
</div>
```

## Component Accessibility

### Accessible Data Tables
```html
<table role="table" aria-label="Orders table">
    <caption>Recent orders with customer information and totals</caption>
    <thead>
        <tr role="row">
            <th scope="col" role="columnheader">Order Number</th>
            <th scope="col" role="columnheader">Customer</th>
            <th scope="col" role="columnheader">Date</th>
            <th scope="col" role="columnheader">Total</th>
            <th scope="col" role="columnheader">Status</th>
        </tr>
    </thead>
    <tbody>
        <tr role="row">
            <td role="cell" data-label="Order Number">ORD2024010001</td>
            <td role="cell" data-label="Customer">ABC Company Ltd</td>
            <td role="cell" data-label="Date">2024-01-15</td>
            <td role="cell" data-label="Total">Rs 1,250.00</td>
            <td role="cell" data-label="Status">
                <span class="status-badge" aria-label="Order status: Confirmed">
                    Confirmed
                </span>
            </td>
        </tr>
    </tbody>
</table>
```

### Accessible Forms
```html
<form novalidate>
    <fieldset>
        <legend>Customer Information</legend>
        
        <div class="form-group">
            <label for="company-name" class="required">
                Company Name
                <span class="sr-only">(required)</span>
            </label>
            <input type="text" 
                   id="company-name" 
                   name="company_name" 
                   required
                   aria-describedby="company-name-error"
                   aria-invalid="false">
            <div id="company-name-error" 
                 class="error-message" 
                 role="alert" 
                 aria-live="polite">
                <!-- Error messages -->
            </div>
        </div>
        
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" 
                   id="email" 
                   name="email"
                   aria-describedby="email-help">
            <div id="email-help" class="help-text">
                We'll use this email to send order confirmations
            </div>
        </div>
        
        <div class="form-group">
            <fieldset>
                <legend>Customer Type</legend>
                <div class="radio-group">
                    <input type="radio" 
                           id="business" 
                           name="customer_type" 
                           value="business"
                           checked>
                    <label for="business">Business</label>
                    
                    <input type="radio" 
                           id="individual" 
                           name="customer_type" 
                           value="individual">
                    <label for="individual">Individual</label>
                </div>
            </fieldset>
        </div>
    </fieldset>
    
    <div class="form-actions">
        <button type="submit" class="btn-primary">
            Save Customer
        </button>
        <button type="button" class="btn-secondary">
            Cancel
        </button>
    </div>
</form>
```

### Accessible Modals
```html
<div id="order-modal" 
     class="modal" 
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="modal-title"
     aria-describedby="modal-description"
     hidden>
    <div class="modal-content">
        <header class="modal-header">
            <h2 id="modal-title">Order Details</h2>
            <button type="button" 
                    class="close-button" 
                    aria-label="Close modal"
                    onclick="closeModal()">
                <svg aria-hidden="true">
                    <path d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </header>
        
        <div id="modal-description" class="modal-body">
            <!-- Modal content -->
        </div>
        
        <footer class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeModal()">
                Close
            </button>
            <button type="button" class="btn-primary">
                Save Changes
            </button>
        </footer>
    </div>
</div>
```

### Accessible Navigation
```html
<nav aria-label="Main navigation">
    <ul role="menubar">
        <li role="none">
            <a href="/dashboard" 
               role="menuitem" 
               aria-current="page">
                Dashboard
            </a>
        </li>
        <li role="none">
            <a href="/orders" 
               role="menuitem">
                Orders
            </a>
        </li>
        <li role="none">
            <a href="/customers" 
               role="menuitem">
                Customers
            </a>
        </li>
        <li role="none">
            <a href="/products" 
               role="menuitem">
                Products
            </a>
        </li>
    </ul>
</nav>

<!-- Breadcrumb navigation -->
<nav aria-label="Breadcrumb">
    <ol class="breadcrumb">
        <li>
            <a href="/dashboard">Dashboard</a>
        </li>
        <li>
            <a href="/orders">Orders</a>
        </li>
        <li aria-current="page">
            Order Details
        </li>
    </ol>
</nav>
```

## Testing and Validation

### Automated Testing
```javascript
// Axe-core accessibility testing
import { axe, toHaveNoViolations } from 'jest-axe';

expect.extend(toHaveNoViolations);

test('should not have accessibility violations', async () => {
    const { container } = render(<OrderForm />);
    const results = await axe(container);
    expect(results).toHaveNoViolations();
});

// Keyboard navigation testing
test('should be navigable with keyboard', () => {
    render(<OrderForm />);
    
    // Tab through form elements
    const firstInput = screen.getByLabelText('Company Name');
    firstInput.focus();
    expect(firstInput).toHaveFocus();
    
    // Test Enter key submission
    fireEvent.keyDown(firstInput, { key: 'Enter' });
    expect(mockSubmit).toHaveBeenCalled();
});
```

### Manual Testing Checklist
- [ ] **Keyboard Navigation**: All functionality accessible via keyboard
- [ ] **Screen Reader**: Content readable with screen reader
- [ ] **Color Contrast**: Sufficient contrast ratios (4.5:1 minimum)
- [ ] **Focus Management**: Clear focus indicators
- [ ] **Alternative Text**: Images have descriptive alt text
- [ ] **Form Labels**: All form inputs have proper labels
- [ ] **Error Messages**: Clear, accessible error messages
- [ ] **Skip Links**: Skip to main content functionality
- [ ] **ARIA Labels**: Proper ARIA attributes used
- [ ] **Semantic HTML**: Proper HTML structure and semantics

### Accessibility Tools
- **axe-core**: Automated accessibility testing
- **WAVE**: Web accessibility evaluation tool
- **Lighthouse**: Accessibility auditing
- **NVDA**: Screen reader for testing
- **VoiceOver**: macOS screen reader
- **JAWS**: Windows screen reader

## Performance Considerations

### Optimized for Assistive Technologies
```css
/* Reduce motion for users with vestibular disorders */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .button {
        border: 2px solid currentColor;
    }
    
    .text-primary {
        color: #000000;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .modal {
        background-color: #1f2937;
        color: #f9fafb;
    }
}
```

### Screen Reader Optimization
```html
<!-- Use aria-live for dynamic content -->
<div aria-live="polite" aria-atomic="true" id="status-updates">
    <!-- Status updates will be announced -->
</div>

<!-- Use aria-expanded for collapsible content -->
<button aria-expanded="false" 
        aria-controls="order-details"
        onclick="toggleDetails()">
    Show Order Details
</button>
<div id="order-details" hidden>
    <!-- Order details content -->
</div>

<!-- Use aria-describedby for complex interactions -->
<input type="text" 
       id="search" 
       aria-describedby="search-instructions"
       placeholder="Search...">
<div id="search-instructions" class="sr-only">
    Type to search orders. Use arrow keys to navigate results.
</div>
```

## Compliance and Documentation

### Accessibility Statement
```html
<div class="accessibility-statement">
    <h2>Accessibility Statement</h2>
    <p>
        Z5 Distribution System is committed to ensuring digital accessibility 
        for all users. We strive to meet WCAG 2.1 AA standards and provide 
        an inclusive experience for everyone.
    </p>
    
    <h3>Accessibility Features</h3>
    <ul>
        <li>Keyboard navigation support</li>
        <li>Screen reader compatibility</li>
        <li>High contrast mode support</li>
        <li>Alternative text for images</li>
        <li>Clear focus indicators</li>
        <li>Semantic HTML structure</li>
    </ul>
    
    <h3>Contact Us</h3>
    <p>
        If you encounter any accessibility issues, please contact us at 
        <a href="mailto:accessibility@z5distribution.com">
            accessibility@z5distribution.com
        </a>
    </p>
</div>
```

### Training and Documentation
- **Developer Training**: Accessibility best practices
- **Design Guidelines**: Inclusive design principles
- **Testing Procedures**: Accessibility testing methods
- **User Documentation**: Accessibility features guide
- **Support Resources**: Accessibility support contacts

This comprehensive accessibility implementation ensures the Z5 Distribution System is usable by all users, regardless of their abilities, while maintaining compliance with international accessibility standards.
