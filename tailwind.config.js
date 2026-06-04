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
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Dark backgrounds
                'dark-primary': '#0F0F14',
                'dark-secondary': '#1A1A24',
                'dark-tertiary': '#24243A',
                'dark-elevated': '#1E1E2E',
                // Rose gold accents
                'gold': '#C8956C',
                'gold-light': '#D4A87C',
                'gold-dark': '#A87A55',
                // Pink accent (secondary)
                'rose-accent': '#E86FA3',
                // Text
                'warm-white': '#F0ECE6',
                'warm-gray': '#9B97A0',
                'warm-muted': '#5E5A66',
                // Borders
                'border-subtle': '#2A2A3E',
            },
            boxShadow: {
                'gold-sm': '0 2px 10px rgba(200, 149, 108, 0.15)',
                'gold-md': '0 8px 30px rgba(200, 149, 108, 0.12)',
                'gold-lg': '0 15px 40px rgba(200, 149, 108, 0.2)',
                'gold-glow': '0 0 30px rgba(200, 149, 108, 0.15), 0 0 60px rgba(200, 149, 108, 0.05)',
                'dark-card': '0 14px 35px rgba(0, 0, 0, 0.3)',
                'dark-card-hover': '0 20px 45px rgba(0, 0, 0, 0.4)',
            },
            ringColor: {
                'gold': 'rgba(200, 149, 108, 0.4)',
            },
        },
    },

    plugins: [forms],
};