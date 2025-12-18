const CACHE_VERSION = 'v2'; // Update ini saat ada perubahan besar
const CACHE_NAME = `app-cache-${CACHE_VERSION}`;
const OFFLINE_PAGE = '/offline.html'; // Halaman fallback offline

// Daftar aset yang akan di-cache saat instalasi
const PRECACHE_ASSETS = [

];

// ===== INSTALL EVENT ===== //
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll(PRECACHE_ASSETS);
    }).then(() => {
      return self.skipWaiting(); // Langsung aktifkan SW baru
    }).catch(err => {
      console.error('[SW] Failed to precache:', err);
    })
  );
});

// ===== ACTIVATE EVENT ===== //
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames =>
      Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            console.log('[SW] Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      )
    ).then(() => {
      return self.clients.claim(); // Ambil alih semua client
    })
  );
});

// ===== FETCH EVENT ===== //
self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET' ||
      event.request.url.includes('/api/') ||
      event.request.url.includes('sockjs-node') ||
      event.request.url.includes('chrome-extension')) {
    return;
  }

  event.respondWith(
    caches.match(event.request).then(cachedResponse => {
      const fetchPromise = fetch(event.request).then(networkResponse => {
        // Hanya cache jika respons valid
        if (networkResponse && networkResponse.status === 200 && isStaticAsset(event.request)) {
          const responseClone = networkResponse.clone();
          caches.open(CACHE_NAME).then(cache => {
            cache.put(event.request, responseClone);
          });
        }
        return networkResponse;
      }).catch(err => {
        // Jika gagal, fallback ke cache
        if (cachedResponse) return cachedResponse;

        if (event.request.mode === 'navigate') {
          return caches.match(OFFLINE_PAGE);
        }
      });

      // Navigasi: coba cache dulu, lalu fetch
      if (event.request.mode === 'navigate') {
        return cachedResponse || fetchPromise;
      }

      // Aset lainnya
      return cachedResponse || fetchPromise;
    })
  );
});

// ===== HELPER ===== //
function isStaticAsset(request) {
  return request.url.includes('/styles/') ||
         request.url.includes('/scripts/') ||
         request.url.includes('/images/') ||
         request.url.includes('/fonts/');
}
