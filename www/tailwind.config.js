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
        // Action button component colors - NEW modern color scheme
        // Green (create, add)
        'bg-green-600', 'hover:bg-green-700', 'active:bg-green-800', 'focus:ring-green-500',
        'bg-green-500', 'hover:bg-green-600', 'active:bg-green-700',
        // Yellow (edit)
        'bg-yellow-500', 'hover:bg-yellow-600', 'active:bg-yellow-700', 'focus:ring-yellow-500',
        // Red (delete)
        'bg-red-600', 'hover:bg-red-700', 'active:bg-red-800', 'focus:ring-red-500',
        // Blue (view, submit)
        'bg-blue-500', 'hover:bg-blue-600', 'active:bg-blue-700', 'focus:ring-blue-500',
        'bg-blue-600', 'hover:bg-blue-700', 'active:bg-blue-800',
        // Indigo (save)
        'bg-indigo-600', 'hover:bg-indigo-700', 'active:bg-indigo-800', 'focus:ring-indigo-500',
        // Gray (cancel)
        'bg-gray-500', 'hover:bg-gray-600', 'active:bg-gray-700', 'focus:ring-gray-500',
        // Slate (back)
        'bg-slate-600', 'hover:bg-slate-700', 'active:bg-slate-800', 'focus:ring-slate-500',
        // Purple (print)
        'bg-purple-600', 'hover:bg-purple-700', 'active:bg-purple-800', 'focus:ring-purple-500',
        // Teal (export)
        'bg-teal-600', 'hover:bg-teal-700', 'active:bg-teal-800', 'focus:ring-teal-500',
        // Violet (filter)
        'bg-violet-600', 'hover:bg-violet-700', 'active:bg-violet-800', 'focus:ring-violet-500',
        // Sky (search)
        'bg-sky-600', 'hover:bg-sky-700', 'active:bg-sky-800', 'focus:ring-sky-500',
        // Emerald (approve)
        'bg-emerald-600', 'hover:bg-emerald-700', 'active:bg-emerald-800', 'focus:ring-emerald-500',
        // Rose (reject)
        'bg-rose-600', 'hover:bg-rose-700', 'active:bg-rose-800', 'focus:ring-rose-500',
        // Lime (download)
        'bg-lime-600', 'hover:bg-lime-700', 'active:bg-lime-800', 'focus:ring-lime-500',
        // Cyan (upload)
        'bg-cyan-600', 'hover:bg-cyan-700', 'active:bg-cyan-800', 'focus:ring-cyan-500',
        // Fuchsia (send)
        'bg-fuchsia-600', 'hover:bg-fuchsia-700', 'active:bg-fuchsia-800', 'focus:ring-fuchsia-500',
        // Stone (copy)
        'bg-stone-600', 'hover:bg-stone-700', 'active:bg-stone-800', 'focus:ring-stone-500',
        // Pink (share)
        'bg-pink-600', 'hover:bg-pink-700', 'active:bg-pink-800', 'focus:ring-pink-500',
        // Amber (refresh)
        'bg-amber-600', 'hover:bg-amber-700', 'active:bg-amber-800', 'focus:ring-amber-500',
        // Orange (remove)
        'bg-orange-600', 'hover:bg-orange-700', 'active:bg-orange-800', 'focus:ring-orange-500',
        // Zinc (reset)
        'bg-zinc-600', 'hover:bg-zinc-700', 'active:bg-zinc-800', 'focus:ring-zinc-500',
        // Neutral (settings)
        'bg-neutral-600', 'hover:bg-neutral-700', 'active:bg-neutral-800', 'focus:ring-neutral-500',
        // Rounded options
        'rounded-none', 'rounded-md',
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
