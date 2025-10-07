<?php

namespace App\Helpers;

/**
 * Button Helper
 * 
 * Provides helper functions to generate standardized action buttons
 * with consistent colors and icons throughout the application.
 */
class ButtonHelper
{
    /**
     * Button type definitions with colors and icons
     */
    private static array $buttonTypes = [
        'create' => [
            'color' => 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500',
            'icon' => 'fa-solid fa-plus',
            'label' => 'Create',
        ],
        'edit' => [
            'color' => 'bg-amber-600 hover:bg-amber-700 focus:ring-amber-500',
            'icon' => 'fa-solid fa-pen',
            'label' => 'Edit',
        ],
        'delete' => [
            'color' => 'bg-rose-600 hover:bg-rose-700 focus:ring-rose-500',
            'icon' => 'fa-solid fa-trash',
            'label' => 'Delete',
        ],
        'view' => [
            'color' => 'bg-sky-600 hover:bg-sky-700 focus:ring-sky-500',
            'icon' => 'fa-regular fa-eye',
            'label' => 'View',
        ],
        'save' => [
            'color' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
            'icon' => 'fa-solid fa-floppy-disk',
            'label' => 'Save',
        ],
        'cancel' => [
            'color' => 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500',
            'icon' => 'fa-solid fa-xmark',
            'label' => 'Cancel',
        ],
        'back' => [
            'color' => 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500',
            'icon' => 'fa-solid fa-arrow-left',
            'label' => 'Back',
        ],
        'print' => [
            'color' => 'bg-purple-600 hover:bg-purple-700 focus:ring-purple-500',
            'icon' => 'fa-solid fa-print',
            'label' => 'Print',
        ],
        'export' => [
            'color' => 'bg-teal-600 hover:bg-teal-700 focus:ring-teal-500',
            'icon' => 'fa-solid fa-file-export',
            'label' => 'Export',
        ],
        'filter' => [
            'color' => 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500',
            'icon' => 'fa-solid fa-filter',
            'label' => 'Filter',
        ],
        'search' => [
            'color' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
            'icon' => 'fa-solid fa-magnifying-glass',
            'label' => 'Search',
        ],
        'approve' => [
            'color' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
            'icon' => 'fa-solid fa-check',
            'label' => 'Approve',
        ],
        'reject' => [
            'color' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
            'icon' => 'fa-solid fa-ban',
            'label' => 'Reject',
        ],
        'submit' => [
            'color' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
            'icon' => 'fa-solid fa-paper-plane',
            'label' => 'Submit',
        ],
        'download' => [
            'color' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
            'icon' => 'fa-solid fa-download',
            'label' => 'Download',
        ],
        'upload' => [
            'color' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
            'icon' => 'fa-solid fa-upload',
            'label' => 'Upload',
        ],
        'send' => [
            'color' => 'bg-cyan-600 hover:bg-cyan-700 focus:ring-cyan-500',
            'icon' => 'fa-solid fa-envelope',
            'label' => 'Send',
        ],
        'copy' => [
            'color' => 'bg-slate-600 hover:bg-slate-700 focus:ring-slate-500',
            'icon' => 'fa-solid fa-copy',
            'label' => 'Copy',
        ],
        'share' => [
            'color' => 'bg-violet-600 hover:bg-violet-700 focus:ring-violet-500',
            'icon' => 'fa-solid fa-share-nodes',
            'label' => 'Share',
        ],
        'refresh' => [
            'color' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
            'icon' => 'fa-solid fa-rotate',
            'label' => 'Refresh',
        ],
        'add' => [
            'color' => 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500',
            'icon' => 'fa-solid fa-plus',
            'label' => 'Add',
        ],
        'remove' => [
            'color' => 'bg-rose-600 hover:bg-rose-700 focus:ring-rose-500',
            'icon' => 'fa-solid fa-minus',
            'label' => 'Remove',
        ],
        'reset' => [
            'color' => 'bg-orange-600 hover:bg-orange-700 focus:ring-orange-500',
            'icon' => 'fa-solid fa-arrow-rotate-left',
            'label' => 'Reset',
        ],
        'settings' => [
            'color' => 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500',
            'icon' => 'fa-solid fa-gear',
            'label' => 'Settings',
        ],
    ];

    /**
     * Get button configuration by type
     *
     * @param string $type Button type
     * @return array|null Button configuration
     */
    public static function getButtonConfig(string $type): ?array
    {
        return self::$buttonTypes[$type] ?? null;
    }

    /**
     * Get all available button types
     *
     * @return array All button configurations
     */
    public static function getAllButtonTypes(): array
    {
        return self::$buttonTypes;
    }

    /**
     * Get button color classes by type
     *
     * @param string $type Button type
     * @return string Color classes
     */
    public static function getButtonColor(string $type): string
    {
        return self::$buttonTypes[$type]['color'] ?? 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500';
    }

    /**
     * Get button icon by type
     *
     * @param string $type Button type
     * @return string Icon class
     */
    public static function getButtonIcon(string $type): string
    {
        return self::$buttonTypes[$type]['icon'] ?? 'fa-solid fa-circle';
    }

    /**
     * Get button label by type
     *
     * @param string $type Button type
     * @return string Label text
     */
    public static function getButtonLabel(string $type): string
    {
        return self::$buttonTypes[$type]['label'] ?? 'Action';
    }

    /**
     * Register a custom button type
     *
     * @param string $type Button type name
     * @param string $color Tailwind color classes
     * @param string $icon Font Awesome icon class
     * @param string $label Button label
     * @return void
     */
    public static function registerButtonType(string $type, string $color, string $icon, string $label): void
    {
        self::$buttonTypes[$type] = [
            'color' => $color,
            'icon' => $icon,
            'label' => $label,
        ];
    }
}
