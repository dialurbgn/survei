// Konfigurasi Service Worker
const SW_CONFIG = {
  debug: true, // Set false untuk production
  swPath: '/service-worker.js',
  updateInterval: 24 * 60 * 60 * 1000 // 24 jam
};

// Fungsi utama
if ('serviceWorker' in navigator) {
  window.addEventListener('load', async () => {
    try {
      const registration = await navigator.serviceWorker.register(SW_CONFIG.swPath);

      if (SW_CONFIG.debug) {
        console.log('✅ ServiceWorker terdaftar dengan scope:', registration.scope);
      }

      // Tangani update worker
      registration.addEventListener('updatefound', () => {
        const newWorker = registration.installing;

        newWorker.addEventListener('statechange', () => {
          if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
            if (SW_CONFIG.debug) {
              console.log('🔄 Versi baru tersedia!');
            }
            //showUpdateNotification(newWorker);
          }
        });
      });

      // Periksa update berkala
      setInterval(() => {
        registration.update();
      }, SW_CONFIG.updateInterval);

    } catch (error) {
      console.error('❌ Registrasi ServiceWorker gagal:', error);
    }
  });
}

// Fungsi untuk menampilkan notifikasi update
function showUpdateNotification(newWorker) {
  if (confirm('Versi baru tersedia! Muat ulang untuk update?')) {
    newWorker.postMessage({ action: 'skipWaiting' });

    // Tunggu hingga SW baru aktif
    navigator.serviceWorker.addEventListener('controllerchange', () => {
      window.location.reload();
    });
  }
}
