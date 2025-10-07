@php
/**
 * Action Button Component
 * 
 * A standardized button component with predefined types, colors, and icons.
 * This ensures visual consistency across the entire application.
 * 
 * Usage:
 * <x-action-button type="edit" :href="route('customers.edit', $customer)" />
 * <x-action-button type="delete" form-action="{{ route('customers.destroy', $customer) }}" />
 * <x-action-button type="save" :rounded="true" />
 * <x-action-button type="custom" color="purple" icon="fa-star">Custom Action</x-action-button>
 * 
 * @param string $type - Button type (create|edit|delete|view|save|cancel|back|print|export|filter|search|approve|reject|submit|download|upload|send|copy|share|refresh|custom)
 * @param string|null $href - URL for link-style button
 * @param string|null $formAction - Form action URL for delete/submit buttons
 * @param string|null $color - Custom color (for type="custom")
 * @param string|null $icon - Custom icon (for type="custom")
 * @param string|null $label - Custom label (overrides default)
 * @param bool $confirm - Show confirmation dialog (default: true for delete)
 * @param string|null $confirmMessage - Custom confirmation message
 * @param string $size - Button size (sm|md|lg) default: md
 * @param bool $iconOnly - Show only icon without text
 * @param bool $rounded - Rounded corners (default: false = square corners)
 */

// Button type definitions with NEW modern colors and icons
$buttonTypes = [
    'create' => [
        'color' => 'bg-green-600 hover:bg-green-700 active:bg-green-800 focus:ring-green-500',
        'icon' => 'fa-solid fa-plus',
        'label' => 'Create',
    ],
    'edit' => [
        'color' => 'bg-yellow-500 hover:bg-yellow-600 active:bg-yellow-700 focus:ring-yellow-500',
        'icon' => 'fa-solid fa-pen',
        'label' => 'Edit',
    ],
    'delete' => [
        'color' => 'bg-red-600 hover:bg-red-700 active:bg-red-800 focus:ring-red-500',
        'icon' => 'fa-solid fa-trash',
        'label' => 'Delete',
    ],
    'view' => [
        'color' => 'bg-blue-500 hover:bg-blue-600 active:bg-blue-700 focus:ring-blue-500',
        'icon' => 'fa-regular fa-eye',
        'label' => 'View',
    ],
    'save' => [
        'color' => 'bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 focus:ring-indigo-500',
        'icon' => 'fa-solid fa-floppy-disk',
        'label' => 'Save',
    ],
    'cancel' => [
        'color' => 'bg-gray-500 hover:bg-gray-600 active:bg-gray-700 focus:ring-gray-500',
        'icon' => 'fa-solid fa-xmark',
        'label' => 'Cancel',
    ],
    'back' => [
        'color' => 'bg-slate-600 hover:bg-slate-700 active:bg-slate-800 focus:ring-slate-500',
        'icon' => 'fa-solid fa-arrow-left',
        'label' => 'Back',
    ],
    'print' => [
        'color' => 'bg-purple-600 hover:bg-purple-700 active:bg-purple-800 focus:ring-purple-500',
        'icon' => 'fa-solid fa-print',
        'label' => 'Print',
    ],
    'export' => [
        'color' => 'bg-teal-600 hover:bg-teal-700 active:bg-teal-800 focus:ring-teal-500',
        'icon' => 'fa-solid fa-file-export',
        'label' => 'Export',
    ],
    'filter' => [
        'color' => 'bg-violet-600 hover:bg-violet-700 active:bg-violet-800 focus:ring-violet-500',
        'icon' => 'fa-solid fa-filter',
        'label' => 'Filter',
    ],
    'search' => [
        'color' => 'bg-sky-600 hover:bg-sky-700 active:bg-sky-800 focus:ring-sky-500',
        'icon' => 'fa-solid fa-magnifying-glass',
        'label' => 'Search',
    ],
    'approve' => [
        'color' => 'bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 focus:ring-emerald-500',
        'icon' => 'fa-solid fa-check',
        'label' => 'Approve',
    ],
    'reject' => [
        'color' => 'bg-rose-600 hover:bg-rose-700 active:bg-rose-800 focus:ring-rose-500',
        'icon' => 'fa-solid fa-ban',
        'label' => 'Reject',
    ],
    'submit' => [
        'color' => 'bg-blue-600 hover:bg-blue-700 active:bg-blue-800 focus:ring-blue-500',
        'icon' => 'fa-solid fa-paper-plane',
        'label' => 'Submit',
    ],
    'download' => [
        'color' => 'bg-lime-600 hover:bg-lime-700 active:bg-lime-800 focus:ring-lime-500',
        'icon' => 'fa-solid fa-download',
        'label' => 'Download',
    ],
    'upload' => [
        'color' => 'bg-cyan-600 hover:bg-cyan-700 active:bg-cyan-800 focus:ring-cyan-500',
        'icon' => 'fa-solid fa-upload',
        'label' => 'Upload',
    ],
    'send' => [
        'color' => 'bg-fuchsia-600 hover:bg-fuchsia-700 active:bg-fuchsia-800 focus:ring-fuchsia-500',
        'icon' => 'fa-solid fa-envelope',
        'label' => 'Send',
    ],
    'copy' => [
        'color' => 'bg-stone-600 hover:bg-stone-700 active:bg-stone-800 focus:ring-stone-500',
        'icon' => 'fa-solid fa-copy',
        'label' => 'Copy',
    ],
    'share' => [
        'color' => 'bg-pink-600 hover:bg-pink-700 active:bg-pink-800 focus:ring-pink-500',
        'icon' => 'fa-solid fa-share-nodes',
        'label' => 'Share',
    ],
    'refresh' => [
        'color' => 'bg-amber-600 hover:bg-amber-700 active:bg-amber-800 focus:ring-amber-500',
        'icon' => 'fa-solid fa-rotate',
        'label' => 'Refresh',
    ],
    'add' => [
        'color' => 'bg-green-500 hover:bg-green-600 active:bg-green-700 focus:ring-green-500',
        'icon' => 'fa-solid fa-plus',
        'label' => 'Add',
    ],
    'remove' => [
        'color' => 'bg-orange-600 hover:bg-orange-700 active:bg-orange-800 focus:ring-orange-500',
        'icon' => 'fa-solid fa-minus',
        'label' => 'Remove',
    ],
    'reset' => [
        'color' => 'bg-zinc-600 hover:bg-zinc-700 active:bg-zinc-800 focus:ring-zinc-500',
        'icon' => 'fa-solid fa-arrow-rotate-left',
        'label' => 'Reset',
    ],
    'settings' => [
        'color' => 'bg-neutral-600 hover:bg-neutral-700 active:bg-neutral-800 focus:ring-neutral-500',
        'icon' => 'fa-solid fa-gear',
        'label' => 'Settings',
    ],
];

// Get button configuration
$type = $type ?? 'custom';
$config = $buttonTypes[$type] ?? null;

// Handle custom type
if ($type === 'custom' || !$config) {
    $config = [
        'color' => $color ?? 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500',
        'icon' => $icon ?? 'fa-solid fa-circle',
        'label' => $label ?? 'Action',
    ];
} else {
    // Allow overriding default label
    if ($label ?? false) {
        $config['label'] = $label;
    }
    // Allow overriding default icon
    if ($icon ?? false) {
        $config['icon'] = $icon;
    }
}

// Button size classes
$sizeClasses = [
    'sm' => 'h-8 px-3 text-xs',
    'md' => 'h-10 px-4 text-sm',
    'lg' => 'h-12 px-6 text-base',
];
$size = $size ?? 'md';
$sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];

// Icon size classes
$iconSizeClasses = [
    'sm' => 'text-sm',
    'md' => 'text-base',
    'lg' => 'text-lg',
];
$iconSizeClass = $iconSizeClasses[$size] ?? $iconSizeClasses['md'];

// Rounded option (default: false = square corners)
$rounded = $rounded ?? false;
$roundedClass = $rounded ? 'rounded-md' : 'rounded-none';

// Build base classes
$baseClasses = "inline-flex justify-center items-center {$sizeClass} border border-transparent shadow-sm font-medium {$roundedClass} text-white focus:outline-none focus:ring-2 focus:ring-offset-2 whitespace-nowrap {$config['color']}";

// Handle confirmation for delete buttons
$confirm = $confirm ?? ($type === 'delete');
$confirmMessage = $confirmMessage ?? 'Are you sure you want to delete this item? This action cannot be undone.';

// Determine if this is a form button (delete, submit actions)
$isFormButton = isset($formAction) || $type === 'delete';

// Icon only mode
$iconOnly = $iconOnly ?? false;
$iconClass = $iconOnly ? '' : 'btn-icon';
@endphp

@if($isFormButton)
    {{-- Form Button (for delete, submit actions) --}}
    <form method="POST" action="{{ $formAction }}" class="inline" {!! $confirm ? "onsubmit=\"return confirm('{$confirmMessage}')\"" : '' !!}>
        @csrf
        @method('DELETE')
        <button type="submit" class="{{ $baseClasses }}" {{ $attributes }}>
            <i class="{{ $config['icon'] }} {{ $iconClass }} {{ $iconSizeClass }}"></i>
            @if(!$iconOnly)
                {{ $slot->isEmpty() ? $config['label'] : $slot }}
            @endif
        </button>
    </form>
@elseif(isset($href))
    {{-- Link Button --}}
    <a href="{{ $href }}" class="{{ $baseClasses }}" {{ $attributes }}>
        <i class="{{ $config['icon'] }} {{ $iconClass }} {{ $iconSizeClass }}"></i>
        @if(!$iconOnly)
            {{ $slot->isEmpty() ? $config['label'] : $slot }}
        @endif
    </a>
@else
    {{-- Regular Button --}}
    <button type="{{ $attributes->get('type', 'button') }}" class="{{ $baseClasses }}" {{ $attributes }}>
        <i class="{{ $config['icon'] }} {{ $iconClass }} {{ $iconSizeClass }}"></i>
        @if(!$iconOnly)
            {{ $slot->isEmpty() ? $config['label'] : $slot }}
        @endif
    </button>
@endif
