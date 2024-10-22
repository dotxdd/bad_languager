import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
               // retro: ['"Press Start 2P"', 'cursive'],  // Retro font
                retro: ['"Coda Caption"', 'sans-serif'], // Dodaj nową czcionkę retro
            },
        },
    },
    plugins: [],
}
