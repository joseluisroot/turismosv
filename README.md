# TurismoSV

Plataforma comunitaria para descubrir El Salvador mediante un catálogo confiable,
reseñas, visitas verificadas y un pasaporte turístico digital.

## Estado

Primera base funcional del MVP. La portada, el catálogo inicial y el modelo de
verificación ya están conectados a la base de datos. Los lugares, métricas y
calificaciones precargados son exclusivamente datos de demostración.

## Tecnología

- PHP 8.2 o superior.
- Laravel 12.
- MySQL en desarrollo local y producción.
- Blade, CSS y JavaScript compilados con Vite.

La aplicación se mantiene como monolito modular para operar inicialmente en un
servidor compartido de HostGator y conservar una ruta de evolución sencilla.

## Instalación local

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

En Windows, el archivo `.env.example` puede copiarse manualmente como `.env`.

## Verificación

```bash
php artisan test
npm run build
```

Consulta [docs/PRODUCTO.md](docs/PRODUCTO.md) y
[docs/ARQUITECTURA.md](docs/ARQUITECTURA.md) para las decisiones iniciales.
