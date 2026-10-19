import forms from '@tailwindcss/forms';
import { tailwindTokens } from './resources/js/tailwind-tokens.js';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: tailwindTokens,
    },

    plugins: [forms],
};
