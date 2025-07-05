/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{js,ts,jsx,tsx}'],
  theme: {
    extend: {
      colors: {
        // VITA Brand Colors
        'vita-blue': '#4A90E2',
        'vita-mint': '#7ED6A5',
        'vita-white': '#F8F9FA',
        'vita-grey': '#D6D9DF',
        'vita-coral': '#E74C3C',
        // Extended palette for variations
        'vita-blue-light': '#6BA3E8',
        'vita-blue-dark': '#3A7BC8',
        'vita-mint-light': '#9BDEB8',
        'vita-mint-dark': '#5CC285',
        'vita-grey-light': '#E8EAED',
        'vita-grey-dark': '#B8BCC4',
      },
      fontFamily: {
        'sans': ['Inter', 'system-ui', 'sans-serif'],
      },
      borderRadius: {
        'xl': '1rem',
        '2xl': '1.5rem',
      },
      boxShadow: {
        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
        'soft-lg': '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
      }
    },
  },
  plugins: [],
};