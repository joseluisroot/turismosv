# TurismoSV

## Acceso con Google y Facebook

La aplicación usa Laravel Socialite y nunca almacena contraseñas ni tokens externos. Configura en `.env` las variables `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `FACEBOOK_CLIENT_ID` y `FACEBOOK_CLIENT_SECRET`. En cada consola registra exactamente estas rutas de retorno usando el mismo dominio y protocolo de `APP_URL`:

- `/acceso/google/callback`
- `/acceso/facebook/callback`

En producción, `APP_URL` y las rutas autorizadas deben utilizar HTTPS. Si no hay credenciales, los botones permanecen visibles pero muestran un aviso seguro y no intentan contactar al proveedor.

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

## Produccion en HostGator

La configuracion de produccion se documenta en
[docs/DESPLIEGUE_HOSTGATOR.md](docs/DESPLIEGUE_HOSTGATOR.md). Usa
`.env.production.example` como lista de variables, sin guardar credenciales en
Git. Antes de abrir el sitio al publico ejecuta:

```bash
php artisan turismosv:production-check
```

El comando devuelve un error mientras falte cualquier requisito critico de
seguridad, base de datos, correo, identidad legal, permisos o recursos
compilados.
