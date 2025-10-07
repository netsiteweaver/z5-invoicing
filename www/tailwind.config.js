import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import aspectRatio from '@tailwindcss/aspect-ratio';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        // Action button component colors - ensure these are always generated
        // Emerald (create, add)
        'bg-emerald-600', 'hover:bg-emerald-700', 'focus:ring-emerald-500',
        // Amber (edit)
        'bg-amber-600', 'hover:bg-amber-700', 'focus:ring-amber-500',
        // Rose (delete, remove)
        'bg-rose-600', 'hover:bg-rose-700', 'focus:ring-rose-500',
        // Sky (view)
        'bg-sky-600', 'hover:bg-sky-700', 'focus:ring-sky-500',
        // Blue (save, submit, search, refresh, upload)
        'bg-blue-600', 'hover:bg-blue-700', 'focus:ring-blue-500',
        // Gray (cancel, back, settings)
        'bg-gray-600', 'hover:bg-gray-700', 'focus:ring-gray-500',
        // Purple (print)
        'bg-purple-600', 'hover:bg-purple-700', 'focus:ring-purple-500',
        // Teal (export)
        'bg-teal-600', 'hover:bg-teal-700', 'focus:ring-teal-500',
        // Indigo (filter)
        'bg-indigo-600', 'hover:bg-indigo-700', 'focus:ring-indigo-500',
        // Green (approve, download)
        'bg-green-600', 'hover:bg-green-700', 'focus:ring-green-500',
        // Red (reject)
        'bg-red-600', 'hover:bg-red-700', 'focus:ring-red-500',
        // Cyan (send)
        'bg-cyan-600', 'hover:bg-cyan-700', 'focus:ring-cyan-500',
        // Slate (copy)
        'bg-slate-600', 'hover:bg-slate-700', 'focus:ring-slate-500',
        // Violet (share)
        'bg-violet-600', 'hover:bg-violet-700', 'focus:ring-violet-500',
        // Orange (reset)
        'bg-orange-600', 'hover:bg-orange-700', 'focus:ring-orange-500',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms, typography, aspectRatio],
};
