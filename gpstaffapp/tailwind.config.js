/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./App.{js,jsx,ts,tsx}",
    "./src/**/*.{js,jsx,ts,tsx}"
  ],
  presets: [require("nativewind/preset")],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#2b66a2',
          dark: '#1e4873',
          light: '#4280c2',
        },
        fanta: {
          DEFAULT: '#ff7700',
          dark: '#e06600',
          light: '#ff9233',
        },
      },
    },
  },
  plugins: [],
};