const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class', // Enables manual dark mode toggling

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        "./resources/**/*.blade.php",  // Laravel Blade templates
        "./resources/**/*.js",         // Any JS inside your resources folder
        "./resources/**/*.vue",        // (if using Vue)
        './node_modules/flowbite/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                primary: {
                    light: '#93C5FD', //White
                    DEFAULT: '#2A6DFA', //Bright Blue
                    dark: '#081032', //Dark Blue
                },
                buttons: {
                    lightHover: '#D4D4D4', //light colored button
                    defaultHover: '#003FC5', // blue colored button
                    darkHover: '#002676', //dark colored button
                    defaultDisabled: '#D9D9D9'
                },
                badges: {
                    complete: '#2FBC00',
                    progress: '#FFBF00',
                    incomplete: '#FF1823'
                },
            },
            fontFamily: {
                sans: ['FamiljenGrotesk', ...defaultTheme.fontFamily.sans], // use your font as default
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('flowbite/plugin'),
    ],
};
