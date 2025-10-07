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
        // Action button component colors - use pattern matching for efficiency
        {
            pattern: /bg-(green|yellow|red|blue|indigo|gray|slate|purple|teal|violet|sky|emerald|rose|lime|cyan|fuchsia|stone|pink|amber|orange|zinc|neutral)-(500|600|700|800)/,
            variants: ['hover', 'active', 'focus'],
        },
        {
            pattern: /ring-(green|yellow|red|blue|indigo|gray|slate|purple|teal|violet|sky|emerald|rose|lime|cyan|fuchsia|stone|pink|amber|orange|zinc|neutral)-500/,
            variants: ['focus'],
        },
        // Rounded options
        'rounded-none', 
        'rounded-md',
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
