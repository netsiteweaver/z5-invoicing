# Action Button System - Quick Reference Card

## Basic Syntax

```blade
<x-action-button type="TYPE" :href="URL" />
<x-action-button type="TYPE" :form-action="URL" />
<x-action-button type="TYPE" />
```

## All Button Types (24)

| Type | Color | Usage |
|------|-------|-------|
| `create` | 🟢 Emerald | Create new |
| `edit` | 🟠 Amber | Edit existing |
| `delete` | 🔴 Rose | Delete item |
| `view` | 🔵 Sky | View details |
| `save` | 🔵 Blue | Save form |
| `cancel` | ⚫ Gray | Cancel action |
| `back` | ⚫ Gray | Go back |
| `print` | 🟣 Purple | Print |
| `export` | 🔵 Teal | Export data |
| `filter` | 🔵 Indigo | Filter list |
| `search` | 🔵 Blue | Search |
| `approve` | 🟢 Green | Approve |
| `reject` | 🔴 Red | Reject |
| `submit` | 🔵 Blue | Submit form |
| `download` | 🟢 Green | Download |
| `upload` | 🔵 Blue | Upload |
| `send` | 🔵 Cyan | Send email |
| `copy` | ⚫ Slate | Copy data |
| `share` | 🟣 Violet | Share |
| `refresh` | 🔵 Blue | Refresh |
| `add` | 🟢 Emerald | Add item |
| `remove` | 🔴 Rose | Remove item |
| `reset` | 🟠 Orange | Reset |
| `settings` | ⚫ Gray | Settings |

## Common Options

```blade
<!-- Custom label -->
<x-action-button type="edit" :href="...">Update Item</x-action-button>

<!-- Size variants -->
size="sm"   <!-- Small -->
size="md"   <!-- Medium (default) -->
size="lg"   <!-- Large -->

<!-- Icon only -->
:icon-only="true"

<!-- Custom confirmation -->
confirm-message="Are you sure?"

<!-- Disable confirmation -->
:confirm="false"
```

## Usage Examples

### Link Button
```blade
<x-action-button type="edit" :href="route('items.edit', $item)" />
```

### Delete Button
```blade
<x-action-button type="delete" :form-action="route('items.destroy', $item)" />
```

### Form Button
```blade
<x-action-button type="save" />
```

### CRUD Actions
```blade
<x-action-button type="view" :href="route('items.show', $item)" />
<x-action-button type="edit" :href="route('items.edit', $item)" />
<x-action-button type="delete" :form-action="route('items.destroy', $item)" />
```

### Form Actions
```blade
<x-action-button type="cancel" :href="route('items.index')" />
<x-action-button type="save" />
```

## Parameters Reference

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `type` | string | required | Button type |
| `href` | string | null | Link URL |
| `form-action` | string | null | Form action URL |
| `size` | string | 'md' | sm\|md\|lg |
| `icon-only` | bool | false | Icon only |
| `label` | string | null | Custom label |
| `confirm` | bool | true (delete) | Show confirm |
| `confirm-message` | string | default | Custom message |

## Quick Examples by Scenario

### Creating
```blade
<x-action-button type="create" :href="route('items.create')">Add New</x-action-button>
```

### Editing
```blade
<x-action-button type="edit" :href="route('items.edit', $item)" />
```

### Deleting
```blade
<x-action-button type="delete" :form-action="route('items.destroy', $item)" />
```

### Viewing
```blade
<x-action-button type="view" :href="route('items.show', $item)" />
```

### Saving
```blade
<x-action-button type="save" />
```

### Exporting
```blade
<x-action-button type="export">Export to Excel</x-action-button>
```

### Printing
```blade
<x-action-button type="print" />
```

### Mobile (Small, Icon Only)
```blade
<x-action-button type="edit" :href="..." size="sm" :icon-only="true" />
```

## Color Scheme

| Color | Hex | Uses |
|-------|-----|------|
| Emerald | #10b981 | Create, Add |
| Amber | #f59e0b | Edit |
| Rose | #f43f5e | Delete, Remove |
| Sky | #0ea5e9 | View |
| Blue | #3b82f6 | Save, Submit |
| Gray | #6b7280 | Cancel, Back |
| Purple | #a855f7 | Print |
| Teal | #14b8a6 | Export |
| Green | #22c55e | Approve, Download |
| Red | #ef4444 | Reject |
| Indigo | #6366f1 | Filter |
| Cyan | #06b6d4 | Send |
| Violet | #8b5cf6 | Share |
| Slate | #64748b | Copy |
| Orange | #f97316 | Reset |

## Files Location

```
/www/resources/views/components/action-button.blade.php
/www/app/Helpers/ButtonHelper.php
/docs/button-system.md
```

## Documentation

- 📖 Full docs: `/docs/button-system.md`
- 🔄 Migration: `/docs/button-migration-example.md`
- ⭐ Summary: `/BUTTON_SYSTEM_SUMMARY.md`
- 📑 Index: `/BUTTON_SYSTEM_INDEX.md`

---

**Print this card for quick reference!**
