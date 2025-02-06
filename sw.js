const CACHE_NAME = 'chill-zone-v1';
const urlsToCache = [
  '/',
  '/index.html',
  '/about.html',
  '/css/style.css',
  '/js/main.js',
  '/images/animebg.jpg',
  // ... weitere Assets ...
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => response || fetch(event.request))
  );
}); 