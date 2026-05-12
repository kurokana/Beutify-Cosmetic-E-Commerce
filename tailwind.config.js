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
                'soft-pink': '#FCE4EC',   // Warna latar pink sangat muda
                'deep-pink': '#F06292',   // Warna tombol dan aksen utama
                'baby-blue': '#E1F5FE',   // Warna latar sekunder/hover
                'ocean-blue': '#039BE5',  // Warna teks link atau harga
            },
        },
    },

    plugins: [forms],
};