/**
 * RPN Supervisor — Service Worker
 * Strategy:
 *   - Static assets (CSS/JS/fonts/icons): Cache First
 *   - HTML pages: Network First with offline fallback
 *   - Images: Cache First with expiry
 *   - API/POST requests: Network Only (never cache mutations)
 */

const CACHE_VERSION    = 'v1';
const STATIC_CACHE     = `rpn-static-${CACHE_VERSION}`;
const PAGES_CACHE      = `rpn-pages-${CACHE_VERSION}`;
const IMAGES_CACHE     = `rpn-images-${CACHE_VERSION}`;

const ALL_CACHES = [STATIC_CACHE, PAGES_CACHE, IMAGES_CACHE];

/* ── Assets to pre-cache on install ── */
const PRECACHE_STATIC = [
  '/css/neumorphism.css',
  'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
  'https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css',
  'https://code.jquery.com/jquery-3.7.1.min.js',
  'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js',
  'https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js',
  'https://cdn.jsdelivr.net/npm/chart.js',
  'https://cdn.jsdelivr.net/npm/sweetalert2@11',
];

const PRECACHE_PAGES = [
  '/offline.html',
];

/* ── Install ── */
self.addEventListener('install', event => {
  self.skipWaiting();
  event.waitUntil(
    Promise.allSettled([
      caches.open(STATIC_CACHE).then(cache =>
        Promise.allSettled(
          PRECACHE_STATIC.map(url =>
            cache.add(url).catch(() => { /* CDN may fail offline, skip */ })
          )
        )
      ),
      caches.open(PAGES_CACHE).then(cache =>
        Promise.allSettled(
          PRECACHE_PAGES.map(url =>
            cache.add(url).catch(() => {})
          )
        )
      ),
    ])
  );
});

/* ── Activate — purge old caches ── */
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(
        keys
          .filter(key => !ALL_CACHES.includes(key))
          .map(key => caches.delete(key))
      )
    ).then(() => self.clients.claim())
  );
});

/* ── Fetch ── */
self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);

  /* Skip non-GET and cross-origin POST (form submissions, API mutations) */
  if (request.method !== 'GET') return;

  /* Skip browser-extension / chrome-extension requests */
  if (!['http:', 'https:'].includes(url.protocol)) return;

  /* Static assets — Cache First */
  if (isStaticAsset(url)) {
    event.respondWith(cacheFirst(request, STATIC_CACHE));
    return;
  }

  /* Images — Cache First with 30-day max */
  if (isImage(url)) {
    event.respondWith(cacheFirst(request, IMAGES_CACHE));
    return;
  }

  /* HTML navigation — Network First, fallback to offline page */
  if (request.mode === 'navigate') {
    event.respondWith(networkFirstPage(request));
    return;
  }

  /* Everything else — Network First, fallback to cache */
  event.respondWith(networkFirst(request, PAGES_CACHE));
});

/* ────────────────────────────────────────
   Helpers
   ──────────────────────────────────────── */

function isStaticAsset(url) {
  return (
    url.pathname.match(/\.(css|js|woff2?|ttf|eot)(\?.*)?$/) ||
    url.hostname.includes('fonts.googleapis.com') ||
    url.hostname.includes('fonts.gstatic.com') ||
    url.hostname.includes('cdnjs.cloudflare.com') ||
    url.hostname.includes('cdn.jsdelivr.net') ||
    url.hostname.includes('code.jquery.com') ||
    url.hostname.includes('cdn.datatables.net')
  );
}

function isImage(url) {
  return url.pathname.match(/\.(png|jpe?g|gif|webp|svg|ico)(\?.*)?$/);
}

/** Cache First: serve from cache, fetch & update if miss */
async function cacheFirst(request, cacheName) {
  const cache    = await caches.open(cacheName);
  const cached   = await cache.match(request);
  if (cached) return cached;

  try {
    const response = await fetch(request);
    if (response.ok) {
      cache.put(request, response.clone());
    }
    return response;
  } catch {
    return new Response('', { status: 408, statusText: 'Offline' });
  }
}

/** Network First: try network, fall back to cache */
async function networkFirst(request, cacheName) {
  const cache = await caches.open(cacheName);
  try {
    const response = await fetch(request);
    if (response.ok) {
      cache.put(request, response.clone());
    }
    return response;
  } catch {
    const cached = await cache.match(request);
    return cached || new Response('', { status: 408, statusText: 'Offline' });
  }
}

/** Network First for HTML pages — fallback to /offline.html */
async function networkFirstPage(request) {
  const cache = await caches.open(PAGES_CACHE);
  try {
    const response = await fetch(request);
    if (response.ok) {
      cache.put(request, response.clone());
    }
    return response;
  } catch {
    const cached = await cache.match(request);
    if (cached) return cached;
    const offline = await cache.match('/offline.html');
    return offline || new Response(
      '<h1 style="font-family:sans-serif;padding:40px;color:#4e73df">Tidak ada koneksi internet.</h1>',
      { status: 503, headers: { 'Content-Type': 'text/html' } }
    );
  }
}

/* ── Push notification handler (future-ready) ── */
self.addEventListener('push', event => {
  if (!event.data) return;
  const data = event.data.json();
  event.waitUntil(
    self.registration.showNotification(data.title || 'RPN Supervisor', {
      body: data.body || '',
      icon: '/icons/icon-192x192.png',
      badge: '/icons/icon-96x96.png',
      data: { url: data.url || '/' },
    })
  );
});

self.addEventListener('notificationclick', event => {
  event.notification.close();
  event.waitUntil(
    clients.openWindow(event.notification.data.url)
  );
});
