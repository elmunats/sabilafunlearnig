import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                sabila: {
                    blue: '#2E6FBE',
                    blueLight: '#EBF3FE',
                    rose: '#FDECEC',
                    mint: '#EBF8F0',
                    amber: '#FEF6E0',
                    lavender: '#F3EEFD',
                    canvas: '#F2F5FA',
                },
            },
        },
    },
    plugins: [],
};
