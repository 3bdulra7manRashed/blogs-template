import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                brand: {
                    primary: 'var(--writer-primary, #1f1f1f)',
                    secondary: 'var(--writer-secondary, #f5f0ea)',
                    accent: 'var(--writer-accent, #c37c54)',
                    muted: 'var(--writer-muted, #6f6f6f)',
                },
            },
            fontFamily: {
                sans: ['"Cairo"', ...defaultTheme.fontFamily.sans],
                serif: ['"Cairo"', ...defaultTheme.fontFamily.serif],
            },
            maxWidth: {
                prose: '70ch',
            },
            container: {
                center: true,
                padding: '1.5rem',
            },
        },
    },

    plugins: [forms, typography],
};
