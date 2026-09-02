# Google Analytics 4

ID configurado: `G-75MT2GD44N`. Se puede reemplazar con `GOOGLE_ANALYTICS_ID` o desactivar con `GOOGLE_ANALYTICS_ENABLED=false` en `.env`.

La etiqueta solo se carga después de aceptar analítica. La preferencia dura 180 días y puede cambiarse con el botón “Preferencias de cookies”. El antiguo botón “Entendido” no cuenta como aceptación. Si el almacenamiento local falla, la decisión solo dura en la página actual.

Se miden portada, exploración, rankings, postales, fichas de lugares y programa fundador para visitantes sin sesión. No se mide administración, autenticación, perfiles de viajeros ni otras páginas privadas. Las consultas y fragmentos se excluyen de la URL inicial enviada a Google. Los contadores propios siguen funcionando independientemente.

## Configuración en Google

- En Administrar → Flujos de datos → flujo Web, desactivar la medición mejorada automática de formularios, búsquedas y cambios del historial. Esta integración está destinada a páginas vistas; no activar eventos adicionales que puedan enviar campos de formularios o URLs con datos personales.
- Mantener desactivados Google Signals y las funciones publicitarias. Revisar la retención de datos en la propiedad (las cookies y la retención de eventos son ajustes distintos).
- Probar en una ventana privada sin sesión: antes de aceptar o después de rechazar no debe haber solicitudes a Google Analytics. Tras aceptar, comprobar la visita en Tiempo real. Los bloqueadores pueden impedir la medición.

## Producción

1. `git pull origin main`.
2. Ejecutar `npm ci` y `npm run build`, o subir el contenido actualizado de `public/build` compilado localmente. Esto retira el aviso anterior de cookies.
3. Ejecutar `php artisan config:cache` y `php artisan view:clear`.
4. Si `public_html` está separado de `public`, copiar también `public/js/analytics-consent.js` a `public_html/js/` y el build actualizado a `public_html/build/`.

La propiedad debe estar bajo control del titular del sitio. No se ha verificado la recepción de datos en la cuenta de Google desde el entorno local.

Referencia: https://developers.google.com/tag-platform/security/concepts/consent-mode
