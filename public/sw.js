// Service worker PWA guru: shell cache untuk aset statis, network-first untuk halaman.
const CACHE = 'rpp-guru-v2';
const SHELL = [
    '/app/offline',
    '/manifest.webmanifest',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    '/icons/icon-192-maskable.png',
    '/icons/icon-512-maskable.png',
    '/icons/apple-touch-icon.png',
    '/logo.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE).then(async (cache) => {
            await cache.addAll(SHELL);

            // Halaman offline harus tetap rapi. Ambil nama aset Vite yang
            // ter-hash saat install, tanpa mengunci service worker jika build
            // belum tersedia pada instalasi lokal yang belum lengkap.
            try {
                const response = await fetch('/build/manifest.json');
                if (!response.ok) return;

                const manifest = await response.json();
                const buildAssets = [...new Set(Object.values(manifest).flatMap((entry) => [
                    entry.file,
                    ...(entry.css || []),
                ]).filter(Boolean).map((file) => `/build/${file}`))];

                await cache.addAll(buildAssets);
            } catch (error) {
                console.warn('Aset build belum dapat disimpan untuk mode offline.', error);
            }
        }).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    if (request.method !== 'GET' || url.origin !== self.location.origin) {
        return;
    }

    // Halaman: coba jaringan, fallback ke halaman offline.
    if (request.mode === 'navigate') {
        event.respondWith(fetch(request).catch(() => caches.match('/app/offline')));
        return;
    }

    const cacheable = ['image', 'style', 'script', 'font', 'manifest'].includes(request.destination);
    if (!cacheable) return;

    // Aset: tampilkan cache seketika lalu segarkan tanpa menahan respons.
    event.respondWith(caches.open(CACHE).then(async (cache) => {
        const cached = await cache.match(request);
        const network = fetch(request).then((response) => {
            if (response.ok && response.type === 'basic') {
                cache.put(request, response.clone());
            }
            return response;
        });

        if (cached) {
            event.waitUntil(network.catch(() => undefined));
            return cached;
        }

        return network;
    }));
});
