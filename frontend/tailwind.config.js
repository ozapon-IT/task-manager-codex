/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  theme: {
    extend: {
      colors: {
        lightBg: '#f8fafc',
        lightCard: '#ffffff',
        lightBorder: '#e2e8f0',
        lightText: '#334155',
        lightHeading: '#1e293b',
        accentBlue: '#2563eb',
        darkBg: '#0f172a',
        darkCard: '#111827',
        darkBorder: '#1e293b',
      },
    },
  },
  plugins: [],
}
