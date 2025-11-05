/** @type {import('tailwindcss').Config} */
export default {
    theme: {
        extend: {
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'float-slow': {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-20px)' },
                },
                'gradient-move': {
                    '0%': { transform: 'translateX(-10%)' },
                    '50%': { transform: 'translateX(10%)' },
                    '100%': { transform: 'translateX(-10%)' },
                },
            },
            animation: {
                'fade-in': 'fadeIn 1s ease-in-out forwards',
                'float-slow': 'float-slow 6s ease-in-out infinite',
                'gradient-move': 'gradient-move 10s ease-in-out infinite',
            },
        },
    },
    plugins: [],
}
