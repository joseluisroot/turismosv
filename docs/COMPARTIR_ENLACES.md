# Vistas previas al compartir enlaces

Las páginas principales generan Open Graph y Twitter Cards desde Blade, sin depender de JavaScript. El componente común es `resources/views/components/social-meta.blade.php`.

- La portada, el catálogo, los rankings, las postales, el programa fundador y los perfiles públicos incluyen título, descripción e imagen.
- Las fichas usan la primera fotografía aprobada que muestra la página. Sin fotos aprobadas usan la portada de marca.
- La imagen de respaldo es `public/brand/compartir-turismosv.png` (1200 × 630). Al reemplazarla, conviene usar otro nombre y actualizar el componente para evitar cachés antiguas.
- Las páginas de cuenta usan la tarjeta general; los datos privados no se incorporan a ella.

## Publicar y comprobar

1. Subir las plantillas modificadas, el nuevo componente y la imagen al servidor. El ZIP de Vite por sí solo no contiene estos archivos. Si el hosting separa la carpeta pública, colocar la imagen en `brand/` dentro de la raíz pública del dominio.
2. Comprobar que `APP_URL` contiene el dominio público con HTTPS y que HTTPS funciona correctamente. No publicar URLs de localhost en los metadatos. Si existe un proxy, comprobar que Laravel reconoce el esquema HTTPS de la petición.
3. Ejecutar `php artisan view:clear` después de actualizar las plantillas. Si se cambió la configuración, regenerar la caché con `php artisan config:cache`.
4. Abrir el código fuente de la página pública y comprobar `og:title`, `og:description`, `og:image` y `og:url`. Abrir también la URL de `og:image`: debe devolver una imagen sin inicio de sesión, bloqueos ni desafíos antibot.
5. Revisar el enlace en https://developers.facebook.com/tools/debug/ y solicitar una nueva lectura cuando esté disponible. Después volver a pegar el enlace en un mensaje nuevo de WhatsApp y esperar la vista previa antes de enviarlo.

Cada red decide el formato, el recorte y si muestra la descripción. Las vistas previas pueden conservarse en caché; una tarjeta antigua no demuestra que las etiquetas nuevas estén mal. No se garantiza que los mensajes ya enviados se actualicen.

Referencia del protocolo: https://ogp.me/
