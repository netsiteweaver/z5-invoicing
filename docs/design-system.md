# Design System

## Overview
This design system provides comprehensive guidelines for creating a modern, corporate-grade user interface for the Z5 Distribution System. It emphasizes usability, accessibility, and professional aesthetics using TailwindCSS and Radix UI components.

## Design Principles

### Core Principles
- **Clarity**: Clear, intuitive interfaces that reduce cognitive load
- **Consistency**: Consistent patterns and behaviors across the application
- **Efficiency**: Streamlined workflows that maximize productivity
- **Accessibility**: Inclusive design that works for all users
- **Professional**: Corporate-grade aesthetics that inspire confidence

### Visual Hierarchy
- **Primary Actions**: Prominent, clear call-to-action buttons
- **Secondary Actions**: Subtle, supportive actions
- **Information Architecture**: Logical grouping and organization
- **Progressive Disclosure**: Show information when needed
- **Visual Weight**: Use size, color, and position to guide attention

## Color Palette

### Primary Colors
```css
/* Corporate Blue Palette */
--primary-50: #eff6ff;
--primary-100: #dbeafe;
--primary-200: #bfdbfe;
--primary-300: #93c5fd;
--primary-400: #60a5fa;
--primary-500: #3b82f6;  /* Main Primary */
--primary-600: #2563eb;
--primary-700: #1d4ed8;
--primary-800: #1e40af;
--primary-900: #1e3a8a;

/* Success Green Palette */
--success-50: #f0fdf4;
--success-100: #dcfce7;
--success-200: #bbf7d0;
--success-300: #86efac;
--success-400: #4ade80;
--success-500: #22c55e;  /* Main Success */
--success-600: #16a34a;
--success-700: #15803d;
--success-800: #166534;
--success-900: #14532d;

/* Warning Orange Palette */
--warning-50: #fffbeb;
--warning-100: #fef3c7;
--warning-200: #fde68a;
--warning-300: #fcd34d;
--warning-400: #fbbf24;
--warning-500: #f59e0b;  /* Main Warning */
--warning-600: #d97706;
--warning-700: #b45309;
--warning-800: #92400e;
--warning-900: #78350f;

/* Error Red Palette */
--error-50: #fef2f2;
--error-100: #fee2e2;
--error-200: #fecaca;
--error-300: #fca5a5;
--error-400: #f87171;
--error-500: #ef4444;  /* Main Error */
--error-600: #dc2626;
--error-700: #b91c1c;
--error-800: #991b1b;
--error-900: #7f1d1d;
```

### Neutral Colors
```css
/* Gray Palette */
--gray-50: #f9fafb;
--gray-100: #f3f4f6;
--gray-200: #e5e7eb;
--gray-300: #d1d5db;
--gray-400: #9ca3af;
--gray-500: #6b7280;  /* Main Gray */
--gray-600: #4b5563;
--gray-700: #374151;
--gray-800: #1f2937;
--gray-900: #111827;
```

### Semantic Colors
```css
/* Semantic Color Usage */
--text-primary: var(--gray-900);
--text-secondary: var(--gray-600);
--text-tertiary: var(--gray-500);
--text-disabled: var(--gray-400);

--bg-primary: #ffffff;
--bg-secondary: var(--gray-50);
--bg-tertiary: var(--gray-100);
--bg-disabled: var(--gray-200);

--border-primary: var(--gray-200);
--border-secondary: var(--gray-300);
--border-focus: var(--primary-500);
--border-error: var(--error-500);
```

## Typography

### Font Stack
```css
/* Primary Font Stack */
--font-primary: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
--font-mono: 'JetBrains Mono', 'Fira Code', Consolas, monospace;

/* Font Weights */
--font-weight-light: 300;
--font-weight-normal: 400;
--font-weight-medium: 500;
--font-weight-semibold: 600;
--font-weight-bold: 700;
```

### Typography Scale
```css
/* Heading Styles */
.heading-1 {
  font-size: 2.25rem;    /* 36px */
  line-height: 2.5rem;   /* 40px */
  font-weight: var(--font-weight-bold);
  letter-spacing: -0.025em;
}

.heading-2 {
  font-size: 1.875rem;   /* 30px */
  line-height: 2.25rem;  /* 36px */
  font-weight: var(--font-weight-semibold);
  letter-spacing: -0.025em;
}

.heading-3 {
  font-size: 1.5rem;     /* 24px */
  line-height: 2rem;     /* 32px */
  font-weight: var(--font-weight-semibold);
}

.heading-4 {
  font-size: 1.25rem;    /* 20px */
  line-height: 1.75rem;  /* 28px */
  font-weight: var(--font-weight-medium);
}

.heading-5 {
  font-size: 1.125rem;   /* 18px */
  line-height: 1.75rem;  /* 28px */
  font-weight: var(--font-weight-medium);
}

.heading-6 {
  font-size: 1rem;       /* 16px */
  line-height: 1.5rem;  /* 24px */
  font-weight: var(--font-weight-medium);
}

/* Body Text */
.body-large {
  font-size: 1.125rem;   /* 18px */
  line-height: 1.75rem;  /* 28px */
  font-weight: var(--font-weight-normal);
}

.body-medium {
  font-size: 1rem;       /* 16px */
  line-height: 1.5rem;  /* 24px */
  font-weight: var(--font-weight-normal);
}

.body-small {
  font-size: 0.875rem;  /* 14px */
  line-height: 1.25rem;  /* 20px */
  font-weight: var(--font-weight-normal);
}

.body-xs {
  font-size: 0.75rem;    /* 12px */
  line-height: 1rem;     /* 16px */
  font-weight: var(--font-weight-normal);
}
```

## Spacing System

### Spacing Scale
```css
/* Spacing Scale (based on 4px grid) */
--space-0: 0px;
--space-1: 0.25rem;   /* 4px */
--space-2: 0.5rem;    /* 8px */
--space-3: 0.75rem;   /* 12px */
--space-4: 1rem;      /* 16px */
--space-5: 1.25rem;   /* 20px */
--space-6: 1.5rem;    /* 24px */
--space-8: 2rem;      /* 32px */
--space-10: 2.5rem;   /* 40px */
--space-12: 3rem;     /* 48px */
--space-16: 4rem;     /* 64px */
--space-20: 5rem;     /* 80px */
--space-24: 6rem;     /* 96px */
--space-32: 8rem;     /* 128px */
```

### Layout Spacing
```css
/* Container Spacing */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 var(--space-4);
}

.section {
  padding: var(--space-16) 0;
}

.section-sm {
  padding: var(--space-12) 0;
}

.section-lg {
  padding: var(--space-24) 0;
}

/* Component Spacing */
.card {
  padding: var(--space-6);
  margin-bottom: var(--space-4);
}

.form-group {
  margin-bottom: var(--space-6);
}

.button-group {
  display: flex;
  gap: var(--space-3);
}
```

## Component Library

### Buttons

#### Primary Button
```css
.btn-primary {
  background-color: var(--primary-500);
  color: white;
  border: none;
  border-radius: 0.375rem;
  padding: var(--space-3) var(--space-6);
  font-size: var(--body-medium);
  font-weight: var(--font-weight-medium);
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-primary:hover {
  background-color: var(--primary-600);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-primary:active {
  transform: translateY(0);
  box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
}

.btn-primary:disabled {
  background-color: var(--gray-300);
  color: var(--gray-500);
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}
```

#### Secondary Button
```css
.btn-secondary {
  background-color: transparent;
  color: var(--primary-500);
  border: 1px solid var(--primary-500);
  border-radius: 0.375rem;
  padding: var(--space-3) var(--space-6);
  font-size: var(--body-medium);
  font-weight: var(--font-weight-medium);
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-secondary:hover {
  background-color: var(--primary-50);
  border-color: var(--primary-600);
  color: var(--primary-600);
}
```

#### Button Sizes
```css
.btn-sm {
  padding: var(--space-2) var(--space-4);
  font-size: var(--body-small);
}

.btn-lg {
  padding: var(--space-4) var(--space-8);
  font-size: var(--body-large);
}

.btn-xl {
  padding: var(--space-5) var(--space-10);
  font-size: var(--heading-5);
}
```

### Form Components

#### Input Fields
```css
.form-input {
  width: 100%;
  padding: var(--space-3) var(--space-4);
  border: 1px solid var(--border-primary);
  border-radius: 0.375rem;
  font-size: var(--body-medium);
  background-color: var(--bg-primary);
  transition: all 0.2s ease;
}

.form-input:focus {
  outline: none;
  border-color: var(--border-focus);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input:disabled {
  background-color: var(--bg-disabled);
  color: var(--text-disabled);
  cursor: not-allowed;
}

.form-input.error {
  border-color: var(--border-error);
}

.form-input.error:focus {
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}
```

#### Select Dropdown
```css
.form-select {
  width: 100%;
  padding: var(--space-3) var(--space-4);
  border: 1px solid var(--border-primary);
  border-radius: 0.375rem;
  font-size: var(--body-medium);
  background-color: var(--bg-primary);
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
  background-position: right var(--space-3) center;
  background-repeat: no-repeat;
  background-size: 1.5em 1.5em;
  padding-right: var(--space-10);
  cursor: pointer;
  transition: all 0.2s ease;
}

.form-select:focus {
  outline: none;
  border-color: var(--border-focus);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
```

#### Form Labels
```css
.form-label {
  display: block;
  font-size: var(--body-medium);
  font-weight: var(--font-weight-medium);
  color: var(--text-primary);
  margin-bottom: var(--space-2);
}

.form-label.required::after {
  content: " *";
  color: var(--error-500);
}

.form-help {
  font-size: var(--body-small);
  color: var(--text-secondary);
  margin-top: var(--space-1);
}

.form-error {
  font-size: var(--body-small);
  color: var(--error-500);
  margin-top: var(--space-1);
}
```

### Cards

#### Basic Card
```css
.card {
  background-color: var(--bg-primary);
  border: 1px solid var(--border-primary);
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.card-header {
  padding: var(--space-6);
  border-bottom: 1px solid var(--border-primary);
  background-color: var(--bg-secondary);
}

.card-title {
  font-size: var(--heading-5);
  font-weight: var(--font-weight-semibold);
  color: var(--text-primary);
  margin: 0;
}

.card-body {
  padding: var(--space-6);
}

.card-footer {
  padding: var(--space-6);
  border-top: 1px solid var(--border-primary);
  background-color: var(--bg-secondary);
}
```

#### Interactive Card
```css
.card-interactive {
  cursor: pointer;
  transition: all 0.2s ease;
}

.card-interactive:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.card-interactive:active {
  transform: translateY(0);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
```

### Tables

#### Data Table
```css
.table-container {
  overflow-x: auto;
  border: 1px solid var(--border-primary);
  border-radius: 0.5rem;
}

.table {
  width: 100%;
  border-collapse: collapse;
  background-color: var(--bg-primary);
}

.table th {
  background-color: var(--bg-secondary);
  padding: var(--space-4);
  text-align: left;
  font-size: var(--body-small);
  font-weight: var(--font-weight-semibold);
  color: var(--text-primary);
  border-bottom: 1px solid var(--border-primary);
}

.table td {
  padding: var(--space-4);
  font-size: var(--body-medium);
  color: var(--text-primary);
  border-bottom: 1px solid var(--border-primary);
}

.table tbody tr:hover {
  background-color: var(--bg-secondary);
}

.table tbody tr:last-child td {
  border-bottom: none;
}
```

#### Table Actions
```css
.table-actions {
  display: flex;
  gap: var(--space-2);
  align-items: center;
}

.table-action {
  padding: var(--space-2);
  border: none;
  background: none;
  color: var(--text-secondary);
  cursor: pointer;
  border-radius: 0.25rem;
  transition: all 0.2s ease;
}

.table-action:hover {
  background-color: var(--bg-tertiary);
  color: var(--text-primary);
}
```

### Navigation

#### Sidebar Navigation
```css
.sidebar {
  width: 256px;
  background-color: var(--bg-primary);
  border-right: 1px solid var(--border-primary);
  height: 100vh;
  overflow-y: auto;
}

.sidebar-header {
  padding: var(--space-6);
  border-bottom: 1px solid var(--border-primary);
}

.sidebar-nav {
  padding: var(--space-4);
}

.nav-group {
  margin-bottom: var(--space-6);
}

.nav-group-title {
  font-size: var(--body-small);
  font-weight: var(--font-weight-semibold);
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: var(--space-3);
  padding: 0 var(--space-3);
}

.nav-item {
  display: flex;
  align-items: center;
  padding: var(--space-3);
  border-radius: 0.375rem;
  color: var(--text-secondary);
  text-decoration: none;
  transition: all 0.2s ease;
  margin-bottom: var(--space-1);
}

.nav-item:hover {
  background-color: var(--bg-secondary);
  color: var(--text-primary);
}

.nav-item.active {
  background-color: var(--primary-50);
  color: var(--primary-600);
}

.nav-item-icon {
  width: 20px;
  height: 20px;
  margin-right: var(--space-3);
}
```

#### Top Navigation
```css
.top-nav {
  height: 64px;
  background-color: var(--bg-primary);
  border-bottom: 1px solid var(--border-primary);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 var(--space-6);
}

.top-nav-left {
  display: flex;
  align-items: center;
  gap: var(--space-4);
}

.top-nav-right {
  display: flex;
  align-items: center;
  gap: var(--space-4);
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  font-size: var(--body-small);
  color: var(--text-secondary);
}

.breadcrumb-item {
  color: var(--text-secondary);
  text-decoration: none;
}

.breadcrumb-item:hover {
  color: var(--text-primary);
}

.breadcrumb-item.active {
  color: var(--text-primary);
  font-weight: var(--font-weight-medium);
}
```

### Modals and Overlays

#### Modal
```css
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal {
  background-color: var(--bg-primary);
  border-radius: 0.5rem;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  padding: var(--space-6);
  border-bottom: 1px solid var(--border-primary);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.modal-title {
  font-size: var(--heading-4);
  font-weight: var(--font-weight-semibold);
  color: var(--text-primary);
  margin: 0;
}

.modal-close {
  background: none;
  border: none;
  color: var(--text-secondary);
  cursor: pointer;
  padding: var(--space-2);
  border-radius: 0.25rem;
  transition: all 0.2s ease;
}

.modal-close:hover {
  background-color: var(--bg-secondary);
  color: var(--text-primary);
}

.modal-body {
  padding: var(--space-6);
}

.modal-footer {
  padding: var(--space-6);
  border-top: 1px solid var(--border-primary);
  display: flex;
  justify-content: flex-end;
  gap: var(--space-3);
}
```

### Status Indicators

#### Status Badges
```css
.status-badge {
  display: inline-flex;
  align-items: center;
  padding: var(--space-1) var(--space-3);
  border-radius: 9999px;
  font-size: var(--body-xs);
  font-weight: var(--font-weight-medium);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.status-badge.success {
  background-color: var(--success-100);
  color: var(--success-700);
}

.status-badge.warning {
  background-color: var(--warning-100);
  color: var(--warning-700);
}

.status-badge.error {
  background-color: var(--error-100);
  color: var(--error-700);
}

.status-badge.info {
  background-color: var(--primary-100);
  color: var(--primary-700);
}

.status-badge.neutral {
  background-color: var(--gray-100);
  color: var(--gray-700);
}
```

#### Progress Indicators
```css
.progress-bar {
  width: 100%;
  height: 8px;
  background-color: var(--bg-tertiary);
  border-radius: 9999px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background-color: var(--primary-500);
  border-radius: 9999px;
  transition: width 0.3s ease;
}

.progress-fill.success {
  background-color: var(--success-500);
}

.progress-fill.warning {
  background-color: var(--warning-500);
}

.progress-fill.error {
  background-color: var(--error-500);
}
```

## Responsive Design

### Breakpoints
```css
/* Responsive Breakpoints */
--breakpoint-sm: 640px;
--breakpoint-md: 768px;
--breakpoint-lg: 1024px;
--breakpoint-xl: 1280px;
--breakpoint-2xl: 1536px;

/* Mobile First Approach */
@media (min-width: 640px) {
  .sm\:hidden { display: none; }
  .sm\:block { display: block; }
  .sm\:flex { display: flex; }
}

@media (min-width: 768px) {
  .md\:hidden { display: none; }
  .md\:block { display: block; }
  .md\:flex { display: flex; }
}

@media (min-width: 1024px) {
  .lg\:hidden { display: none; }
  .lg\:block { display: block; }
  .lg\:flex { display: flex; }
}
```

### Responsive Layouts
```css
/* Responsive Grid */
.grid-responsive {
  display: grid;
  gap: var(--space-4);
  grid-template-columns: 1fr;
}

@media (min-width: 640px) {
  .grid-responsive {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1024px) {
  .grid-responsive {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (min-width: 1280px) {
  .grid-responsive {
    grid-template-columns: repeat(4, 1fr);
  }
}

/* Responsive Sidebar */
.sidebar-responsive {
  position: fixed;
  top: 0;
  left: -256px;
  width: 256px;
  height: 100vh;
  transition: left 0.3s ease;
  z-index: 1000;
}

.sidebar-responsive.open {
  left: 0;
}

@media (min-width: 1024px) {
  .sidebar-responsive {
    position: static;
    left: 0;
  }
}
```

## Accessibility

### Focus Management
```css
/* Focus Styles */
.focus-visible {
  outline: 2px solid var(--primary-500);
  outline-offset: 2px;
}

.focus-visible:not(:focus-visible) {
  outline: none;
}

/* Skip Links */
.skip-link {
  position: absolute;
  top: -40px;
  left: 6px;
  background-color: var(--primary-500);
  color: white;
  padding: var(--space-2) var(--space-4);
  text-decoration: none;
  border-radius: 0.25rem;
  z-index: 1000;
}

.skip-link:focus {
  top: 6px;
}
```

### Screen Reader Support
```css
/* Screen Reader Only */
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

/* High Contrast Mode */
@media (prefers-contrast: high) {
  .btn-primary {
    border: 2px solid var(--primary-700);
  }
  
  .form-input {
    border-width: 2px;
  }
}
```

## Animation and Transitions

### Transition System
```css
/* Standard Transitions */
.transition-fast {
  transition: all 0.15s ease;
}

.transition-normal {
  transition: all 0.2s ease;
}

.transition-slow {
  transition: all 0.3s ease;
}

/* Hover Animations */
.hover-lift {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-lift:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Loading Animations */
.loading-spinner {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.loading-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}
```

## Dark Mode Support

### Dark Mode Variables
```css
/* Dark Mode Color Palette */
@media (prefers-color-scheme: dark) {
  :root {
    --text-primary: #f9fafb;
    --text-secondary: #d1d5db;
    --text-tertiary: #9ca3af;
    --text-disabled: #6b7280;
    
    --bg-primary: #111827;
    --bg-secondary: #1f2937;
    --bg-tertiary: #374151;
    --bg-disabled: #4b5563;
    
    --border-primary: #374151;
    --border-secondary: #4b5563;
    --border-focus: var(--primary-400);
    --border-error: var(--error-400);
  }
}

/* Dark Mode Toggle */
.dark-mode {
  --text-primary: #f9fafb;
  --text-secondary: #d1d5db;
  --text-tertiary: #9ca3af;
  --text-disabled: #6b7280;
  
  --bg-primary: #111827;
  --bg-secondary: #1f2937;
  --bg-tertiary: #374151;
  --bg-disabled: #4b5563;
  
  --border-primary: #374151;
  --border-secondary: #4b5563;
  --border-focus: var(--primary-400);
  --border-error: var(--error-400);
}
```

## Implementation Guidelines

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

### Component Usage Examples
```php
<!-- Button Component -->
<button class="btn-primary btn-lg">
  Create Order
</button>

<!-- Form Component -->
<div class="form-group">
  <label class="form-label required">Customer Name</label>
  <input type="text" class="form-input" placeholder="Enter customer name">
  <div class="form-help">Enter the full customer name</div>
</div>

<!-- Card Component -->
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Order Summary</h3>
  </div>
  <div class="card-body">
    <!-- Card content -->
  </div>
  <div class="card-footer">
    <button class="btn-primary">Save Order</button>
  </div>
</div>
```

This design system provides a comprehensive foundation for building a modern, professional, and accessible user interface for the Z5 Distribution System. It emphasizes consistency, usability, and corporate-grade aesthetics while maintaining flexibility for customization and future enhancements.
