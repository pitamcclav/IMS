/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        'primary': 'var(--color-primary)',
        'secondary': 'var(--color-secondary)',
        'success': 'var(--color-success)',
        'info': 'var(--color-info)',
        'warning': 'var(--color-warning)',
        'danger': 'var(--color-danger)',
        'light': 'var(--color-light)',
        'dark': 'var(--color-dark)',
        'header': 'var(--color-header)',
        'accent': 'var(--color-accent)',
      },
      fontFamily: {
        'default': 'var(--font-default)',
        'primary': 'var(--font-primary)',
        'secondary': 'var(--font-secondary)',
      },
    },
  },
  plugins: [],
}