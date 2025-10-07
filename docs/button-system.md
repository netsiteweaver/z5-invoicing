# Action Button System

A comprehensive button component system for consistent UI/UX across the entire application. Each button type has predefined colors and icons, ensuring visual consistency and better user experience.

## Overview

The Action Button System provides:
- **Consistency**: All buttons of the same type look identical throughout the application
- **Predefined Types**: 24 button types with specific colors and icons
- **Easy to Use**: Simple component syntax with sensible defaults
- **Customizable**: Support for custom colors, icons, and labels
- **Responsive**: Three size options (sm, md, lg)
- **Accessible**: Proper focus states and semantic HTML

## Quick Start

### Basic Usage

```blade
<!-- Link Button -->
<x-action-button type="edit" :href="route('customers.edit', $customer)" />

<!-- Delete Button (with form) -->
<x-action-button type="delete" :form-action="route('customers.destroy', $customer)" />

<!-- Regular Button -->
<x-action-button type="save" />
```

## Available Button Types

| Type | Color | Icon | Default Label | Use Case |
|------|-------|------|---------------|----------|
| `create` | Emerald | fa-plus | Create | Creating new records |
| `edit` | Amber | fa-pen | Edit | Editing existing records |
| `delete` | Rose | fa-trash | Delete | Deleting records |
| `view` | Sky | fa-eye | View | Viewing details |
| `save` | Blue | fa-floppy-disk | Save | Saving forms |
| `cancel` | Gray | fa-xmark | Cancel | Canceling actions |
| `back` | Gray | fa-arrow-left | Back | Going back |
| `print` | Purple | fa-print | Print | Printing documents |
| `export` | Teal | fa-file-export | Export | Exporting data |
| `filter` | Indigo | fa-filter | Filter | Filtering lists |
| `search` | Blue | fa-magnifying-glass | Search | Searching |
| `approve` | Green | fa-check | Approve | Approving items |
| `reject` | Red | fa-ban | Reject | Rejecting items |
| `submit` | Blue | fa-paper-plane | Submit | Submitting forms |
| `download` | Green | fa-download | Download | Downloading files |
| `upload` | Blue | fa-upload | Upload | Uploading files |
| `send` | Cyan | fa-envelope | Send | Sending emails/messages |
| `copy` | Slate | fa-copy | Copy | Copying data |
| `share` | Violet | fa-share-nodes | Share | Sharing content |
| `refresh` | Blue | fa-rotate | Refresh | Refreshing data |
| `add` | Emerald | fa-plus | Add | Adding items |
| `remove` | Rose | fa-minus | Remove | Removing items |
| `reset` | Orange | fa-arrow-rotate-left | Reset | Resetting forms |
| `settings` | Gray | fa-gear | Settings | Settings/configuration |

## Usage Examples

### 1. Link Buttons

```blade
<x-action-button type="view" :href="route('customers.show', $customer)" />
<x-action-button type="edit" :href="route('customers.edit', $customer)" />
<x-action-button type="back" :href="route('customers.index')" />
```

### 2. Form Buttons

```blade
<!-- Submit button -->
<x-action-button type="save" />

<!-- Cancel button (link) -->
<x-action-button type="cancel" :href="route('customers.index')" />

<!-- Reset button -->
<x-action-button type="reset" type="reset" />
```

### 3. Delete Buttons (with automatic form generation)

```blade
<!-- Automatic confirmation dialog -->
<x-action-button 
    type="delete" 
    :form-action="route('customers.destroy', $customer)" 
/>

<!-- Custom confirmation message -->
<x-action-button 
    type="delete" 
    :form-action="route('customers.destroy', $customer)"
    confirm-message="Are you sure you want to delete this customer? All related data will be lost."
/>

<!-- Disable confirmation -->
<x-action-button 
    type="delete" 
    :form-action="route('customers.destroy', $customer)"
    :confirm="false"
/>
```

### 4. Custom Labels

```blade
<x-action-button type="create" :href="route('customers.create')">
    Add New Customer
</x-action-button>

<x-action-button type="save">
    Save Changes
</x-action-button>

<x-action-button type="export">
    Export to Excel
</x-action-button>
```

### 5. Button Sizes

```blade
<!-- Small -->
<x-action-button type="edit" size="sm" :href="route('customers.edit', $customer)" />

<!-- Medium (default) -->
<x-action-button type="edit" size="md" :href="route('customers.edit', $customer)" />

<!-- Large -->
<x-action-button type="edit" size="lg" :href="route('customers.edit', $customer)" />
```

### 6. Icon Only Buttons

```blade
<x-action-button type="edit" :href="route('customers.edit', $customer)" :icon-only="true" />
<x-action-button type="delete" :form-action="route('customers.destroy', $customer)" :icon-only="true" />
```

### 7. Custom Buttons

```blade
<x-action-button 
    type="custom" 
    color="bg-pink-600 hover:bg-pink-700 focus:ring-pink-500" 
    icon="fa-solid fa-heart"
    :href="route('favorites.add', $item)">
    Add to Favorites
</x-action-button>
```

### 8. With Additional Attributes

```blade
<!-- Alpine.js -->
<x-action-button 
    type="save" 
    x-on:click="submitForm()"
    id="save-button"
/>

<!-- Disabled -->
<x-action-button 
    type="save" 
    disabled
/>

<!-- With custom classes -->
<x-action-button 
    type="edit" 
    :href="route('customers.edit', $customer)"
    class="my-custom-class"
/>
```

## Component Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `type` | string | required | Button type (see table above) |
| `href` | string | null | URL for link-style button |
| `form-action` | string | null | Form action URL for delete/submit buttons |
| `color` | string | null | Custom color classes (for type="custom") |
| `icon` | string | null | Custom icon class (for type="custom" or override) |
| `label` | string | null | Custom label (overrides default) |
| `confirm` | boolean | true for delete | Show confirmation dialog |
| `confirm-message` | string | default message | Custom confirmation message |
| `size` | string | 'md' | Button size: 'sm', 'md', 'lg' |
| `icon-only` | boolean | false | Show only icon without text |

## Real World Examples

### Customer List Actions

```blade
<div class="flex space-x-2">
    <x-action-button type="view" :href="route('customers.show', $customer)" />
    
    @can('edit', $customer)
        <x-action-button type="edit" :href="route('customers.edit', $customer)" />
    @endcan
    
    @can('delete', $customer)
        <x-action-button type="delete" :form-action="route('customers.destroy', $customer)" />
    @endcan
</div>
```

### Form Actions

```blade
<form method="POST" action="{{ route('customers.update', $customer) }}">
    @csrf
    @method('PUT')
    
    <!-- Form fields here -->
    
    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
        <x-action-button type="cancel" :href="route('customers.index')" />
        <x-action-button type="save" />
    </div>
</form>
```

### Header Actions

```blade
<div class="flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Customers</h1>
    </div>
    <div class="flex space-x-2">
        <x-action-button type="export">Export to Excel</x-action-button>
        <x-action-button type="create" :href="route('customers.create')" />
    </div>
</div>
```

### Mobile Actions (Icon Only)

```blade
<div class="flex space-x-2 sm:hidden">
    <x-action-button type="view" :href="route('customers.show', $customer)" :icon-only="true" />
    <x-action-button type="edit" :href="route('customers.edit', $customer)" :icon-only="true" />
    <x-action-button type="delete" :form-action="route('customers.destroy', $customer)" :icon-only="true" />
</div>
```

## Migration Guide

### Before (Old Way)

```blade
<a href="{{ route('customers.edit', $customer) }}" class="btn btn-edit">
    <i class="btn-icon fa-solid fa-pen"></i>
    Edit
</a>

<form method="POST" action="{{ route('customers.destroy', $customer) }}" 
      onsubmit="return confirm('Are you sure?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-delete">
        <i class="btn-icon fa-solid fa-trash"></i>
        Delete
    </button>
</form>
```

### After (New Way)

```blade
<x-action-button type="edit" :href="route('customers.edit', $customer)" />

<x-action-button type="delete" :form-action="route('customers.destroy', $customer)" />
```

## PHP Helper (Optional)

For cases where you need button configuration in PHP code:

```php
use App\Helpers\ButtonHelper;

// Get button configuration
$config = ButtonHelper::getButtonConfig('edit');
// Returns: ['color' => '...', 'icon' => '...', 'label' => 'Edit']

// Get specific properties
$color = ButtonHelper::getButtonColor('edit');
$icon = ButtonHelper::getButtonIcon('edit');
$label = ButtonHelper::getButtonLabel('edit');

// Get all button types
$allTypes = ButtonHelper::getAllButtonTypes();

// Register custom button type
ButtonHelper::registerButtonType(
    'archive',
    'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500',
    'fa-solid fa-box-archive',
    'Archive'
);
```

## Color Reference

| Color | Tailwind Classes | Use Case |
|-------|------------------|----------|
| Emerald | `bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500` | Create, Add |
| Amber | `bg-amber-600 hover:bg-amber-700 focus:ring-amber-500` | Edit |
| Rose/Red | `bg-rose-600 hover:bg-rose-700 focus:ring-rose-500` | Delete, Reject, Remove |
| Sky | `bg-sky-600 hover:bg-sky-700 focus:ring-sky-500` | View |
| Blue | `bg-blue-600 hover:bg-blue-700 focus:ring-blue-500` | Save, Submit, Upload, Search, Refresh |
| Gray | `bg-gray-600 hover:bg-gray-700 focus:ring-gray-500` | Cancel, Back, Settings |
| Purple | `bg-purple-600 hover:bg-purple-700 focus:ring-purple-500` | Print |
| Teal | `bg-teal-600 hover:bg-teal-700 focus:ring-teal-500` | Export |
| Indigo | `bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500` | Filter |
| Green | `bg-green-600 hover:bg-green-700 focus:ring-green-500` | Approve, Download |
| Cyan | `bg-cyan-600 hover:bg-cyan-700 focus:ring-cyan-500` | Send |
| Violet | `bg-violet-600 hover:bg-violet-700 focus:ring-violet-500` | Share |
| Slate | `bg-slate-600 hover:bg-slate-700 focus:ring-slate-500` | Copy |
| Orange | `bg-orange-600 hover:bg-orange-700 focus:ring-orange-500` | Reset |

## Best Practices

1. **Use Semantic Types**: Choose button types that match the action (e.g., `edit` for editing, `delete` for deleting)

2. **Consistent Placement**: Place similar buttons in the same order across pages (e.g., View → Edit → Delete)

3. **Mobile Considerations**: Use `icon-only` for space-constrained mobile views

4. **Confirmation Dialogs**: Keep default confirmation for destructive actions (delete)

5. **Custom Labels**: Use custom labels for specific contexts while keeping the same type for consistency

6. **Permission Checks**: Wrap buttons in permission checks using `@can` directives

7. **Form Context**: Use appropriate button types in forms (save, cancel, reset)

## Demo Page

To see all button types in action, visit the demo page:

```
/demo/buttons
```

*(You need to add a route for this in your routes file)*

## Files

- Component: `/resources/views/components/action-button.blade.php`
- Helper: `/app/Helpers/ButtonHelper.php`
- Reference: `/resources/views/components/button-reference.blade.php`
- Demo: `/resources/views/demo/buttons.blade.php`
- Documentation: `/docs/button-system.md`

## Support

For issues or feature requests related to the button system, please contact the development team.
