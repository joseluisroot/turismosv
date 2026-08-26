# TurismoSV: definición inicial del producto

TurismoSV es un catálogo turístico comunitario para descubrir El Salvador con
información verificable, experiencias auténticas y un pasaporte digital de visitas.

## Promesa

Cada lugar tiene una historia; cada dato debe tener respaldo; cada reconocimiento debe merecerse.

## Primer recorrido del MVP

1. Una persona descubre un lugar.
2. Consulta datos, reseñas y nivel de verificación.
3. Crea su cuenta y acepta los documentos vigentes.
4. Hace check-in y publica una experiencia.
5. Obtiene un sello para su pasaporte.
6. Su aporte ayuda a mantener la ficha actualizada.

## Reglas estructurales

- El contenido comunitario, editorial y patrocinado siempre se distingue.
- Un patrocinio nunca compra estrellas ni altera el ranking orgánico.
- Un usuario conserva la autoría de sus fotografías.
- "Verificado por TurismoSV" describe una metodología privada y no una certificación gubernamental.
- La información sensible y la ubicación personal nunca son públicas por defecto.

## Pasaporte, logros y puntos

El pasaporte digital reúne los sellos obtenidos mediante visitas verificadas. La primera versión de logros y puntos ya está activa; las tarjetas para compartir se incorporarán en un incremento posterior.

### Capacidades previstas

1. Cada visita válida agrega un sello al pasaporte del usuario.
2. Los sellos completan logros, por ejemplo: primera visita, tres playas, ruta de montaña o recorrido por un departamento.
3. Al completar un logro, la plataforma genera una tarjeta PNG vertical con el nombre del logro, fecha, puntos y marca TurismoSV.
4. El usuario decide si desea descargarla o compartirla mediante el menú nativo de su dispositivo en Instagram, Facebook, TikTok, X, WhatsApp u otra aplicación compatible.
5. El pasaporte completo tendrá una vista pública opcional y una versión visual compartible, sin revelar ubicaciones sensibles ni recorridos en tiempo real.
6. El usuario recibe puntos por visitas verificadas y logros. Compartir podrá entregar una bonificación limitada, pero no deberá permitir acumular puntos indefinidamente por la misma publicación.

### Principios de verificación y antifraude

- Compartir un logro no verifica una visita; la visita debe validarse primero mediante la metodología de check-in.
- Un lugar solo puede otorgar puntos una vez dentro del período definido para cada logro.
- Se aplicarán límites de frecuencia, señales de ubicación con consentimiento, evidencia opcional y revisión comunitaria o comercial según el nivel de confianza.
- El sistema registrará por qué se otorgaron o retiraron puntos y permitirá resolver reclamaciones.
- Los puntos no tendrán valor monetario ni serán canjeables hasta definir reglas, vigencia, patrocinadores, impuestos y condiciones legales.
- Las publicaciones sociales serán siempre voluntarias y requerirán una acción explícita del usuario.
- El perfil público está desactivado por defecto. El viajero elige alias o nombre real y controla por separado la visibilidad de logros y sellos; nunca se publica el correo ni una visita pendiente.

### Modelo inicial de puntos

- Check-in verificado: 100 puntos, una sola vez por sello.
- Reseña útil posterior a una visita: puntos adicionales.
- Primera visita verificada: 50 puntos adicionales.
- Tres lugares diferentes: 100 puntos adicionales.
- Dos departamentos diferentes: 150 puntos adicionales.
- Primera publicación voluntaria de cada logro: bonificación social única.
- Contenido denunciado, duplicado o fraudulento: retención o reversión de puntos.

Cada movimiento queda en un registro inmutable con una clave de idempotencia, por lo que repetir una verificación no duplica puntos ni logros. La matriz podrá ampliarse antes del lanzamiento, manteniendo estas garantías.

## Personalización privada

Después de verificar el correo, el viajero puede elegir hasta seis intereses. La primera recomendación combina esas preferencias con la calidad del catálogo y excluye lugares que ya registró como visitados. Los intereses son privados, opcionales y editables; no forman parte del perfil público.

## Estado de esta primera entrega

### Fotografías comunitarias

Las fotografías se almacenan de forma privada al recibirse y solo una decisión de moderación permite servirlas públicamente. Cada envío conserva autor, consentimiento, versión de licencia, estado y nota interna. En el servidor se pueden revisar con `php artisan turismosv:photo-moderate {id} approve|reject --note="..."`.

### Moderación operativa

Las cuentas con rol `admin` disponen de un panel privado para decidir sobre fotografías, visitas pendientes y denuncias. Cada acción registra responsable, fecha y justificación; verificar una visita utiliza el mismo servicio transaccional que emite sellos y puntos, evitando duplicados.

### Gestión de comercios

Una persona verificada puede reclamar una ficha aportando relación, contacto y documento privado. Solo después de aprobación administrativa puede editar descripción, contacto, dirección, horarios y referencia de precios oficiales. El comercio no puede modificar nombre editorial, reseñas, estrellas, visitas, puntuación de confianza ni ranking.

### Administración editorial del catálogo

Los administradores crean lugares inicialmente como borradores, gestionan categorías y departamentos y controlan publicación o archivo. Para publicar se exige una fuente identificada y fecha de verificación. El registro conserva notas editoriales privadas, responsable de creación, último editor y fecha inicial de publicación.

### Descubrimiento del catálogo

La búsqueda pública consulta nombre, municipio y departamento. Los filtros de categoría, territorio, verificación y calificación se combinan y pueden ordenarse por relevancia, calificación, visitas verificadas o publicación reciente. Borradores y archivos quedan excluidos en todos los casos.

### Rankings turísticos

El índice público combina 50% de calificación ajustada por volumen, 25% de respaldo, 15% de visitas verificadas en escala logarítmica y 10% de confianza por cantidad de reseñas. Se calcula para el catálogo general y por categoría; no incluye borradores o archivos y ningún patrocinio puede modificar la posición.

Los lugares, métricas y calificaciones cargados inicialmente son datos de demostración. No deben
publicarse como información real hasta completar el proceso editorial y documentar sus fuentes.
