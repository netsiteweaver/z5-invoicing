## Z5 Distribution System — End‑User Manual

Last updated: 2025-09-16

### Who this manual is for
- **Normal users**: day-to-day operations (orders, sales, inventory, payments)
- **Administrators**: manage users, permissions, catalogs, and settings
- **Root users**: full system access and administration

## 1. Overview
The Z5 Distribution System is a modern business application for managing products, customers, orders, sales, payments, and inventory across one or more departments/locations. It includes dashboards, reporting, email notifications, audit trails, and role-based access control.

## 2. Access & Accounts
- **URL**: Provided by your administrator
- **Supported browsers**: Latest Chrome, Edge, Firefox, Safari
- **Login**: Enter your email/username and password; enable Remember Me if needed
- **Forgot password**: Use “Forgot password” to receive a reset link by email
- **Sign out**: Use your avatar/profile menu → Sign out
- **Account status**: If your account is inactive or locked, contact an administrator

## 3. Navigation & UI Basics
- **Main navigation**: Sidebar with modules (Dashboard, Customers, Products, Orders, Sales, Payments, Inventory, Reports, Audit, Users)
- **Search and filters**: Most list pages include a search bar, filters, and status chips
- **Tables**: Click column headers to sort; use pagination controls at the bottom
- **Actions**: Common actions include View, Edit, Delete, Status Update, Export
- **Badges**: Status is shown with colored badges (e.g., order `confirmed`, payment `overdue`)
- **Exports**: Export lists to CSV/Excel; many detail pages offer PDF generation

## 4. Roles & Permissions
- **Normal**: Basic access to assigned features
- **Admin**: Broad access including configuration and user management
- **Root**: Full access; can manage all permissions

If a menu item or action is missing, your role may not allow it. Contact an administrator.

## 5. Customers
### What you can do
- Create, edit, and deactivate customers; advanced search and filtering
- Track order, sales, and payment history per customer
- Add internal notes and log communications (email, phone, meetings)
- Import customers from CSV; export to CSV

### Create a customer
1) Go to Customers → New
2) Enter company and contact details (company name, phone, email, city)
3) Add optional identifiers (BRN, VAT) and remarks
4) Save

### Find and manage customers
- Use the search bar (company name, contact, phone, email) and filters (type, city, status)
- Open a customer to view details, history, notes, and communications
- Deactivate instead of deleting when there is historical activity

## 6. Products (Catalog)
### What you can do
- Create and edit products with pricing, categories, and brands
- Upload product images; manage a primary image
- Bulk import products via CSV; export the catalog
- View linked inventory levels per department/location

### Create a product
1) Go to Products → New
2) Enter name, stock reference, pricing, category, and brand
3) Add specifications (size, compartments, color) if needed
4) Save, then optionally upload images

### Bulk import (CSV)
- Download/import template if provided by your admin
- Ensure unique stock references; verify required columns
- Upload CSV via Products → Import; review the preview and confirm

## 7. Orders
Orders track the full lifecycle from creation to delivery and can be converted into sales.

### Statuses
`draft` → `pending` → `confirmed` → `processing` → `shipped` → `delivered` → `cancelled`

### Create an order
1) Go to Orders → New
2) Select a customer
3) Add items: product, quantity, pricing, discounts, and tax
4) Review the pricing summary (subtotal, discount, tax, shipping, total)
5) Save as `draft` or submit to `pending`

### Manage an order
- Edit order details and items while allowed by status
- Update status as the order progresses; capture notes
- Generate a PDF document when needed
- Convert to sale once the order is `confirmed` (see Sales)

## 8. Sales
Sales can be created directly or by converting a confirmed order.

### Statuses
`draft` → `pending` → `confirmed` → `processing` → `shipped` → `delivered` → `cancelled`

### Create a sale (two options)
- Direct: Sales → New → select customer and add items
- From an order: Orders → open order → Convert to Sale → select items → confirm

### Manage a sale
- Update details and items (while permitted by status)
- Process status updates through fulfillment stages
- Generate PDF documents; proceed to record payments

## 9. Payments
Payments track receipts and disbursements and can be linked to orders or sales.

### Statuses
`pending`, `partial`, `paid`, `overdue`, `cancelled`

### Record a payment
1) Go to Payments → New
2) Select payment type and method (cash, bank transfer, wallet, card)
3) Optionally link to an order or sale
4) Enter amount, date, reference number, and notes
5) Save and set the correct status

### Monitor outstanding and overdue
- Use Payments → Outstanding/Overdue views or Dashboard cards
- Filter by customer, method, or date range; export as needed

## 10. Inventory
Manage stock levels across departments/locations.

### What you can do
- View stock by product and department
- Record stock movements (in/out/transfer/adjustment/count)
- Create and track stock transfers between departments
- Perform stock counts and reconcile variances
- Monitor low stock alerts and reorder points

### Common tasks
- Adjustment: Inventory → Adjustment → select product/department → set quantity → reason → Save
- Transfer: Inventory → Transfers → New → from/to department → add items → submit and track status
- Count: Inventory → Counts → generate sheets → enter results → reconcile

## 11. Dashboard & Reports
- View KPIs (orders, sales, customers, payments, inventory) and trends
- Drill into charts and tables; use filters and date ranges
- Export reports to PDF, Excel, or CSV where available
- Optionally configure scheduled or email exports if enabled by admin

## 12. Audit Trail
- Access via Audit (admin/authorized roles)
- Filter by user, action, resource, and date range
- Open a record to view before/after values and metadata (IP, user agent)

## 13. User Management (Admin)
- Create users, set roles (Normal, Admin, Root), and assign granular permissions
- Activate/deactivate accounts; reset passwords; review login history
- Use permission matrices or role templates to manage access consistently

## 14. Documents & Exports
- Detail pages often provide PDF generation (orders, sales, payments)
- Lists can be exported (CSV/Excel); use filters first to narrow results
- Keep exported data secure per your organization’s policies

## 15. Mobile & Accessibility
- Fully responsive design; works on phones and tablets
- Keyboard navigable tables and forms; color-contrast friendly components
- Use system zoom and screen readers as needed

## 16. Data Rules (at a glance)
- Records with historical links (e.g., products used in orders) cannot be hard-deleted
- Prefer deactivation/soft delete to preserve history
- Many status changes are permission-controlled and may be irreversible
- Unique fields (e.g., product stock reference, customer email) are validated

## 17. Troubleshooting & FAQs
- I can’t log in: Confirm username/password; try “Forgot password”; if still blocked, contact an administrator
- I don’t see a menu or button: Your role/permissions may not allow it; contact an administrator
- Cannot change status: The current workflow or your permissions may restrict this action
- Import failed: Check CSV headers, required columns, and unique fields; fix errors and re-upload
- Payment won’t mark as paid: Ensure linked order/sale exists and that amounts/dates are valid
- Inventory won’t update: Verify you selected the correct department and have sufficient stock
- PDF won’t download: Disable pop-up blockers or try another browser

## 18. Glossary
- Customer: The company or person you sell to
- Product: An item in the catalog with pricing and attributes
- Department: A physical location or warehouse for inventory
- Order: A request from a customer that can become a sale
- Sale: A confirmed transaction that can be delivered and paid
- Payment: A recorded receipt/disbursement linked to orders/sales
- Movement: Any change in inventory (in/out/transfer/adjustment/count)

## 19. Support
- For access issues or feature requests, contact your system administrator
- For critical incidents, follow your organization’s escalation process

— End of user manual —

