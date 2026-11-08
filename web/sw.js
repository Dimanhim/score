/* Caches static files only. Pages and data always come from the network,
   so balances are never shown stale. Bump VERSION when CSS/JS change. */
var VERSION = 'saldo-v1';
var STATIC = /\.(?:css|js|png|svg|woff2?|webmanifest)$/;

self.addEventListener('install', function () { self.skipWaiting(); });

self.addEventListener('activate', function (event) {
  event.waitUntil(
    caches.keys().then(function (keys) {
      return Promise.all(keys.filter(function (k) { return k !== VERSION; }).map(function (k) { return caches.delete(k); }));
    }).then(function () { return self.clients.claim(); })
  );
});

self.addEventListener('fetch', function (event) {
  var req = event.request;
  if (req.method !== 'GET') return;
  var url = new URL(req.url);
  var isFont = url.hostname === 'fonts.googleapis.com' || url.hostname === 'fonts.gstatic.com';
  if (!isFont && (url.origin !== location.origin || !STATIC.test(url.pathname))) return;

  event.respondWith(
    caches.open(VERSION).then(function (cache) {
      return cache.match(req).then(function (hit) {
        var fresh = fetch(req).then(function (res) {
          if (res.ok || res.type === 'opaque') cache.put(req, res.clone());
          return res;
        });
        return hit || fresh;
      });
    })
  );
});
