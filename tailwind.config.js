import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // BCA brand palette — BCA Blue #0060AF dengan turunan biru muda
                gista: {
                    50: '#eff6fd',
                    100: '#d9ebfa',
                    200: '#b6d7f5',
                    300: '#83bced',
                    400: '#4a9ce0',
                    500: '#1d7fd0',
                    600: '#0060af',
                    700: '#005293',
                    800: '#074678',
                    900: '#0b3a60',
                    950: '#06263f',
                },
            },
            keyframes: {
                'slide-in': {
                    '0%': { transform: 'translateX(100%)', opacity: '0' },
                    '100%': { transform: 'translateX(0)', opacity: '1' },
                },
            },
            animation: {
                'slide-in': 'slide-in 0.3s ease-out',
            },
        },
    },

    plugins: [forms],
};
