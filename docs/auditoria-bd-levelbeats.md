# Auditoría de base de datos LevelBeats

Informe de revisión de uso real de tablas del proyecto LevelBeats. La auditoría se ha realizado únicamente mediante lectura del repositorio Laravel, sin ejecutar migraciones, sin modificar base de datos y sin tocar phpMyAdmin.

## 1. Resumen ejecutivo

- Total de tablas revisadas: 40.
- Tablas claramente usadas por funcionalidad activa: 20.
- Tablas usadas como pivots o relación necesarias: 5.
- Tablas existentes pero aparentemente no usadas por el código actual: 12.
- Tablas dudosas o preparadas para funcionalidad no conectada completamente: 3.
- No se ha ejecutado ningún `DROP TABLE`.
- Cualquier limpieza debe hacerse manualmente, con copia de seguridad previa y revisando datos históricos.

Conclusión general: la base de datos conserva varias tablas de versiones anteriores o módulos planificados que no aparecen conectadas al código Laravel actual. Las candidatas más claras a eliminación son tablas sin modelo, sin controlador, sin relación Eloquent, sin rutas, sin vistas y sin mención en migraciones actuales del repositorio.

## 2. Metodología

Se han revisado las siguientes fuentes:

- Modelos Eloquent en `app/Models`, especialmente `protected $table`, relaciones y `fillable`.
- Controladores en `app/Http/Controllers`, revisando consultas Eloquent, relaciones cargadas, `DB::table`, `join`, `whereHas`, creación, actualización y borrado.
- Servicios y clases auxiliares en `app/Services` y `app/Support`.
- Rutas en `routes/web.php`.
- Vistas Blade en `resources/views`, buscando acceso a relaciones y módulos funcionales.
- Migraciones en `database/migrations`.
- Seeders en `database/seeders` como evidencia secundaria, no suficiente por sí sola para considerar una tabla viva.
- Búsquedas exactas por nombre de tabla y patrones: `protected $table`, `DB::table`, `Schema::hasTable`, `Schema::hasColumn`, `join`, `belongsToMany`, `withPivot`, `Schema::create`, `Schema::table`.

La clasificación se ha hecho con criterios conservadores:

- Si una tabla aparece en modelo y en controlador/vista/ruta funcional, se considera usada.
- Si una tabla aparece como pivote en relaciones Eloquent o en consultas de compra/analítica, se considera necesaria.
- Si solo aparece en un modelo sin flujo funcional claro, se marca como dudosa o revisar manualmente.
- Si no aparece en código funcional, se considera candidata a eliminación, pero siempre con revisión manual previa.

## 3. Tabla resumen

| Tabla | Categoría | Uso detectado | Riesgo de eliminar | Recomendación |
|---|---|---|---|---|
| analitica | E | Modelo y relación en Usuario, pero analíticas actuales son dinámicas | Medio | Revisar manualmente |
| archivos | D | No hay modelo ni consultas a tabla; solo palabra genérica en textos/rutas de archivos | Bajo | Candidata a eliminar |
| archivos_proyecto | A | Modelo, controlador, vistas y relaciones de proyectos | Alto | Mantener |
| beat | A | Catálogo, Studio, carrito, compra, analíticas, perfiles | Alto | Mantener |
| beat_compra | B | Pivot Compra-Beat, compras legacy, analíticas | Alto | Mantener |
| beat_licencia | D | Sin referencias funcionales; sistema actual usa `licencia` y `compra_detalle` | Bajo | Candidata a eliminar |
| carpeta_guardado | D | Sin referencias | Bajo | Candidata a eliminar |
| carpeta_guardado_item | D | Sin referencias | Bajo | Candidata a eliminar |
| coleccion | A | Catálogo, Studio, carrito, compra, perfiles, analíticas | Alto | Mantener |
| coleccion_beat | B | Pivot Colección-Beat | Alto | Mantener |
| coleccion_compra | B | Pivot Compra-Colección, compras legacy, analíticas | Alto | Mantener |
| compra | A | Checkout, compras, facturas, productos, servicios | Alto | Mantener |
| compra_detalle | A | Licencias compradas, PDFs, analíticas, snapshots | Alto | Mantener |
| contrato | E | Modelo y vista de detalle de compra; no parece generarse en flujo actual | Medio | Mantener por ahora |
| conversacion | A | Mensajería directa y contador de mensajes | Alto | Mantener |
| cupones_aplicados | D | Sin referencias funcionales | Bajo | Candidata a eliminar |
| elemento_contenido | D | Sin referencias funcionales | Bajo | Candidata a eliminar |
| etiqueta_beat | D | Sin referencias funcionales | Bajo | Candidata a eliminar |
| factura | A | Facturación, PDFs, compras | Alto | Mantener |
| favoritos_usuario | D | Sustituida aparentemente por `guardados` | Bajo | Candidata a eliminar |
| guardados | A | Sistema actual de favoritos polimórficos | Alto | Mantener |
| impuestos_aplicados | D | Sin lógica; impuestos se calculan en Compra/Factura | Bajo | Candidata a eliminar |
| licencia | A | Selector de licencias, carrito, compra_detalle, PDFs | Alto | Mantener |
| mensaje | A | Mensajería de proyectos/encargos | Alto | Mantener |
| mensaje_directo | A | Mensajería directa entre usuarios | Alto | Mantener |
| muestra_previa | D | Sin referencias funcionales | Bajo | Candidata a eliminar |
| notificacion | E | Modelo y relación en Usuario, sin flujo activo detectado | Medio | Revisar manualmente |
| pago | E | Modelo y seeders, pero checkout usa `compra`/`factura` | Medio | Revisar manualmente |
| plan | A | Onboarding, suscripciones, límites Studio | Alto | Mantener |
| plan_por_rol | A | Planes por rol y límites de suscripción | Alto | Mantener |
| proyecto | A | Encargos de servicios, archivos, mensajes, compras | Alto | Mantener |
| redes_sociales_usuario | D | Sin referencias funcionales | Bajo | Candidata a eliminar |
| revisiones_proyecto | D | Sin referencias funcionales; flujo actual usa estados de proyecto | Bajo | Candidata a eliminar |
| rol | A | Multirol, permisos, onboarding, admin | Alto | Mantener |
| servicio | A | Servicios, Studio, proyectos, carrito, compras | Alto | Mantener |
| servicio_compra | B | Pivot Compra-Servicio, checkout de servicios, analíticas | Alto | Mantener |
| suscripcion | A | Planes activos, límites Studio, perfil | Alto | Mantener |
| telefonos_usuario | D | Sin referencias funcionales | Bajo | Candidata a eliminar |
| usuario | A | Autenticación, perfiles, roles, compras, Studio | Alto | Mantener |
| usuario_rol | B | Pivot Usuario-Rol, permisos y navegación por rol | Alto | Mantener |

## 4. Análisis detallado por tabla

### analitica

- Categoría: E, tabla a revisar manualmente.
- Evidencias: `app/Models/Analitica.php` define `protected $table = 'analitica'`; `Usuario` tiene relación `analitica()`.
- Archivos donde aparece: `app/Models/Analitica.php`, `app/Models/Usuario.php`.
- Dependencias/relaciones: relación `Usuario hasOne Analitica`.
- Riesgo: Medio.
- Recomendación: Revisar manualmente antes de borrar.
- Comentario: El controlador `AnaliticaController` calcula métricas en tiempo real usando `beat_compra`, `coleccion_compra`, `servicio_compra`, `compra_detalle`, `guardados`, `proyecto` y `conversacion`. No se detecta uso funcional de la tabla `analitica` para almacenar métricas.

### archivos

- Categoría: D, candidata a eliminación.
- Evidencias: no existe modelo `Archivo` ni consultas a tabla `archivos`. Las coincidencias encontradas son textos genéricos o rutas relacionadas con archivos de proyecto.
- Archivos donde aparece: coincidencias textuales en vistas y rutas, pero no como tabla.
- Dependencias/relaciones: ninguna detectada.
- Riesgo: Bajo.
- Recomendación: Candidata a eliminar.
- Comentario: La funcionalidad actual usa `archivos_proyecto` y almacenamiento en `Storage`, no una tabla `archivos`.

### archivos_proyecto

- Categoría: A, tabla imprescindible.
- Evidencias: `ArchivoProyecto` usa `protected $table = 'archivos_proyecto'`; `ArchivoProyectoController` crea y descarga registros; `Proyecto` tiene relación `archivos()`.
- Archivos donde aparece: `app/Models/ArchivoProyecto.php`, `app/Models/Proyecto.php`, `app/Http/Controllers/ArchivoProyectoController.php`, `StudioProyectoController`, `UsuarioEncargoController`, `resources/views/partials/project-files.blade.php`.
- Dependencias/relaciones: `Proyecto hasMany ArchivoProyecto`, `ArchivoProyecto belongsTo Usuario`.
- Riesgo: Alto.
- Recomendación: Mantener.
- Comentario: Es la tabla activa para archivos compartidos en encargos.

### beat

- Categoría: A, tabla imprescindible.
- Evidencias: modelo `Beat`, controladores públicos, Studio, Admin, carrito, compra, analíticas y vistas.
- Archivos donde aparece: `app/Models/Beat.php`, `BeatController`, `StudioBeatController`, `AdminBeatController`, `CarritoCompra`, `CompraController`, `AnaliticaController`, vistas de beat, home, search, productos, perfiles.
- Dependencias/relaciones: usuario, colecciones, compras, licencias, guardados.
- Riesgo: Alto.
- Recomendación: Mantener.
- Comentario: Tabla central del marketplace musical.

### beat_compra

- Categoría: B, pivot necesaria.
- Evidencias: relación `Compra::beats()` y `Beat::compras()`; se usa en checkout y analíticas legacy.
- Archivos donde aparece: `app/Models/Compra.php`, `app/Models/Beat.php`, `app/Http/Controllers/AnaliticaController.php`, migración `create_beat_compra_table`.
- Dependencias/relaciones: N:N Compra-Beat.
- Riesgo: Alto.
- Recomendación: Mantener.
- Comentario: Necesaria para compatibilidad con compras antiguas y descargas.

### beat_licencia

- Categoría: D, candidata a eliminación.
- Evidencias: no se detectan referencias funcionales en modelos, controladores, rutas, vistas, servicios ni migraciones actuales.
- Archivos donde aparece: ninguno en búsquedas de código funcional.
- Dependencias/relaciones: ninguna detectada.
- Riesgo: Bajo.
- Recomendación: Candidata a eliminar.
- Comentario: El sistema actual usa `licencia` como catálogo de licencias y `compra_detalle` para registrar la licencia comprada. Hay una relación `Beat::licencias()` hacia `licencia`, pero no usa `beat_licencia` y no se ha detectado uso de esa relación en flujo funcional.

### carpeta_guardado

- Categoría: D, candidata a eliminación.
- Evidencias: sin referencias funcionales.
- Archivos donde aparece: ninguno.
- Dependencias/relaciones: ninguna detectada.
- Riesgo: Bajo.
- Recomendación: Candidata a eliminar.
- Comentario: El sistema actual de favoritos usa `guardados`.

### carpeta_guardado_item

- Categoría: D, candidata a eliminación.
- Evidencias: sin referencias funcionales.
- Archivos donde aparece: ninguno.
- Dependencias/relaciones: ninguna detectada.
- Riesgo: Bajo.
- Recomendación: Candidata a eliminar.
- Comentario: No se detecta sistema activo de carpetas de guardados.

### coleccion

- Categoría: A, tabla imprescindible.
- Evidencias: modelo `Coleccion`, controladores públicos y Studio, carrito, compras, perfiles y analíticas.
- Archivos donde aparece: `app/Models/Coleccion.php`, `ColeccionController`, `StudioColeccionController`, `CarritoCompra`, `CompraController`, `AnaliticaController`, vistas de colección, search, perfil y productos.
- Dependencias/relaciones: usuario, beats, compras, guardados.
- Riesgo: Alto.
- Recomendación: Mantener.
- Comentario: Tabla central para packs/agrupaciones musicales.

### coleccion_beat

- Categoría: B, pivot necesaria.
- Evidencias: relaciones `Coleccion::beats()` y `Beat::colecciones()`.
- Archivos donde aparece: `app/Models/Coleccion.php`, `app/Models/Beat.php`, migración, seeders.
- Dependencias/relaciones: N:N Colección-Beat.
- Riesgo: Alto.
- Recomendación: Mantener.
- Comentario: Necesaria para saber qué beats pertenecen a cada colección.

### coleccion_compra

- Categoría: B, pivot necesaria.
- Evidencias: `Compra::colecciones()`, `Coleccion::compras()` y analíticas de productor.
- Archivos donde aparece: `app/Models/Compra.php`, `app/Models/Coleccion.php`, `app/Http/Controllers/AnaliticaController.php`.
- Dependencias/relaciones: N:N Compra-Colección.
- Riesgo: Alto.
- Recomendación: Mantener.
- Comentario: Necesaria para compras legacy y productos adquiridos.

### compra

- Categoría: A, tabla imprescindible.
- Evidencias: modelo `Compra`, checkout, compras, facturación, servicios y analíticas.
- Archivos donde aparece: `app/Models/Compra.php`, `CompraController`, `FacturaController`, `UsuarioController`, `UsuarioEncargoController`, `FacturaPdfService`, vistas de compra/factura/productos.
- Dependencias/relaciones: comprador, vendedor, factura, contrato, detalles, beats, colecciones, servicios.
- Riesgo: Alto.
- Recomendación: Mantener.
- Comentario: Núcleo transaccional del proyecto.

### compra_detalle

- Categoría: A, tabla imprescindible.
- Evidencias: modelo `CompraDetalle`; se crea en checkout para productos con licencia; se usa en PDFs, Mis productos y analíticas.
- Archivos donde aparece: `app/Models/CompraDetalle.php`, `CompraController`, `UsuarioController`, `FacturaPdfService`, `LicenciaCompra`, `AnaliticaController`.
- Dependencias/relaciones: pertenece a compra y licencia.
- Riesgo: Alto.
- Recomendación: Mantener.
- Comentario: Conserva snapshots históricos de producto/licencia/precio.

### contrato

- Categoría: E, revisar manualmente.
- Evidencias: modelo `Contrato`, relación `Compra::contrato()`, carga en `CompraController@detail`, vista `compra/detail.blade.php`.
- Archivos donde aparece: `app/Models/Contrato.php`, `app/Models/Compra.php`, `CompraController`, `resources/views/compra/detail.blade.php`, seeders.
- Dependencias/relaciones: `Contrato belongsTo Compra`.
- Riesgo: Medio.
- Recomendación: Mantener por ahora.
- Comentario: Aunque el sistema moderno de licencias usa `compra_detalle` y PDF de licencia, la vista de compra sigue mostrando contrato si existe. Borrarla podría romper compras históricas con contrato.

### conversacion

- Categoría: A, tabla imprescindible.
- Evidencias: modelo `Conversacion`, `MensajeDirectoController`, rutas `/mensajes`, contador de no leídos en layout.
- Archivos donde aparece: `app/Models/Conversacion.php`, `app/Http/Controllers/MensajeDirectoController.php`, `resources/views/mensajes`, `resources/views/layouts/master.blade.php`, `AnaliticaController`.
- Dependencias/relaciones: usuarios participantes y mensajes directos.
- Riesgo: Alto.
- Recomendación: Mantener.
- Comentario: Tabla central de mensajería directa.

### cupones_aplicados

- Categoría: D, candidata a eliminación.
- Evidencias: sin referencias funcionales.
- Archivos donde aparece: ninguno.
- Dependencias/relaciones: ninguna detectada.
- Riesgo: Bajo.
- Recomendación: Candidata a eliminar.
- Comentario: No hay lógica activa de cupones aplicada a checkout.

### elemento_contenido

- Categoría: D, candidata a eliminación.
- Evidencias: sin referencias funcionales.
- Archivos donde aparece: ninguno.
- Dependencias/relaciones: ninguna detectada.
- Riesgo: Bajo.
- Recomendación: Candidata a eliminar.
- Comentario: No hay modelo ni módulo conectado.

### etiqueta_beat

- Categoría: D, candidata a eliminación.
- Evidencias: sin referencias funcionales.
- Archivos donde aparece: ninguno.
- Dependencias/relaciones: ninguna detectada.
- Riesgo: Bajo.
- Recomendación: Candidata a eliminar.
- Comentario: No se detecta sistema activo de etiquetas de beat.

### factura

- Categoría: A, tabla imprescindible.
- Evidencias: modelo `Factura`, checkout crea factura, `FacturaController` lista/detalla y genera PDF, vistas de facturación.
- Archivos donde aparece: `app/Models/Factura.php`, `CompraController`, `FacturaController`, `FacturaPdfService`, vistas `factura/*`, `compra/*`, `pdf/factura.blade.php`.
- Dependencias/relaciones: `Factura belongsTo Compra`.
- Riesgo: Alto.
- Recomendación: Mantener.
- Comentario: Necesaria para facturación y PDFs.

### favoritos_usuario

- Categoría: D, candidata a eliminación.
- Evidencias: sin referencias funcionales.
- Archivos donde aparece: ninguno.
- Dependencias/relaciones: ninguna detectada.
- Riesgo: Bajo.
- Recomendación: Candidata a eliminar.
- Comentario: Parece sustituida por la tabla polimórfica `guardados`.

### guardados

- Categoría: A, tabla imprescindible.
- Evidencias: modelo `Guardado`, `GuardadoController`, vistas de guardados, botón parcial, analíticas.
- Archivos donde aparece: `app/Models/Guardado.php`, `GuardadoController`, `BeatController`, `ServicioController`, `AnaliticaController`, `resources/views/partials/btn-guardado.blade.php`, `resources/views/usuario/guardados/index.blade.php`.
- Dependencias/relaciones: relación polimórfica con beat, colección y servicio; morphMap en `AppServiceProvider`.
- Riesgo: Alto.
- Recomendación: Mantener.
- Comentario: Sistema actual de favoritos.

### impuestos_aplicados

- Categoría: D, candidata a eliminación.
- Evidencias: sin referencias funcionales.
- Archivos donde aparece: ninguno.
- Dependencias/relaciones: ninguna detectada.
- Riesgo: Bajo.
- Recomendación: Candidata a eliminar.
- Comentario: Los impuestos se calculan en checkout y se guardan en `factura`, no en esta tabla.

### licencia

- Categoría: A, tabla imprescindible.
- Evidencias: modelo `Licencia`, selector de licencias, `CarritoCompra`, `LicenciaCompra`, `CompraDetalle`, PDFs de licencia/factura.
- Archivos donde aparece: `app/Models/Licencia.php`, `app/Support/LicenciaCompra.php`, `app/Support/CarritoCompra.php`, `CompraController`, `UsuarioController`, vistas de beat/colección/productos/PDF.
- Dependencias/relaciones: `CompraDetalle belongsTo Licencia`.
- Riesgo: Alto.
- Recomendación: Mantener.
- Comentario: Tabla central del sistema de licencias básica, premium y exclusiva.

### mensaje

- Categoría: A, tabla imprescindible.
- Evidencias: modelo `Mensaje`, proyecto tiene mensajes, `MensajeProyectoController` y vistas de encargos/proyectos.
- Archivos donde aparece: `app/Models/Mensaje.php`, `app/Models/Proyecto.php`, `MensajeProyectoController`, `StudioProyectoController`, `UsuarioEncargoController`, vistas de proyectos/encargos.
- Dependencias/relaciones: emisor, receptor y proyecto.
- Riesgo: Alto.
- Recomendación: Mantener.
- Comentario: Mensajería asociada a proyectos/encargos.

### mensaje_directo

- Categoría: A, tabla imprescindible.
- Evidencias: modelo `MensajeDirecto`, `MensajeDirectoController`, layout calcula no leídos.
- Archivos donde aparece: `app/Models/MensajeDirecto.php`, `app/Models/Conversacion.php`, `MensajeDirectoController`, `resources/views/mensajes`, `layouts/master.blade.php`.
- Dependencias/relaciones: pertenece a conversación y emisor.
- Riesgo: Alto.
- Recomendación: Mantener.
- Comentario: Mensajería directa entre perfiles.

### muestra_previa

- Categoría: D, candidata a eliminación.
- Evidencias: sin referencias funcionales.
- Archivos donde aparece: ninguno.
- Dependencias/relaciones: ninguna detectada.
- Riesgo: Bajo.
- Recomendación: Candidata a eliminar.
- Comentario: No se detecta uso en servicios ni beats.

### notificacion

- Categoría: E, revisar manualmente.
- Evidencias: modelo `Notificacion` y relación `Usuario::notificaciones()`.
- Archivos donde aparece: `app/Models/Notificacion.php`, `app/Models/Usuario.php`.
- Dependencias/relaciones: relación desde Usuario.
- Riesgo: Medio.
- Recomendación: Revisar manualmente.
- Comentario: No se detectan controladores ni vistas que creen o muestren notificaciones. Parece preparada pero no usada.

### pago

- Categoría: E, revisar manualmente.
- Evidencias: modelo `Pago` y seeder; no se detecta creación de `Pago` en checkout actual.
- Archivos donde aparece: `app/Models/Pago.php`, `database/seeders/CompraSeeder.php`.
- Dependencias/relaciones: pertenece a usuario.
- Riesgo: Medio.
- Recomendación: Revisar manualmente.
- Comentario: El flujo actual usa `compra.estado_compra`, `compra.metodo_de_pago` y `factura.pago_confirmado`. La tabla puede contener histórico, pero no parece activa.

### plan

- Categoría: A, tabla imprescindible.
- Evidencias: modelo `Plan`, `PlanPorRol`, `OnboardingController`, vistas de planes, perfil y límites Studio.
- Archivos donde aparece: `app/Models/Plan.php`, `PlanPorRol.php`, `OnboardingController`, `UsuarioController`, `StudioBeatController`, `StudioServicioController`, vistas de onboarding y perfil.
- Dependencias/relaciones: `Plan hasMany PlanPorRol`.
- Riesgo: Alto.
- Recomendación: Mantener.
- Comentario: Necesaria para suscripciones y límites por rol.

### plan_por_rol

- Categoría: A, tabla imprescindible.
- Evidencias: modelo `PlanPorRol`, onboarding, suscripciones, límites de beats/servicios.
- Archivos donde aparece: `app/Models/PlanPorRol.php`, `OnboardingController`, `Usuario`, `StudioBeatController`, `StudioServicioController`, vistas de planes y perfil.
- Dependencias/relaciones: pertenece a Plan y Rol.
- Riesgo: Alto.
- Recomendación: Mantener.
- Comentario: Tabla funcional para configurar planes por rol.

### proyecto

- Categoría: A, tabla imprescindible.
- Evidencias: modelo `Proyecto`, servicios, encargos, archivos, mensajes, checkout de servicios, admin/studio.
- Archivos donde aparece: `app/Models/Proyecto.php`, `ServicioController`, `UsuarioEncargoController`, `StudioProyectoController`, `ArchivoProyectoController`, `MensajeProyectoController`, `CompraController`, vistas de encargos/proyectos/productos.
- Dependencias/relaciones: cliente, servicio, mensajes, archivos, compra, usuario cancelador.
- Riesgo: Alto.
- Recomendación: Mantener.
- Comentario: Núcleo del flujo de servicios/encargos.

### redes_sociales_usuario

- Categoría: D, candidata a eliminación.
- Evidencias: sin referencias funcionales.
- Archivos donde aparece: ninguno.
- Dependencias/relaciones: ninguna detectada.
- Riesgo: Bajo.
- Recomendación: Candidata a eliminar.
- Comentario: No conectada a perfil público ni ajustes actuales.

### revisiones_proyecto

- Categoría: D, candidata a eliminación.
- Evidencias: sin referencias funcionales.
- Archivos donde aparece: ninguno.
- Dependencias/relaciones: ninguna detectada.
- Riesgo: Bajo.
- Recomendación: Candidata a eliminar.
- Comentario: El flujo actual maneja revisiones mediante campos/estado de `proyecto` y `servicio`, no mediante esta tabla.

### rol

- Categoría: A, tabla imprescindible.
- Evidencias: modelo `Rol`, relación con Usuario, middleware admin, onboarding, perfiles, controladores Studio.
- Archivos donde aparece: `app/Models/Rol.php`, `Usuario.php`, `AdminOnly.php`, `AuthController`, `OnboardingController`, `PerfilController`, `StudioBeatController`, `StudioServicioController`, `AdminServicioController`.
- Dependencias/relaciones: N:N con usuario mediante `usuario_rol`, relación con `plan_por_rol`.
- Riesgo: Alto.
- Recomendación: Mantener.
- Comentario: Base del sistema multirol.

### servicio

- Categoría: A, tabla imprescindible.
- Evidencias: modelo `Servicio`, listado público, Studio, Admin, proyectos, carrito, compras y analíticas.
- Archivos donde aparece: `app/Models/Servicio.php`, `ServicioController`, `StudioServicioController`, `AdminServicioController`, `CarritoCompra`, `CompraController`, `Proyecto`, vistas de servicio/studio/admin/productos/guardados.
- Dependencias/relaciones: usuario, proyectos, compras.
- Riesgo: Alto.
- Recomendación: Mantener.
- Comentario: Tabla central para servicios de ingenieros.

### servicio_compra

- Categoría: B, pivot necesaria.
- Evidencias: relaciones `Compra::servicios()` y `Servicio::compras()`, checkout de servicios y analíticas.
- Archivos donde aparece: `app/Models/Compra.php`, `app/Models/Servicio.php`, `CompraController`, `AnaliticaController`.
- Dependencias/relaciones: N:N Compra-Servicio.
- Riesgo: Alto.
- Recomendación: Mantener.
- Comentario: Necesaria para servicios pagados y Mis productos.

### suscripcion

- Categoría: A, tabla imprescindible.
- Evidencias: modelo `Suscripcion`, `Usuario::suscripciones()`, onboarding, perfil, límites de Studio.
- Archivos donde aparece: `app/Models/Suscripcion.php`, `Usuario.php`, `OnboardingController`, `UsuarioController`, `StudioBeatController`, `StudioServicioController`, vistas de perfil y planes.
- Dependencias/relaciones: pertenece a `plan_por_rol`.
- Riesgo: Alto.
- Recomendación: Mantener.
- Comentario: Controla planes activos y acceso/límites profesionales.

### telefonos_usuario

- Categoría: D, candidata a eliminación.
- Evidencias: sin referencias funcionales.
- Archivos donde aparece: ninguno.
- Dependencias/relaciones: ninguna detectada.
- Riesgo: Bajo.
- Recomendación: Candidata a eliminar.
- Comentario: Los datos de contacto actuales están en `usuario`; no se detecta gestión de teléfonos.

### usuario

- Categoría: A, tabla imprescindible.
- Evidencias: modelo `Usuario`, autenticación, perfiles, roles, compras, Studio, admin y relaciones principales.
- Archivos donde aparece: `app/Models/Usuario.php`, `AuthController`, `UsuarioController`, `PerfilController`, `MensajeDirectoController`, `CompraController`, controladores Studio/Admin, vistas de perfil/cuenta/admin.
- Dependencias/relaciones: roles, beats, colecciones, compras, servicios, guardados, proyectos, mensajes, conversaciones, suscripciones.
- Riesgo: Alto.
- Recomendación: Mantener.
- Comentario: Tabla central de identidad y autenticación.

### usuario_rol

- Categoría: B, pivot necesaria.
- Evidencias: relaciones `Usuario::roles()` y `Rol::usuarios()`, middleware admin, permisos, navegación, PerfilController.
- Archivos donde aparece: `app/Models/Usuario.php`, `app/Models/Rol.php`, `AdminOnly.php`, `AuthController`, `PerfilController`, `UsuarioController`, `CompraController`, seeders y migración.
- Dependencias/relaciones: N:N Usuario-Rol con `rol_activo`.
- Riesgo: Alto.
- Recomendación: Mantener.
- Comentario: Imprescindible para multirol.

## 5. Tablas imprescindibles

No se deben borrar:

- `archivos_proyecto`
- `beat`
- `coleccion`
- `compra`
- `compra_detalle`
- `conversacion`
- `factura`
- `guardados`
- `licencia`
- `mensaje`
- `mensaje_directo`
- `plan`
- `plan_por_rol`
- `proyecto`
- `rol`
- `servicio`
- `suscripcion`
- `usuario`

## 6. Tablas pivot o de relación necesarias

No se deben borrar:

- `beat_compra`
- `coleccion_beat`
- `coleccion_compra`
- `servicio_compra`
- `usuario_rol`

## 7. Tablas candidatas a eliminación

Con la evidencia actual, son candidatas a eliminación manual tras backup y revisión de datos:

- `archivos`
- `beat_licencia`
- `carpeta_guardado`
- `carpeta_guardado_item`
- `cupones_aplicados`
- `elemento_contenido`
- `etiqueta_beat`
- `favoritos_usuario`
- `impuestos_aplicados`
- `muestra_previa`
- `redes_sociales_usuario`
- `revisiones_proyecto`
- `telefonos_usuario`

Motivo común: no se detectan modelos, controladores, rutas, vistas, servicios ni relaciones Eloquent activas que dependan de ellas.

## 8. Tablas dudosas / revisar manualmente

Conviene revisar datos antes de borrar:

- `analitica`: existe modelo y relación, pero el módulo actual calcula métricas dinámicamente.
- `contrato`: hay modelo y se muestra en detalle de compra si existe; puede contener histórico.
- `notificacion`: existe modelo y relación, pero no flujo activo detectado.
- `pago`: existe modelo y seeders, pero el flujo actual parece apoyarse en `compra` y `factura`.

## 9. SQL sugerido NO EJECUTAR SIN REVISIÓN

El siguiente bloque es únicamente una propuesta de limpieza. No debe ejecutarse sin:

1. Backup completo de la base de datos.
2. Revisión manual de datos en phpMyAdmin.
3. Comprobación de claves foráneas.
4. Prueba en entorno local antes de producción.

```sql
-- NO EJECUTAR SIN HACER BACKUP Y REVISAR
-- Tablas sin referencias funcionales detectadas en el código actual.

-- DROP TABLE IF EXISTS archivos;
-- DROP TABLE IF EXISTS beat_licencia;
-- DROP TABLE IF EXISTS carpeta_guardado_item;
-- DROP TABLE IF EXISTS carpeta_guardado;
-- DROP TABLE IF EXISTS cupones_aplicados;
-- DROP TABLE IF EXISTS elemento_contenido;
-- DROP TABLE IF EXISTS etiqueta_beat;
-- DROP TABLE IF EXISTS favoritos_usuario;
-- DROP TABLE IF EXISTS impuestos_aplicados;
-- DROP TABLE IF EXISTS muestra_previa;
-- DROP TABLE IF EXISTS redes_sociales_usuario;
-- DROP TABLE IF EXISTS revisiones_proyecto;
-- DROP TABLE IF EXISTS telefonos_usuario;
```

No se propone eliminar en este bloque:

- `analitica`, porque existe modelo y relación aunque no haya uso funcional actual.
- `contrato`, porque hay vista y relación activa para compras históricas.
- `notificacion`, porque existe modelo y relación.
- `pago`, porque existe modelo y puede contener histórico de pagos antiguos.

## 10. Conclusión

La base de datos de LevelBeats contiene un núcleo claro y activo: usuarios, roles, suscripciones, marketplace musical, servicios, proyectos, compras, facturas, licencias, mensajes y guardados. Estas tablas están conectadas mediante modelos Eloquent, controladores, rutas y vistas, por lo que deben conservarse.

También hay pivots imprescindibles: `usuario_rol`, `coleccion_beat`, `beat_compra`, `coleccion_compra` y `servicio_compra`. Estas tablas no siempre tienen controlador propio, pero son esenciales para relaciones N:N y compatibilidad con histórico de compras.

Las tablas más claramente obsoletas son las que no aparecen en ninguna referencia funcional: `archivos`, `beat_licencia`, `carpeta_guardado`, `carpeta_guardado_item`, `cupones_aplicados`, `elemento_contenido`, `etiqueta_beat`, `favoritos_usuario`, `impuestos_aplicados`, `muestra_previa`, `redes_sociales_usuario`, `revisiones_proyecto` y `telefonos_usuario`. Parecen pertenecer a versiones anteriores o módulos planificados no conectados.

Por seguridad, se recomienda conservar temporalmente `analitica`, `contrato`, `notificacion` y `pago` hasta revisar datos históricos. La tabla `contrato`, especialmente, sigue apareciendo en el detalle de compra si existe un contrato asociado.

La limpieza final debe hacerse manualmente desde phpMyAdmin o SQL controlado, nunca directamente sin backup. El orden de borrado debe respetar posibles claves foráneas, empezando siempre por tablas hijas antes de tablas padre.

