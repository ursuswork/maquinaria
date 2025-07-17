self.addEventListener('install', e => {
  e.waitUntil(
    caches.open('maquinaria-app').then(cache => {
      return cache.addAll([
        './',
        './login.php',
        './inventario.php',
        './agregar_maquinaria.php',
        './editar_maquinaria.php',
        './eliminar_maquinaria.php',
        './conexion.php',
        './logout.php',
        './validar_login.php',
        './manifest.json',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'
      ]);
    })
  );
});

self.addEventListener('fetch', e => {
  e.respondWith(
    caches.match(e.request).then(response => response || fetch(e.request))
  );
});
