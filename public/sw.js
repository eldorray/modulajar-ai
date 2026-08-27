// Service worker PWA guru: shell cache untuk aset statis, network-first untuk halaman.
const CACHE = 'rpp-guru-v1';
const SHELL = ['/icons/icon-192.png', '/icons/icon-512.png', '/logo.png', '/app/offline'];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE).then((cache) => cache.addAll(SHELL)).then(() => self.skipWaiting())
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

    if (request.method !== 'GET' || new URL(request.url).origin !== self.location.origin) {
        return;
    }

    // Halaman: coba jaringan, fallback ke halaman offline.
    if (request.mode === 'navigate') {
        event.respondWith(fetch(request).catch(() => caches.match('/app/offline')));
        return;
    }

    // Aset: pakai cache kalau ada, isi cache di belakang.
    event.respondWith(
        caches.match(request).then((cached) => cached || fetch(request).then((response) => {
            if (response.ok) {
                const copy = response.clone();
                caches.open(CACHE).then((cache) => cache.put(request, copy));
            }
            return response;
        }).catch(() => cached))
    );
});
