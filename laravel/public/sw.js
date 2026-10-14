/* Mora Lenz PWA service worker. */
var CACHE_NAME = "mora-lenz-static-v2";
var OFFLINE_URL = "/offline.html";
var PRECACHE = [
  OFFLINE_URL,
  "/css/site.css",
  "/js/site.js",
  "/img/favicon.png",
  "/img/pwa-icon-192.png",
  "/img/pwa-icon-512.png"
];

self.addEventListener("install", function (event) {
  event.waitUntil(
    caches.open(CACHE_NAME).then(function (cache) {
      return cache.addAll(PRECACHE);
    }).then(function () {
      return self.skipWaiting();
    })
  );
});

self.addEventListener("activate", function (event) {
  event.waitUntil(
    caches.keys().then(function (keys) {
      return Promise.all(keys.map(function (key) {
        if (key !== CACHE_NAME) return caches.delete(key);
      }));
    }).then(function () {
      return self.clients.claim();
    })
  );
});

self.addEventListener("fetch", function (event) {
  var request = event.request;

  if (request.method !== "GET") return;

  // Pages stay fresh from Laravel. If the network is unavailable, show a
  // dedicated offline page instead of exposing stale event or gallery data.
  if (request.mode === "navigate") {
    event.respondWith(
      fetch(request).catch(function () {
        return caches.match(OFFLINE_URL);
      })
    );
    return;
  }

  var url = new URL(request.url);
  if (url.origin !== self.location.origin || url.pathname.indexOf("/api/") === 0) return;

  // Static assets load immediately from cache and refresh in the background.
  event.respondWith(
    caches.match(request).then(function (cached) {
      var update = fetch(request).then(function (response) {
        if (response && response.ok) {
          // Clone now, synchronously: by the time caches.open() resolves the
          // page may already be reading the body, and clone() would throw.
          var copy = response.clone();
          event.waitUntil(
            caches.open(CACHE_NAME).then(function (cache) {
              return cache.put(request, copy);
            }).catch(function () {})
          );
        }
        return response;
      }).catch(function () {
        return cached;
      });

      return cached || update;
    })
  );
});
