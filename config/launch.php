<?php

return [
    'minimum_published_places' => 6,

    'checklist' => [
        'legal_review' => ['label' => 'Revisión jurídica y datos del responsable', 'area' => 'Legal', 'required' => true],
        'catalog_audit' => ['label' => 'Auditoría del catálogo y retiro de datos demostrativos', 'area' => 'Contenido', 'required' => true],
        'image_rights' => ['label' => 'Permisos y créditos de todas las imágenes públicas', 'area' => 'Contenido', 'required' => true],
        'email_flow' => ['label' => 'Registro, verificación y recuperación probados con SMTP', 'area' => 'Tecnología', 'required' => true],
        'backup_restore' => ['label' => 'Respaldo descargado y restauración ensayada', 'area' => 'Tecnología', 'required' => true],
        'mobile_qa' => ['label' => 'Recorrido móvil de los flujos principales', 'area' => 'Calidad', 'required' => true],
        'support_channel' => ['label' => 'Canal público para soporte, privacidad y apelaciones', 'area' => 'Operación', 'required' => true],
        'launch_content' => ['label' => 'Contenido editorial de la primera semana programado', 'area' => 'Mercadeo', 'required' => true],
        'social_login' => ['label' => 'Acceso con Google y Facebook habilitado', 'area' => 'Tecnología', 'required' => false],
        'sponsor_pilot' => ['label' => 'Patrocinador piloto y condiciones comerciales', 'area' => 'Negocio', 'required' => false],
    ],
];
