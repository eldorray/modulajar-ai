import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Daftarkan dari entry point bersama agar installability tidak bergantung pada
// banner atau halaman tertentu. Browser mengabaikannya di origin non-HTTPS.
if ('serviceWorker' in navigator) {
    window.addEventListener('load', async () => {
        try {
            const registration = await navigator.serviceWorker.register('/sw.js', { scope: '/' });
            registration.update();
        } catch (error) {
            console.warn('Service worker RPP Guru gagal didaftarkan.', error);
        }
    });
}
