# Action Button Component

A standardized button component system for consistent UI/UX across the application.

## Quick Reference

### Basic Usage

```blade
<!-- Link Button -->
<x-action-button type="edit" :href="route('customers.edit', $customer)" />

<!-- Form Delete Button -->
<x-action-button type="delete" :form-action="route('customers.destroy', $customer)" />

<!-- Regular Button -->
<x-action-button type="save" />
```

### Available Types

| Type | Color | Icon |
|------|-------|------|
| `create` | Emerald | fa-plus |
| `edit` | Amber | fa-pen |
| `delete` | Rose | fa-trash |
| `view` | Sky | fa-eye |
| `save` | Blue | fa-floppy-disk |
| `cancel` | Gray | fa-xmark |
| `back` | Gray | fa-arrow-left |
| `print` | Purple | fa-print |
| `export` | Teal | fa-file-export |
| `filter` | Indigo | fa-filter |
| `search` | Blue | fa-magnifying-glass |
| `approve` | Green | fa-check |
| `reject` | Red | fa-ban |
| `submit` | Blue | fa-paper-plane |
| `download` | Green | fa-download |
| `upload` | Blue | fa-upload |
| `send` | Cyan | fa-envelope |
| `copy` | Slate | fa-copy |
| `share` | Violet | fa-share-nodes |
| `refresh` | Blue | fa-rotate |
| `add` | Emerald | fa-plus |
| `remove` | Rose | fa-minus |
| `reset` | Orange | fa-arrow-rotate-left |
| `settings` | Gray | fa-gear |

### Options

```blade
<!-- Custom Label -->
<x-action-button type="edit" :href="...">Update Customer</x-action-button>

<!-- Size -->
<x-action-button type="edit" size="sm|md|lg" :href="..." />

<!-- Icon Only -->
<x-action-button type="edit" :icon-only="true" :href="..." />

<!-- Custom Button -->
<x-action-button 
    type="custom" 
    color="bg-pink-600 hover:bg-pink-700 focus:ring-pink-500" 
    icon="fa-heart">
    Favorite
</x-action-button>
```

## Documentation

- Full docs: `/docs/button-system.md`
- Migration guide: `/docs/button-migration-example.md`
- Demo page: `/resources/views/demo/buttons.blade.php`
- Reference: `/resources/views/components/button-reference.blade.php`

## Files

- Component: `action-button.blade.php`
- Helper: `/app/Helpers/ButtonHelper.php`
- Provider: `/app/Providers/AppServiceProvider.php` (Blade directive registration)
