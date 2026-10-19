/**
 * Token desain identitas NU (warna, tipografi, radius, bayangan).
 *
 * Sebelumnya token ini hanya hidup sebagai config inline pada Tailwind Play CDN
 * di `resources/views/components/public-head.blade.php`. Akibatnya token tidak
 * pernah ikut terkompilasi ke `public/build`, dan halaman publik bergantung
 * penuh pada CDN saat runtime (FOUC + boros bandwidth).
 *
 * Mulai sekarang berkas ini menjadi satu sumber kebenaran yang dipakai
 * `tailwind.config.js`, sehingga `npm run build` menghasilkan seluruh utility
 * kustom tanpa CDN.
 *
 * @type {import('tailwindcss').Config['theme']['extend']}
 */
export const tailwindTokens = {
    colors: {
        'warm-bg': '#F7F5EF',
        'warm-card': '#FDFCF7',
        charcoal: '#171816',
        'muted-charcoal': '#4F544E',
        'nu-deep': '#16452F',
        'nu-night': '#141815',
        'muted-sage': '#E7ECE4',
        'warm-beige': '#EDE8DD',
        'muted-gold': '#B49352',
        'border-neutral': '#E5E2D9',
        'border-subtle': '#D5D2C8',
        nu: {
            50: '#f0fdf4',
            100: '#dcfce7',
            200: '#bbf7d0',
            500: '#8bc14b',
            600: '#6ba82f',
            700: '#1F5A3F',
            800: '#16452F',
            900: '#0F3324',
        },
        gold: '#C99A2E',
    },
    fontFamily: {
        // Halaman publik memakai Inter (dimuat via Google Fonts).
        sans: ['Inter', 'sans-serif'],
        // Panel admin (Breeze) memakai Figtree dari fonts.bunny.net.
        figtree: ['Figtree', 'sans-serif'],
        arabic: ['Amiri', 'serif'],
        heading: ['Baloo 2', 'Inter', 'sans-serif'],
    },
    borderRadius: {
        btn: '14px',
        card: '20px',
        'container-r': '28px',
    },
    boxShadow: {
        subtle: '0 2px 10px rgba(23, 24, 22, 0.04), 0 1px 3px rgba(23, 24, 22, 0.03)',
        elevated: '0 10px 30px rgba(23, 24, 22, 0.06), 0 1px 3px rgba(23, 24, 22, 0.04)',
    },
};
