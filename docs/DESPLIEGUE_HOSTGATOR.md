# Despliegue de TurismoSV en HostGator

Esta guia mantiene el codigo privado fuera del directorio publico y no requiere
un proceso de cola permanente. Nunca se deben subir `.env`, respaldos ni
credenciales al repositorio.

## 1. Requisitos de la cuenta

- PHP 8.2 o superior con `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`,
  `xml`, `ctype`, `json` y `fileinfo`.
- MySQL con cotejamiento `utf8mb4_unicode_ci`.
- HTTPS activo para el dominio.
- Acceso al administrador de archivos y, preferiblemente, Terminal/SSH.
- Posibilidad de configurar el document root del dominio a la carpeta `public`.

Antes de subir archivos, compilar localmente con `npm ci && npm run build` e
instalar dependencias de produccion con `composer install --no-dev
--optimize-autoloader`. Las carpetas `vendor` y `public/build` deben llegar al
servidor cuando Composer o Node no esten disponibles en cPanel.

## 2. Estructura recomendada

Crear la aplicacion fuera de `public_html`:

```text
/home/USUARIO/turismosv/          aplicacion completa
/home/USUARIO/turismosv/public/   unico document root publico
```

En cPanel, apuntar el dominio o subdominio directamente a
`/home/USUARIO/turismosv/public`. Esta es la opcion preferida porque impide el
acceso web a `.env`, `storage`, `vendor` y los archivos fuente.

Si el plan no permite cambiar el document root, copiar solamente el contenido
de `public/` a `public_html/`. En `public_html/index.php`, cambiar las dos rutas
`__DIR__.'/../...'` por rutas absolutas hacia `storage/framework/maintenance.php`,
`vendor/autoload.php` y `bootstrap/app.php` dentro de `/home/USUARIO/turismosv`.
No copiar el resto de Laravel dentro de `public_html`.

## 3. Variables y base de datos

Copiar `.env.production.example` como `.env` solo en el servidor, completar
valores reales y ejecutar una sola vez:

```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan optimize
php artisan turismosv:production-check
```

El `db:seed` carga el catalogo fundador como borrador. No se deben publicar
lugares sin verificacion editorial e imagenes autorizadas. Si HostGator bloquea
enlaces simbolicos, solicitar su habilitacion antes de aceptar fotografias; no
se debe exponer toda la carpeta `storage` como alternativa.

Dar permisos de escritura únicamente a `storage/` y `bootstrap/cache/` (normalmente
`755` o `775`, segun la configuracion del servidor). Nunca usar `777`.

## 4. Dominio, correo y acceso social

- `APP_URL` debe contener el dominio final con `https://` y sin barra final.
- Crear una cuenta de correo del dominio y configurar SMTP; probar registro,
  verificacion y recuperacion de acceso.
- Registrar en Google y Facebook las URL exactas:
  `https://DOMINIO/acceso/google/callback` y
  `https://DOMINIO/acceso/facebook/callback`.
- Completar la identidad del responsable legal y hacer revisar los documentos
  por un profesional en El Salvador antes del lanzamiento.

## 5. Actualizaciones sin mostrar errores

Respaldar primero archivos subidos y base de datos. Después:

```bash
php artisan down --retry=60
git pull --ff-only
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
php artisan turismosv:production-check
php artisan up
```

Si el frontend cambió y Node no existe en el servidor, subir `public/build`
compilado localmente antes de ejecutar la comprobacion. No ejecutar
`migrate:fresh` en produccion.

## 6. Respaldo y recuperacion

- Base de datos: exportacion diaria y antes de cada despliegue; conservar al
  menos siete copias diarias y cuatro semanales fuera del hosting.
- Archivos: respaldar `.env` de forma cifrada y `storage/app/public`.
- Probar una restauracion antes del lanzamiento.
- Para revertir codigo, desplegar el commit estable anterior y ejecutar
  `php artisan optimize`. Las migraciones destructivas requieren un plan de
  reversión especifico y nunca deben improvisarse.

## 7. Validacion final

Comprobar `/up`, portada, catálogo, registro, verificacion de correo, carga de
imagen, reseña, check-in, panel administrativo, sitemap y una pagina inexistente.
Confirmar que esta ultima no muestra trazas, que HTTPS no presenta contenido
mixto y que el respaldo más reciente puede descargarse.
