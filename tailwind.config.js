/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
    ],
    theme: {
        extend: {
            colors: {
                "primary": "#d47311",
                "primary-hover": "#b05d0e",
                "cream": "#f8f7f6",
                "background-light": "#f8f7f6",
                "background-dark": "#221910",
                "surface-light": "#ffffff",
                "surface-dark": "#2c241b", // standardized to user's card-dark
                "card-light": "#ffffff",
                "card-dark": "#2c241b",
                "text-main": "#181411", // alias for text-main-light
                "text-main-light": "#181411",
                "text-main-dark": "#f4f2f0",
                "text-sec-light": "#897561",
                "text-sec-dark": "#b0a090",
                "text-secondary": "#897561", // alias

                // Existing aliases for backward compatibility
                "background": {
                    light: "#f8f7f6",
                    dark: "#221910",
                },
                "surface": {
                    light: "#ffffff",
                    dark: "#2c2016", // kept for slight variation if needed, but preferred user's surface-dark
                },
                "coffee": {
                    50: '#fdf8f6',
                    100: '#f9ebe5',
                    200: '#f3d5c8',
                    300: '#e9b8a1',
                    400: '#dc9575',
                    500: '#c97850',
                    600: '#b86342',
                    700: '#9a4f36',
                    800: '#7d4230',
                    900: '#66382b',
                },
            },
            borderRadius: {
                'lg': '0.5rem',
                'xl': '0.75rem',
                '2xl': '1rem',
            },
            boxShadow: {
                'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                'hover': '0 10px 25px -5px rgba(212, 115, 17, 0.15)',
            },
            fontFamily: {
                sans: ['Inter', 'system-ui', 'sans-serif'],
                display: ['Playfair Display', 'serif'],
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-out',
                'slide-up': 'slideUp 0.5s ease-out',
                'slide-down': 'slideDown 0.3s ease-out',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideDown: {
                    '0%': { opacity: '0', transform: 'translateY(-10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },
    plugins: [],
}
