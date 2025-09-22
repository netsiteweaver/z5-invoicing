<?php

namespace App\Services;

class BreadcrumbService
{
    protected $breadcrumbs = [];

    public function __construct()
    {
        // Always start with home
        $this->add('Home', route('dashboard'), false);
    }

    /**
     * Add a breadcrumb item
     *
     * @param string $title
     * @param string|null $url
     * @param bool $isCurrent
     * @return $this
     */
    public function add(string $title, ?string $url = null, bool $isCurrent = false): self
    {
        $this->breadcrumbs[] = [
            'title' => $title,
            'url' => $url,
            'current' => $isCurrent
        ];

        return $this;
    }

    /**
     * Add multiple breadcrumb items at once
     *
     * @param array $items Array of ['title' => 'Title', 'url' => 'url', 'current' => false]
     * @return $this
     */
    public function addMultiple(array $items): self
    {
        foreach ($items as $item) {
            $this->add(
                $item['title'],
                $item['url'] ?? null,
                $item['current'] ?? false
            );
        }

        return $this;
    }

    /**
     * Get all breadcrumbs
     *
     * @return array
     */
    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }

    /**
     * Clear all breadcrumbs (except home)
     *
     * @return $this
     */
    public function clear(): self
    {
        $this->breadcrumbs = [
            ['title' => 'Home', 'url' => route('dashboard'), 'current' => false]
        ];

        return $this;
    }

    /**
     * Set breadcrumbs for common pages
     *
     * @param string $page
     * @param array $params
     * @return $this
     */
    public function setForPage(string $page, array $params = []): self
    {
        $this->clear();

        switch ($page) {
            case 'departments.index':
                $this->add('Departments', null, true);
                break;

            case 'departments.create':
                $this->add('Departments', route('departments.index'));
                $this->add('Create Department', null, true);
                break;

            case 'departments.show':
                $this->add('Departments', route('departments.index'));
                $this->add($params['department']->name, null, true);
                break;

            case 'departments.edit':
                $this->add('Departments', route('departments.index'));
                $this->add($params['department']->name, route('departments.show', $params['department']));
                $this->add('Edit Department', null, true);
                break;
            case 'suppliers.index':
                $this->add('Suppliers', null, true);
                break;

            case 'suppliers.create':
                $this->add('Suppliers', route('suppliers.index'));
                $this->add('Create Supplier', null, true);
                break;

            case 'suppliers.show':
                $this->add('Suppliers', route('suppliers.index'));
                $this->add($params['supplier']->name, null, true);
                break;

            case 'suppliers.edit':
                $this->add('Suppliers', route('suppliers.index'));
                $this->add($params['supplier']->name, route('suppliers.show', $params['supplier']));
                $this->add('Edit Supplier', null, true);
                break;
            case 'orders.index':
                $this->add('Orders', null, true);
                break;

            case 'orders.show':
                $this->add('Orders', route('orders.index'));
                $this->add('Order #' . $params['order']->order_number, null, true);
                break;

            case 'orders.create':
                $this->add('Orders', route('orders.index'));
                $this->add('Create Order', null, true);
                break;

            case 'orders.edit':
                $this->add('Orders', route('orders.index'));
                $this->add('Order #' . $params['order']->order_number, route('orders.show', $params['order']));
                $this->add('Edit Order', null, true);
                break;

            case 'customers.index':
                $this->add('Customers', null, true);
                break;

            case 'customers.show':
                $customerName = $params['customer']->company_name ?? $params['customer']->full_name;
                $this->add('Customers', route('customers.index'));
                $this->add($customerName, null, true);
                break;

            case 'customers.create':
                $this->add('Customers', route('customers.index'));
                $this->add('Create Customer', null, true);
                break;

            case 'customers.edit':
                $customerName = $params['customer']->company_name ?? $params['customer']->full_name;
                $this->add('Customers', route('customers.index'));
                $this->add($customerName, route('customers.show', $params['customer']));
                $this->add('Edit Customer', null, true);
                break;

            case 'products.index':
                $this->add('Products', null, true);
                break;

            case 'products.show':
                $this->add('Products', route('products.index'));
                $this->add($params['product']->name, null, true);
                break;

            case 'products.create':
                $this->add('Products', route('products.index'));
                $this->add('Create Product', null, true);
                break;

            case 'products.edit':
                $this->add('Products', route('products.index'));
                $this->add($params['product']->name, route('products.show', $params['product']));
                $this->add('Edit Product', null, true);
                break;

            case 'sales.index':
                $this->add('Sales', null, true);
                break;

            case 'sales.show':
                $this->add('Sales', route('sales.index'));
                $this->add('Sale #' . $params['sale']->id, null, true);
                break;

            case 'inventory.index':
                $this->add('Inventory', null, true);
                break;

            case 'inventory.show':
                $this->add('Inventory', route('inventory.index'));
                $this->add($params['inventory']->product->name, null, true);
                break;

            // Goods Receipts
            case 'goods-receipts.index':
                $this->add('Inventory', route('inventory.index'));
                $this->add('Goods Receipts', null, true);
                break;

            case 'goods-receipts.create':
                $this->add('Inventory', route('inventory.index'));
                $this->add('Goods Receipts', route('goods-receipts.index'));
                $this->add('Create Receipt', null, true);
                break;

            case 'goods-receipts.show':
                $this->add('Inventory', route('inventory.index'));
                $this->add('Goods Receipts', route('goods-receipts.index'));
                $this->add($params['receipt']->grn_number ?? 'Receipt', null, true);
                break;

            case 'goods-receipts.edit':
                $this->add('Inventory', route('inventory.index'));
                $this->add('Goods Receipts', route('goods-receipts.index'));
                $this->add($params['receipt']->grn_number ?? 'Receipt', route('goods-receipts.show', $params['receipt']));
                $this->add('Edit Receipt', null, true);
                break;

            // Stock Transfers
            case 'stock-transfers.index':
                $this->add('Inventory', route('inventory.index'));
                $this->add('Stock Transfers', null, true);
                break;

            case 'stock-transfers.create':
                $this->add('Inventory', route('inventory.index'));
                $this->add('Stock Transfers', route('stock-transfers.index'));
                $this->add('Create Transfer', null, true);
                break;

            case 'stock-transfers.show':
                $this->add('Inventory', route('inventory.index'));
                $this->add('Stock Transfers', route('stock-transfers.index'));
                $this->add($params['transfer']->transfer_number ?? 'Transfer', null, true);
                break;

            case 'stock-transfers.edit':
                $this->add('Inventory', route('inventory.index'));
                $this->add('Stock Transfers', route('stock-transfers.index'));
                $this->add($params['transfer']->transfer_number ?? 'Transfer', route('stock-transfers.show', $params['transfer']));
                $this->add('Edit Transfer', null, true);
                break;

            case 'product-categories.index':
                $this->add('Product Categories', null, true);
                break;

            case 'product-categories.show':
                $this->add('Product Categories', route('product-categories.index'));
                $this->add($params['productCategory']->name, null, true);
                break;

            case 'product-categories.create':
                $this->add('Product Categories', route('product-categories.index'));
                $this->add('Create Category', null, true);
                break;

            case 'product-categories.edit':
                $this->add('Product Categories', route('product-categories.index'));
                $this->add($params['productCategory']->name, route('product-categories.show', $params['productCategory']));
                $this->add('Edit Category', null, true);
                break;

            case 'product-brands.index':
                $this->add('Product Brands', null, true);
                break;

            case 'product-brands.show':
                $this->add('Product Brands', route('product-brands.index'));
                $this->add($params['productBrand']->name, null, true);
                break;

            case 'product-brands.create':
                $this->add('Product Brands', route('product-brands.index'));
                $this->add('Create Brand', null, true);
                break;

            case 'product-brands.edit':
                $this->add('Product Brands', route('product-brands.index'));
                $this->add($params['productBrand']->name, route('product-brands.show', $params['productBrand']));
                $this->add('Edit Brand', null, true);
                break;

            case 'user-management.index':
                $this->add('User Management', null, true);
                break;

            case 'user-management.show':
                $this->add('User Management', route('user-management.index'));
                $this->add($params['user']->name, null, true);
                break;

            case 'user-management.create':
                $this->add('User Management', route('user-management.index'));
                $this->add('Create User', null, true);
                break;

            case 'user-management.edit':
                $this->add('User Management', route('user-management.index'));
                $this->add($params['user']->name, route('user-management.show', $params['user']));
                $this->add('Edit User', null, true);
                break;

            case 'payment-terms.index':
                $this->add('Payment Terms', null, true);
                break;

            case 'payment-terms.show':
                $this->add('Payment Terms', route('payment-terms.index'));
                $this->add($params['payment_term']->name, null, true);
                break;

            case 'payment-terms.create':
                $this->add('Payment Terms', route('payment-terms.index'));
                $this->add('Create Payment Term', null, true);
                break;

            case 'payment-terms.edit':
                $this->add('Payment Terms', route('payment-terms.index'));
                $this->add($params['payment_term']->name, route('payment-terms.show', $params['payment_term']));
                $this->add('Edit Payment Term', null, true);
                break;

            case 'uoms.index':
                $this->add('Units of Measure', null, true);
                break;

            case 'uoms.show':
                $this->add('Units of Measure', route('uoms.index'));
                $this->add($params['uom']->name, null, true);
                break;

            case 'uoms.create':
                $this->add('Units of Measure', route('uoms.index'));
                $this->add('Create UOM', null, true);
                break;

            case 'uoms.edit':
                $this->add('Units of Measure', route('uoms.index'));
                $this->add($params['uom']->name, route('uoms.show', $params['uom']));
                $this->add('Edit UOM', null, true);
                break;

            case 'payments.index':
                $this->add('Payments', null, true);
                break;

            case 'payments.show':
                $this->add('Payments', route('payments.index'));
                $this->add('Payment #' . $params['payment']->payment_number, null, true);
                break;

            case 'payments.create':
                $this->add('Payments', route('payments.index'));
                $this->add('Record Payment', null, true);
                break;
        }

        return $this;
    }
}
