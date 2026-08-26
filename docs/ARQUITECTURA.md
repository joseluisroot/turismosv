# Arquitectura inicial

## Decisión

Monolito modular construido con Laravel 12, PHP 8.2+ y MySQL tanto en el
entorno local de Wamp como en producción.

## Módulos previstos

- Identidad, conexiones sociales y aceptaciones legales.
- Catálogo: lugares, categorías, departamentos y fuentes.
- Comunidad: reseñas, fotografías, favoritos y reportes.
- Confianza: verificaciones, visitas y reputación.
- Pasaporte: check-ins, sellos, rutas y colecciones.
- Comercios: reclamación, representantes y respuestas.
- Moderación y administración.

## Restricciones de HostGator compartido

- No depender de procesos Node.js en producción.
- Compilar CSS y JavaScript antes del despliegue.
- Ejecutar trabajos diferidos mediante base de datos y cron.
- Mantener consultas indexadas y paginadas.
- Abstraer fotografías para migrarlas a almacenamiento externo.

## Evolución

La aplicación podrá separar almacenamiento de imágenes, correo, caché, colas y base de datos sin
cambiar el modelo de producto ni dividir prematuramente el dominio en microservicios.
