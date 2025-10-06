## Mobile Responsiveness TODO

Purpose: Track pages that need mobile-friendly improvements. For each view, implement a mobile card layout (hide wide tables on small screens), collapse large filter blocks on mobile, and ensure horizontal scrolling for unavoidable wide content.

Conventions to apply
- [ ] Replace table on mobile with a card list (`sm:hidden` for cards container, `hidden sm:block` for table).
- [ ] Use `overflow-x-auto` on wrappers and `min-w-max` on tables that must scroll.
- [ ] Collapse filters on mobile using Alpine.js toggles.
- [ ] Ensure actions are reachable (no off-screen buttons); stack controls on mobile.
- [ ] Verify image/barcode toggles apply to both table and cards where relevant.

Completed
- [x] Products › `products/index.blade.php`: Mobile cards, table scroll, collapsible filters, image/barcode toggles.
- [x] Products › `products/show.blade.php`: Stacked header/actions on mobile; responsive details.
- [x] Products › `products/create.blade.php`: Mobile-friendly actions; inputs already responsive.
- [x] Products › `products/edit.blade.php`: Mobile-friendly actions; inputs already responsive.

Products
// Done above

Orders
- [x] `orders/index.blade.php`: Add mobile card list, collapse filters, ensure actions column accessible.
- [x] `orders/create.blade.php`: Make line items table mobile-friendly (cards or scroll); inputs wrap.
- [x] `orders/edit.blade.php`: Same as create.
- [ ] `orders/show.blade.php`: Items/history tables as cards or scroll; stack meta info.

Sales
- [ ] `sales/index.blade.php`: Card list on mobile; actions accessible.
- [ ] `sales/create.blade.php`: Items table responsive; inputs wrap.
- [ ] `sales/show.blade.php`: Tables/card conversion; totals readable.

Inventory
- [ ] `inventory/index.blade.php`: Table → mobile cards; filters collapsible.
- [ ] `inventory/show.blade.php`: Movements table responsive; details stack.
- [ ] `inventory/low-stock.blade.php`: Card list on mobile.
- [ ] `inventory/stock-report.blade.php`: Wide table scroll; consider card summary.

Goods Receipts
- [ ] `goods-receipts/index.blade.php`: Card list; filters collapsible.
- [ ] `goods-receipts/show.blade.php`: Items table responsive; details stack.

Stock Transfers
- [ ] `stock-transfers/index.blade.php`: Card list; filters collapsible.
- [ ] `stock-transfers/show.blade.php`: Items table responsive; details stack.

Payments & Settings
- [ ] `payments/index.blade.php`: Card list; filters collapsible.
- [ ] `payment-types/index.blade.php`: Table → cards; drag/sort UX on mobile.
- [ ] `payment-terms/index.blade.php`: Table → cards.
- [ ] `product-brands/index.blade.php`: Table → cards.
- [ ] `uoms/index.blade.php`: Table → cards.

User Management
- [ ] `user-management/index.blade.php`: Card list; actions accessible.
- [ ] `user-management/roles.blade.php`: Table → cards.
- [ ] `user-management/permissions.blade.php`: Wide table scroll; consider grouping on mobile.

Customers
- [ ] `customers/show.blade.php`: Two content tables → cards; details stack.

Reports (all have wide tables; prefer scroll with summaries on mobile)
- [ ] `reports/orders.blade.php`
- [ ] `reports/sales.blade.php`
- [ ] `reports/monthly-summary.blade.php`
- [ ] `reports/growth-analysis.blade.php`
- [ ] `reports/payments.blade.php`
- [ ] `reports/suppliers.blade.php`
- [ ] `reports/goods-receipts.blade.php`
- [ ] `reports/customers.blade.php`
- [ ] `reports/stock-transfers.blade.php`
- [ ] `reports/inventory.blade.php`

Dashboard
- [ ] `dashboard.blade.php`: Convert embedded tables to cards on mobile; ensure charts are responsive and scrollable if needed.

Notes
- Prioritize transactional flows: Orders → Sales → Inventory.
- Keep to a single CSS framework (Tailwind) and Alpine.js for behavior.

