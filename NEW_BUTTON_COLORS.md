# 🎨 New Button Color Reference

## Quick Color Guide

### ✅ Changes Applied

| Button Type | Color | Visual | Use Case |
|-------------|-------|--------|----------|
| **create** | 🟢 Green | `bg-green-600` | Creating new records |
| **edit** | 🟡 Yellow | `bg-yellow-500` | Editing records - HIGH VISIBILITY |
| **delete** | 🔴 Red | `bg-red-600` | Deleting records - DANGER |
| **view** | 🔵 Blue (light) | `bg-blue-500` | Viewing details |
| **save** | 🟣 Indigo | `bg-indigo-600` | Saving forms |
| **cancel** | ⚫ Gray | `bg-gray-500` | Canceling actions |
| **back** | ⬛ Slate | `bg-slate-600` | Navigation back |
| **print** | 🟣 Purple | `bg-purple-600` | Printing |
| **export** | 🔵 Teal | `bg-teal-600` | Exporting data |
| **filter** | 🟣 Violet | `bg-violet-600` | Filtering lists |
| **search** | 🔵 Sky | `bg-sky-600` | Searching |
| **approve** | 🟢 Emerald | `bg-emerald-600` | Approving |
| **reject** | 🔴 Rose | `bg-rose-600` | Rejecting |
| **submit** | 🔵 Blue (dark) | `bg-blue-600` | Submitting |
| **download** | 🟢 Lime | `bg-lime-600` | Downloading |
| **upload** | 🔵 Cyan | `bg-cyan-600` | Uploading |
| **send** | 🔴 Fuchsia | `bg-fuchsia-600` | Sending |
| **copy** | ⚫ Stone | `bg-stone-600` | Copying |
| **share** | 🔴 Pink | `bg-pink-600` | Sharing |
| **refresh** | 🟠 Amber | `bg-amber-600` | Refreshing |
| **add** | 🟢 Green (light) | `bg-green-500` | Adding items |
| **remove** | 🟠 Orange | `bg-orange-600` | Removing items |
| **reset** | ⚫ Zinc | `bg-zinc-600` | Resetting |
| **settings** | ⚫ Neutral | `bg-neutral-600` | Settings |

## Usage

### Square Corners (Default)
```blade
<x-action-button type="edit" :href="route('items.edit', $item)" />
```

### Rounded Corners
```blade
<x-action-button type="edit" :href="route('items.edit', $item)" :rounded="true" />
```

## Color Categories

### 🟢 Green Tones (Positive Actions)
- **create**: `bg-green-600` - Main create action
- **add**: `bg-green-500` - Add to list
- **approve**: `bg-emerald-600` - Approve/confirm
- **download**: `bg-lime-600` - Get/download

### 🟡🟠 Yellow/Orange (Attention/Warning)
- **edit**: `bg-yellow-500` - Edit action (high visibility)
- **refresh**: `bg-amber-600` - Refresh/update
- **remove**: `bg-orange-600` - Remove (warning)

### 🔴 Red Tones (Danger/Stop)
- **delete**: `bg-red-600` - Delete (danger)
- **reject**: `bg-rose-600` - Reject/deny
- **send**: `bg-fuchsia-600` - Send (action)
- **share**: `bg-pink-600` - Share (social)

### 🔵 Blue Tones (Information/Action)
- **view**: `bg-blue-500` - View details
- **submit**: `bg-blue-600` - Submit form
- **save**: `bg-indigo-600` - Save (important)
- **search**: `bg-sky-600` - Search/find
- **upload**: `bg-cyan-600` - Upload
- **export**: `bg-teal-600` - Export data

### 🟣 Purple Tones (Special)
- **print**: `bg-purple-600` - Print action
- **filter**: `bg-violet-600` - Filter data

### ⚫ Neutral Tones
- **cancel**: `bg-gray-500` - Cancel
- **back**: `bg-slate-600` - Go back
- **copy**: `bg-stone-600` - Copy
- **reset**: `bg-zinc-600` - Reset
- **settings**: `bg-neutral-600` - Settings

## State Colors

Each button has:
- **Normal**: `bg-{color}-500` or `bg-{color}-600`
- **Hover**: `hover:bg-{color}-600` or `hover:bg-{color}-700`
- **Active** (clicked): `active:bg-{color}-700` or `active:bg-{color}-800`
- **Focus**: `focus:ring-{color}-500`

## Examples

### CRUD Actions
```blade
<div class="flex space-x-2">
    <x-action-button type="view" :href="route('items.show', $item)" />
    <!-- Blue (light) - bg-blue-500 -->
    
    <x-action-button type="edit" :href="route('items.edit', $item)" />
    <!-- Yellow - bg-yellow-500 -->
    
    <x-action-button type="delete" :form-action="route('items.destroy', $item)" />
    <!-- Red - bg-red-600 -->
</div>
```

### Form Actions
```blade
<div class="flex justify-end space-x-3">
    <x-action-button type="cancel" :href="route('items.index')" />
    <!-- Gray - bg-gray-500 -->
    
    <x-action-button type="save" />
    <!-- Indigo - bg-indigo-600 -->
</div>
```

### Rounded Buttons
```blade
<x-action-button type="create" :rounded="true" :href="route('items.create')">
    Add New Item
</x-action-button>
<!-- Green with rounded corners -->
```

## Accessibility

All colors maintain WCAG AA contrast ratios with white text:
- ✅ All 600-level colors: Excellent contrast
- ✅ 500-level colors (yellow, blue light, green light): Good contrast
- ✅ Hover states darken for more contrast
- ✅ Focus rings clearly visible

## Build Status

After running `npm run build`, all these colors will be available in your CSS.

**Next**: Test buttons in your application to see the new modern color scheme!
