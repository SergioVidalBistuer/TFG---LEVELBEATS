# Memoria técnica LevelBeats - Borrador de documentación

Este documento resume la arquitectura técnica del proyecto LevelBeats a partir de la inspección del repositorio Laravel. Está pensado como base para redactar la memoria final del proyecto, por lo que evita incluir código fuente y se centra en explicar decisiones, estructura, tecnologías y flujos funcionales.

## 1. Frontend

### 1.1 Tecnologías utilizadas

El frontend de LevelBeats se construye principalmente con Blade, el motor de plantillas de Laravel, combinado con Bootstrap y una hoja de estilos propia ubicada en `public/css/style.css`. El layout principal se encuentra en `resources/views/layouts/master.blade.php` y actúa como plantilla base para las vistas del proyecto.

La interfaz utiliza HTML5, CSS3 y JavaScript estándar integrado en las vistas para interacciones concretas como menús desplegables, filtros o selectores visuales. Bootstrap se carga desde CDN en el layout principal, concretamente Bootstrap 5.3.3, y se usa como base para grid, espaciados, formularios, tablas y utilidades responsive. La identidad visual final se consigue mediante CSS propio.

El proyecto también dispone de Vite configurado en `vite.config.js`, con entradas en `resources/css/app.css` y `resources/js/app.js`, aunque la mayor parte de la interfaz inspeccionada se apoya en Blade y en `public/css/style.css`.

### 1.2 Estructura de vistas

Las vistas se organizan por módulos dentro de `resources/views`. Esta estructura permite separar las pantallas públicas, las pantallas de cuenta, Studio y administración:

- `resources/views/beat`: catálogo, detalle y formularios de beats.
- `resources/views/coleccion`: catálogo y detalle de colecciones.
- `resources/views/servicio`: listado y detalle público de servicios.
- `resources/views/perfiles`: listado y detalle de perfiles públicos.
- `resources/views/mensajes`: mensajería directa entre usuarios.
- `resources/views/usuario`: perfil, ajustes, guardados, productos y encargos.
- `resources/views/studio`: gestión profesional de beats, colecciones, servicios y proyectos.
- `resources/views/admin`: panel de administración, usuarios, pedidos, servicios, beats, colecciones y proyectos.
- `resources/views/compra`, `resources/views/carrito` y `resources/views/factura`: carrito, checkout, compras y facturación.
- `resources/views/pdf`: plantillas específicas para documentos PDF, como licencias y facturas.
- `resources/views/partials`: componentes reutilizables, como el botón de guardado y la sección de archivos de proyecto.

La mayoría de vistas extienden `layouts.master`, lo que centraliza cabecera, navegación, pie de página, carga de estilos y scripts compartidos.

### 1.3 Layout principal y navegación

El archivo `resources/views/layouts/master.blade.php` define la estructura común de la web. Incluye la navegación principal, el buscador global del header, el menú Marketplace, el acceso a Servicios, Perfiles y Contacto, el dropdown de usuario autenticado, el dropdown de Studio y el acceso al panel admin para usuarios con rol administrador.

La navegación está condicionada por autenticación y roles. Los usuarios no autenticados ven accesos públicos y login/registro. Los usuarios autenticados acceden a su área personal. Los productores e ingenieros ven opciones de Studio. Los administradores ven acceso al dashboard admin mediante una etiqueta integrada en la navbar.

El layout también contiene lógica visual para:

- Avatar/foto de perfil en el header.
- Contador de mensajes no leídos.
- Dropdowns accesibles con apertura/cierre mediante JavaScript.
- Menús diferenciados para Marketplace, Mi Área, Studio y usuario.

### 1.4 Marketplace

El Marketplace agrupa la parte pública de descubrimiento y compra de contenido:

- Beats: `resources/views/beat/index.blade.php` y `resources/views/beat/detail.blade.php`.
- Colecciones: `resources/views/coleccion/index.blade.php` y `resources/views/coleccion/detail.blade.php`.
- Servicios: `resources/views/servicio/index.blade.php` y `resources/views/servicio/detail.blade.php`.
- Perfiles públicos: `resources/views/perfiles/index.blade.php` y `resources/views/perfiles/show.blade.php`.

La interfaz del Marketplace usa cards, grids responsive, filtros visuales, botones de acción, badges y estados vacíos. En beats y colecciones se integra selección de licencia, carrito y visualización de productos publicados. En servicios se muestra una oferta profesional vinculada a ingenieros. En perfiles se listan productores e ingenieros que han activado su perfil público.

### 1.5 Panel de cuenta

El panel de cuenta concentra las funciones personales del usuario:

- Perfil: `resources/views/usuario/profile.blade.php`.
- Ajustes de cuenta: `resources/views/usuario/settings.blade.php`.
- Compras: `resources/views/compra/index.blade.php`.
- Facturación: `resources/views/factura/index.blade.php` y `resources/views/factura/detail.blade.php`.
- Mis productos: `resources/views/usuario/productos/index.blade.php`.
- Guardados: `resources/views/usuario/guardados/index.blade.php`.
- Encargos: `resources/views/usuario/encargos/index.blade.php` y `resources/views/usuario/encargos/detail.blade.php`.
- Mensajes: `resources/views/mensajes/index.blade.php` y `resources/views/mensajes/show.blade.php`.
- Analíticas para usuarios sin rol profesional: `resources/views/analiticas/index.blade.php`.

Estas pantallas mantienen el mismo lenguaje visual oscuro con paneles, tablas sobrias, botones de acción, badges de estado y mensajes de feedback.

### 1.6 Studio

Studio es el área de trabajo para roles profesionales:

- Productores: gestión de beats y colecciones.
- Ingenieros: gestión de servicios y proyectos/encargos.
- Usuarios con rol productor o ingeniero: acceso a analíticas profesionales.

Las vistas principales se encuentran en:

- `resources/views/studio/beats`
- `resources/views/studio/colecciones`
- `resources/views/studio/servicios`
- `resources/views/studio/proyectos`

Los formularios de Studio incluyen subida de archivos de audio, portadas de beats, portadas de colecciones, portadas de servicios y gestión visual de estado/publicación. Los proyectos incluyen chat, archivos compartidos, estados de aceptación/pago/cancelación y acciones específicas del flujo de servicio.

### 1.7 Panel de administración

El panel admin se estructura en:

- Dashboard: `resources/views/admin/dashboard.blade.php`.
- Usuarios: vistas de usuario y panel admin relacionado.
- Pedidos/compras: `resources/views/compra/index.blade.php` en modo admin.
- Servicios: `resources/views/admin/servicios`.
- Beats: `resources/views/admin/beats`.
- Colecciones: `resources/views/admin/colecciones`.
- Proyectos: `resources/views/admin/proyectos`.

El dashboard admin presenta métricas generales y accesos de gestión. Los listados internos comparten tabla oscura, badges, botones compactos y navegación de retorno al dashboard. La funcionalidad de auditoría fue eliminada y no forma parte del panel final.

### 1.8 Diseño responsive

El diseño responsive se apoya en Bootstrap y CSS propio. Se usan grids fluidos, contenedores flexibles, tablas con `table-responsive`, cards adaptables y media queries definidas en `public/css/style.css`.

Los formularios se apilan en móvil, las acciones se reorganizan, las tablas se hacen desplazables cuando es necesario y los menús mantienen una estructura usable en distintos anchos. La estética prioriza fondos oscuros, bordes sutiles, texto blanco/gris y morado como acento controlado.

### 1.9 Redacción final propuesta

El frontend de LevelBeats se ha desarrollado mediante Blade, el sistema de plantillas de Laravel, lo que permite generar interfaces dinámicas integradas directamente con los datos del backend. La estructura visual se centraliza en el layout `resources/views/layouts/master.blade.php`, desde el que se gestionan la navegación principal, los menús por rol, el footer y la carga de recursos compartidos.

La interfaz se organiza por módulos funcionales, separando Marketplace, cuenta de usuario, Studio y administración. Esta organización facilita el mantenimiento y permite que cada bloque de la plataforma tenga sus propias vistas sin perder coherencia visual. Para elementos reutilizables se emplean partials, como botones de guardado o secciones de archivos compartidos.

Bootstrap 5.3.3 se utiliza como base estructural para grid, formularios, tablas, espaciados y comportamiento responsive. Sobre esta base se ha construido una capa visual propia en `public/css/style.css`, responsable de la identidad de LevelBeats: estética oscura, minimalista, profesional, con bordes suaves, sombras discretas y acentos morados. Esta combinación permite aprovechar la robustez de Bootstrap sin que la interfaz tenga aspecto genérico.

La navegación se adapta al tipo de usuario. Un visitante puede acceder a Marketplace, Servicios, Perfiles, Contacto, login y registro. Un usuario autenticado accede a compras, facturación, guardados, mensajes, ajustes y productos adquiridos. Los roles profesionales, productor e ingeniero, disponen además de Studio, desde donde gestionan su catálogo, servicios, proyectos y analíticas. El rol admin/root tiene un panel específico con acceso a la gestión global de la plataforma.

El diseño responsive permite que las pantallas principales se adapten a escritorio, tablet y móvil. Las cards, tablas, formularios y menús se reorganizan para conservar legibilidad y usabilidad. Los estados visuales se resuelven con badges, botones diferenciados, mensajes de éxito/error, estados vacíos y componentes de tabla/card coherentes en toda la plataforma.

## 2. Backend

### 2.1 Arquitectura Laravel MVC

El backend está desarrollado en PHP con Laravel 12. La arquitectura sigue el patrón MVC:

- Modelos en `app/Models`, que representan entidades de base de datos y relaciones Eloquent.
- Controladores en `app/Http/Controllers`, que reciben peticiones, validan datos y coordinan la lógica de aplicación.
- Vistas en `resources/views`, generadas con Blade.

La aplicación utiliza `routes/web.php` como punto central de definición de rutas web. Los recursos públicos, privados, Studio y admin están agrupados mediante rutas y middleware.

### 2.2 Rutas

Las rutas públicas incluyen home, búsqueda, perfiles, contacto, autenticación, beats, colecciones y servicios. Las rutas protegidas por autenticación se agrupan con middleware `requirelogin`, y las rutas del panel de administración se agrupan con middleware `adminonly`.

Ejemplos de grupos funcionales:

- Público: `/beat`, `/coleccion`, `/servicios`, `/perfiles`, `/contacto`.
- Autenticado: `/carrito`, `/compra`, `/usuario`, `/mensajes`, `/analiticas`.
- Studio: `/studio/beats`, `/studio/colecciones`, `/studio/servicios`, `/studio/proyectos`.
- Admin: `/admin/dashboard`, `/admin/beats`, `/admin/colecciones`, `/admin/proyectos`, `/admin/servicios`.

### 2.3 Controladores

Los controladores principales tienen responsabilidades diferenciadas:

- `AuthController`: login, registro, logout y OAuth con Google mediante Laravel Socialite.
- `UsuarioController`: perfil, ajustes, foto de perfil, productos comprados, descargas y licencias.
- `BeatController`: catálogo público, detalle y operaciones antiguas relacionadas con beats.
- `ColeccionController`: catálogo público y detalle de colecciones.
- `ServicioController`: listado/detalle público de servicios y contacto inicial con ingenieros.
- `CarritoController`: gestión de carrito para beats y colecciones.
- `CompraController`: checkout, creación de compras, factura base y relaciones con productos.
- `FacturaController`: listado/detalle de facturas y vista previa PDF bajo demanda.
- `StudioBeatController`: creación, edición, visibilidad, audio y portada de beats en Studio.
- `StudioColeccionController`: gestión de colecciones, portada y selección de beats.
- `StudioServicioController`: creación/edición de servicios y portada.
- `StudioProyectoController`: gestión de proyectos por ingeniero.
- `UsuarioEncargoController`: vista y acciones del cliente en encargos de servicio.
- `ArchivoProyectoController`: subida y descarga de archivos compartidos de proyecto.
- `MensajeProyectoController`: mensajería vinculada a proyectos.
- `PerfilController`: listado y detalle de perfiles públicos.
- `MensajeDirectoController`: conversaciones y mensajes directos entre usuarios.
- `AnaliticaController`: cálculo dinámico de métricas por rol.
- `ContactoController`: envío del formulario de contacto mediante correo SMTP.
- Controladores admin: dashboard y listados/edición de entidades desde administración.

### 2.4 Modelos y relaciones

Los modelos principales se encuentran en `app/Models`:

- `Usuario`: usuario autenticable no estándar, con contraseña en `contrasena`, email en `direccion_correo`, roles N:N, compras, servicios, beats, colecciones, guardados, proyectos, mensajes y conversaciones.
- `Rol`: roles del sistema vinculados a usuarios por `usuario_rol`.
- `Suscripcion`, `Plan`, `PlanPorRol`: estructura de planes y suscripciones por rol.
- `Beat`: producto musical de productor, con audio, portada, licencias, compras y colecciones.
- `Coleccion`: agrupación de beats, con usuario, beats, compras y visibilidad pública.
- `Servicio`: oferta profesional de ingeniero, con proyectos y compras.
- `Compra`: compra con comprador, vendedor, factura, contrato, detalles, beats, colecciones y servicios.
- `CompraDetalle`: líneas de compra con snapshots de producto, licencia, formato, derechos y precio.
- `Factura`: factura asociada a una compra.
- `Contrato`: documento legal asociado a compra.
- `Licencia`: licencias disponibles para beats.
- `Proyecto`: encargo asociado a un servicio, con cliente, servicio, mensajes, archivos, compra y cancelación.
- `Mensaje` y `ArchivoProyecto`: comunicación y archivos de proyectos.
- `Conversacion` y `MensajeDirecto`: mensajería directa entre perfiles.
- `Guardado`: favoritos/guardados polimórficos.
- `Analitica`, `Notificacion`, `Pago`: modelos auxiliares presentes en el proyecto.

### 2.5 Roles y permisos

El sistema de roles se basa en la relación N:N entre `usuario` y `rol` mediante la tabla `usuario_rol`. El pivote incluye `rol_activo`, por lo que un usuario puede tener varios roles y activarlos/desactivarlos.

Roles principales:

- `admin`: acceso al panel de administración.
- `usuario` o artista: uso general de marketplace, compras, mensajes y cuenta.
- `productor`: gestión de beats y colecciones en Studio.
- `ingeniero`: gestión de servicios y proyectos en Studio.

Los middleware `RequireLogin` y `AdminOnly` protegen zonas privadas y de administración. Además, los controladores realizan comprobaciones de propiedad y permisos sobre recursos específicos, por ejemplo facturas, conversaciones, proyectos o elementos de Studio.

### 2.6 Compras y licencias

El flujo de compra se realiza mediante carrito y checkout. Los productos pueden ser beats o colecciones, y el sistema permite seleccionar licencias. La licencia seleccionada queda registrada en `compra_detalle`, donde se guardan snapshots del producto, licencia, formato, derechos y precio final. Esto permite mantener el histórico aunque cambien los datos originales.

Las compras antiguas siguen siendo compatibles mediante tablas pivote:

- `beat_compra`
- `coleccion_compra`
- `servicio_compra`

El modelo `Compra` centraliza relaciones con comprador, factura, contrato, detalles, beats, colecciones y servicios.

### 2.7 Servicios y proyectos

Los ingenieros publican servicios técnicos en Studio. Un usuario puede contactar por un servicio y se crea un proyecto/encargo. El proyecto incluye:

- Cliente.
- Servicio contratado.
- Estado del proyecto.
- Aceptación del ingeniero.
- Aceptación/pago del cliente.
- Cancelación.
- Mensajes.
- Archivos compartidos.

El flujo evita que el cliente pague antes de que el ingeniero acepte. La compra final del servicio se integra con el checkout existente, manteniendo compra, factura y relación en `servicio_compra`.

### 2.8 Mensajería

Existen dos tipos de mensajería:

- Mensajería de proyecto: asociada a un encargo y gestionada mediante `MensajeProyectoController`, `Mensaje` y `Proyecto`.
- Mensajería directa: independiente de proyectos, gestionada mediante `Conversacion`, `MensajeDirecto` y `MensajeDirectoController`.

La mensajería directa permite iniciar conversación desde un perfil público, listar conversaciones en `/mensajes`, ver un hilo y enviar respuestas. El acceso está protegido para que solo los participantes puedan consultar o responder una conversación.

### 2.9 Facturación PDF

El sistema de facturación usa el modelo `Factura` asociado a `Compra`. Las facturas se generan como PDF bajo demanda mediante `FacturaPdfService`, `FacturaController` y la vista `resources/views/pdf/factura.blade.php`.

El PDF se guarda en `storage/app/public/facturas` y se registra en `factura.url_factura_pdf` como ruta pública compatible con `storage/...`. La ruta de acceso a la factura es `GET /compras/{compra}/factura`, con nombre `compra.factura.download`. Aunque el nombre conserva “download” por compatibilidad, la respuesta se sirve inline para vista previa en el navegador.

El PDF incluye cabecera de LevelBeats, número de factura, fecha, estado de pago, comprador, datos de compra, líneas de producto, licencia/formato cuando aplica, importes y totales.

### 2.10 Contacto por email

El módulo de contacto se compone de:

- `ContactoController`
- `app/Mail/ContactoRecibido.php`
- `resources/views/contacto/index.blade.php`
- `resources/views/emails/contacto-recibido.blade.php`

El formulario valida nombre, email, asunto, mensaje, aceptación de privacidad y honeypot antispam. El envío usa `Mail::to(...)` con configuración SMTP obtenida desde variables de entorno. No guarda mensajes en base de datos.

### 2.11 Validaciones y seguridad

La aplicación aplica seguridad en varios niveles:

- CSRF en formularios Blade.
- Validación de formularios en controladores.
- Middleware de autenticación y administración.
- Comprobaciones de propiedad en recursos privados.
- Restricción de acceso a facturas por comprador o admin.
- Restricción de acceso a conversaciones por participante.
- Uso de variables de entorno para credenciales de Google OAuth y SMTP.
- Uso de `Storage::disk('public')` para archivos públicos gestionados por Laravel.
- No exposición de secretos en vistas ni JavaScript.

### 2.12 Redacción final propuesta

El backend de LevelBeats está desarrollado con Laravel 12 y PHP, siguiendo una arquitectura MVC. Los controladores gestionan la entrada de peticiones, validan datos y coordinan los casos de uso; los modelos representan las entidades de base de datos y sus relaciones; y las vistas Blade se encargan de la presentación.

El archivo `routes/web.php` centraliza las rutas de la aplicación. Las rutas públicas permiten consultar Marketplace, perfiles, servicios y contacto. Las rutas privadas se agrupan mediante el middleware `requirelogin`, mientras que el panel de administración utiliza `adminonly`, que verifica el rol activo `admin` a través de la relación `usuario_rol`.

El modelo `Usuario` actúa como usuario autenticable principal y adapta campos propios del esquema, como `direccion_correo` y `contrasena`. La relación con roles es de muchos a muchos, lo que permite combinar perfiles de usuario, productor, ingeniero y administrador. Esta estructura habilita navegación, permisos y funcionalidades diferenciadas.

Los módulos de negocio principales son el catálogo musical, las colecciones, los servicios profesionales, el carrito, las compras, las licencias, la facturación, los proyectos, la mensajería, los guardados y las analíticas. Las compras modernas registran sus líneas en `compra_detalle`, conservando snapshots de licencia y precio, mientras que las compras antiguas siguen siendo compatibles mediante pivotes.

Los archivos subidos se gestionan mediante el sistema de almacenamiento de Laravel. El proyecto usa el disco público para audios, portadas y facturas, y almacenamiento local para archivos de proyecto. Las facturas y licencias se generan mediante DomPDF, usando plantillas Blade específicas.

## 3. Integración y despliegue

### 3.1 Entorno local

El entorno local del proyecto se ejecuta sobre Ubuntu y utiliza Docker para levantar servicios de desarrollo. La aplicación Laravel se monta dentro de un contenedor PHP/Apache y se expone en `http://localhost:8086/public/` según el contexto proporcionado y la configuración del `docker-compose.yml` localizado en `../proyectoLaravel_docker`.

Además, el entorno local dispone de MySQL y phpMyAdmin para gestión de base de datos.

### 3.2 Docker

El fichero `../proyectoLaravel_docker/docker-compose.yml` define tres servicios:

- `php`: contenedor construido desde `Dockerfile`, con volumen hacia el proyecto Laravel y puerto `8086:80`.
- `mysql`: imagen `mysql:8.0`, base de datos `daw`, usuario `admin`, puerto local `3310`.
- `phpmyadmin`: imagen `phpmyadmin/phpmyadmin`, conectada al servicio MySQL, puerto local `8087`.

El volumen `mysqldata` persiste los datos de MySQL.

### 3.3 Apache/PHP/Laravel

El Dockerfile usa la imagen `php:8.5.2-apache`. Instala extensiones necesarias para Laravel y MySQL:

- `pdo_mysql`
- `mysqli`
- `mbstring`
- `bcmath`

También habilita `opcache`, `rewrite` y `autoindex`. La configuración Apache se define en:

- `../proyectoLaravel_docker/config/apache.conf`
- `../proyectoLaravel_docker/config/laravel.conf`

El vhost de Laravel apunta a `/var/www/html/laravel/public`.

### 3.4 MySQL/phpMyAdmin

La base de datos local se ejecuta con MySQL 8.0. phpMyAdmin está disponible en el puerto `8087`, facilitando la inspección y modificación manual de tablas durante el desarrollo.

El proyecto incluye migraciones para tablas centrales como usuario, beat, colección, compra, factura, roles, pivotes, compra_detalle y guardados. Algunas modificaciones posteriores pueden haberse aplicado manualmente en phpMyAdmin, según el historial del desarrollo.

### 3.5 Hostinger

El despliegue posterior se plantea en Hostinger con dominio propio `level-beats.com`. En producción será necesario configurar:

- Código Laravel en el hosting.
- Document root apuntando a `public`.
- Variables `.env` de producción.
- Base de datos MySQL de Hostinger.
- Correo corporativo y SMTP.
- OAuth de Google con redirect URI de producción.
- Enlace simbólico de storage si el hosting lo permite, o estrategia equivalente.

### 3.6 Dominio level-beats.com

El dominio previsto para producción es `level-beats.com`. En Google Cloud se deberá añadir la URI de redirección:

- `https://level-beats.com/auth/google/callback`

Además, `APP_URL` deberá apuntar al dominio de producción.

### 3.7 Correo SMTP contacto@level-beats.com

El formulario de contacto está preparado para usar SMTP de Hostinger con el correo corporativo `contacto@level-beats.com`. Las variables relevantes son:

- `MAIL_MAILER=smtp`
- `MAIL_HOST=smtp.hostinger.com`
- `MAIL_PORT=465`
- `MAIL_ENCRYPTION=ssl`
- `MAIL_USERNAME=contacto@level-beats.com`
- `MAIL_PASSWORD=...`
- `MAIL_FROM_ADDRESS=contacto@level-beats.com`
- `MAIL_FROM_NAME="LevelBeats"`
- `CONTACT_MAIL_TO=contacto@level-beats.com`

Las credenciales no deben almacenarse en el repositorio.

### 3.8 Variables de entorno

Variables relevantes para despliegue:

- `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`.
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
- `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`.
- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_ENCRYPTION`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`.
- `CONTACT_MAIL_TO`.
- `FILESYSTEM_DISK` si se desea modificar el disco por defecto.

### 3.9 Pasos de despliegue

Pasos habituales para producción:

1. Subir código al hosting.
2. Configurar `.env` de producción.
3. Ejecutar `composer install --no-dev --optimize-autoloader`.
4. Generar clave si no existe: `php artisan key:generate`.
5. Ejecutar migraciones si procede: `php artisan migrate`.
6. Crear enlace público de storage: `php artisan storage:link`.
7. Limpiar y regenerar cachés: `php artisan optimize:clear`, `php artisan config:clear`, `php artisan view:cache`.
8. Ejecutar build frontend si se usa Vite en producción: `npm install` y `npm run build`.
9. Configurar dominio y document root hacia `public`.
10. Configurar SMTP y OAuth en servicios externos.

Partes ya implementadas en el proyecto:

- Integración Google OAuth en Laravel.
- Formulario de contacto preparado para SMTP.
- Generación de PDF de licencias y facturas.
- Uso de storage público para archivos subidos y PDFs.
- Docker local PHP/Apache, MySQL y phpMyAdmin localizado en carpeta externa al proyecto Laravel.

Partes de despliegue posterior:

- Configurar `.env` de producción.
- Configurar base de datos real en Hostinger.
- Configurar redirect URI de Google Cloud para producción.
- Configurar dominio `level-beats.com`.
- Verificar `storage:link` o alternativa del hosting.
- Ejecutar build frontend si se decide usar assets Vite compilados.

### 3.10 Redacción final propuesta

El desarrollo local de LevelBeats se ha realizado sobre Ubuntu utilizando Docker para reproducir un entorno controlado. La infraestructura local se compone de un contenedor PHP/Apache para ejecutar Laravel, un contenedor MySQL 8.0 para la base de datos y un contenedor phpMyAdmin para administración visual.

El contenedor PHP se construye a partir de la imagen `php:8.5.2-apache`, incorporando extensiones necesarias para Laravel y MySQL. Apache se configura para servir el directorio `public` del proyecto, respetando la estructura recomendada por Laravel.

La gestión de dependencias PHP se realiza con Composer, mientras que el ecosistema frontend dispone de NPM y Vite. Las credenciales y parámetros sensibles se gestionan mediante variables de entorno, evitando exponer contraseñas o claves en el código.

Para producción se prevé desplegar en Hostinger bajo el dominio `level-beats.com`. Será necesario adaptar el archivo `.env`, configurar la base de datos MySQL del hosting, el correo corporativo `contacto@level-beats.com`, el SMTP de Hostinger y la redirección OAuth de Google. También se debe garantizar que el almacenamiento público de Laravel sea accesible, ya que se utiliza para portadas, audios y PDFs de facturas.

## 4. Versiones y herramientas

### 4.1 Tabla de tecnologías detectadas

| Tecnología | Uso dentro del proyecto | Versión detectada | Archivo donde se ha detectado | Observaciones |
|---|---|---:|---|---|
| Laravel Framework | Framework backend MVC | 12.50.0 | `composer.lock`, `php artisan --version` | Requisito en `composer.json`: `^12.0` |
| PHP requerido | Lenguaje backend requerido por Composer | ^8.2 | `composer.json` | El entorno CLI actual indica PHP 8.4.20; Docker usa imagen PHP 8.5.2 Apache |
| PHP Docker | Runtime PHP del contenedor local | 8.5.2 Apache | `../proyectoLaravel_docker/Dockerfile` | Imagen `php:8.5.2-apache` |
| MySQL | Base de datos local | 8.0 | `../proyectoLaravel_docker/docker-compose.yml` | Imagen `mysql:8.0` |
| phpMyAdmin | Administración de BD local | No fijada | `../proyectoLaravel_docker/docker-compose.yml` | Imagen `phpmyadmin/phpmyadmin` sin tag específico |
| Apache | Servidor web local | No exacta | `../proyectoLaravel_docker/Dockerfile` | Incluido en `php:8.5.2-apache`; versión exacta no fijada en el repositorio |
| Bootstrap | Base frontend responsive | 5.3.3 | `resources/views/layouts/master.blade.php` | CDN jsDelivr |
| HTML | Marcado de vistas | HTML5 | Vistas Blade | Estándar técnico, no versión de librería |
| CSS | Estilos propios | CSS3 | `public/css/style.css` | Estándar técnico, no versión de librería |
| JavaScript | Interacciones frontend | JavaScript estándar/vanilla | `resources/views/layouts/master.blade.php`, `resources/js` | No se detecta framework frontend SPA |
| Vite | Build frontend | 7.3.2 | `package-lock.json` | Requisito en `package.json`: `^7.0.7` |
| Laravel Vite Plugin | Integración Vite/Laravel | 2.1.0 | `package-lock.json` | Requisito en `package.json`: `^2.0.0` |
| Tailwind CSS | Dependencia frontend instalada | 4.2.2 | `package-lock.json` | Existe en dependencias, aunque el diseño inspeccionado usa principalmente CSS propio |
| @tailwindcss/vite | Plugin Tailwind para Vite | 4.2.2 | `package-lock.json` | Configurado en `vite.config.js` |
| Axios | Cliente HTTP JS | 1.15.1 | `package-lock.json` | Dependencia frontend |
| concurrently | Ejecución simultánea de tareas | 9.2.1 | `package-lock.json` | Usado en script `composer dev` |
| DomPDF | Generación PDF | 3.1.5 | `composer.lock` | Requisito `dompdf/dompdf:^3.1` |
| Laravel Socialite | OAuth Google | 5.27.0 | `composer.lock` | Configurado en `config/services.php` |
| Laravel Tinker | Consola interactiva | 2.11.0 | `composer.lock` | Dependencia Laravel |
| Laravel Sail | Entorno Docker Laravel | 1.52.0 | `composer.lock` | Instalado como dependencia de desarrollo, aunque Docker real observado usa configuración propia |
| PHPUnit | Tests | 11.5.51 | `composer.lock` | Dependencia de desarrollo |
| Composer | Gestión dependencias PHP | No disponible en shell actual | Comando local | `composer` no está instalado/disponible en el PATH de este entorno |
| Node.js | Runtime JS local | 24.14.1 | comando `node -v` | Versión del entorno local, no fijada en proyecto |
| NPM | Gestión dependencias JS local | 11.12.1 | comando `npm -v` | Versión del entorno local, no fijada en proyecto |
| Git | Control de versiones | 2.43.0 | comando `git --version` | Git Bash no tiene versión dentro del proyecto; se justifica como terminal para usar Git en Windows |
| Docker | Contenedores locales | No detectada | Repositorio | Hay ficheros Docker, pero no versión de Docker Engine |
| Ubuntu | Sistema de desarrollo | No detectada | Contexto de uso | Se conoce por entorno de trabajo, no por archivo del repositorio |
| Hostinger | Hosting previsto | No aplica | Contexto del proyecto | Despliegue posterior |

### 4.2 Versiones encontradas

Versiones confirmadas por archivos del proyecto:

- Laravel Framework 12.50.0.
- PHP requerido por Composer: `^8.2`.
- DomPDF 3.1.5.
- Laravel Socialite 5.27.0.
- Bootstrap 5.3.3.
- Vite 7.3.2.
- Laravel Vite Plugin 2.1.0.
- Tailwind CSS 4.2.2.
- MySQL 8.0 en Docker.
- Imagen Docker PHP `php:8.5.2-apache`.

Versiones observadas en el entorno local:

- PHP CLI 8.4.20.
- Node.js 24.14.1.
- NPM 11.12.1.
- Git 2.43.0.

### 4.3 Versiones no detectadas

No se ha encontrado versión exacta en el repositorio para:

- Apache: se usa la imagen `php:8.5.2-apache`, pero no se fija la versión exacta de Apache.
- phpMyAdmin: la imagen está declarada sin tag específico.
- Docker Engine / Docker Compose: no aparecen versiones en archivos del repositorio.
- Git Bash: no es una dependencia del proyecto. Puede mencionarse como terminal usada en Windows para ejecutar Git.
- HTML, CSS y JavaScript no tienen versión de paquete. Deben justificarse como HTML5, CSS3 y JavaScript estándar.

### 4.4 Cómo justificarlo en la memoria

Para tecnologías con versión exacta en archivos de dependencias, se puede citar la versión detectada. Para tecnologías que son estándares, como HTML, CSS y JavaScript, se debe hablar de estándares web: HTML5, CSS3 y JavaScript estándar/vanilla. Para Apache, se puede indicar que se utiliza Apache incluido en la imagen Docker `php:8.5.2-apache`, aclarando que el repositorio no fija una versión concreta del servidor Apache.

En el caso de Git Bash, no se debe tratar como dependencia de la aplicación, sino como herramienta de terminal utilizada durante el desarrollo para ejecutar comandos Git en Windows. La versión de Git detectada en el entorno local es 2.43.0, pero puede variar según la máquina.

## 5. Conclusiones técnicas

LevelBeats es una aplicación Laravel modular que combina Marketplace, gestión profesional, panel de usuario, administración, mensajería, compras, licencias, facturación PDF, perfiles públicos, analíticas y contacto por email. La arquitectura se apoya en MVC, rutas agrupadas por permisos, modelos Eloquent con relaciones reales y una capa frontend basada en Blade, Bootstrap y CSS propio.

El proyecto muestra una evolución desde funcionalidades básicas de catálogo hacia una plataforma completa con roles diferenciados. Productores e ingenieros cuentan con Studio; usuarios/artistas gestionan compras, productos y mensajes; y administradores tienen un panel de control global. La aplicación usa almacenamiento público para recursos subidos y documentos generados, y mantiene integraciones externas como Google OAuth, SMTP de Hostinger y DomPDF para documentos legales y facturación.

Desde el punto de vista de memoria técnica, los puntos más sólidos a destacar son:

- Uso de Laravel 12 con arquitectura MVC.
- Sistema multirol mediante `usuario_rol`.
- Separación clara entre Marketplace, Mi Área, Studio y Admin.
- Compra de productos con licencias y snapshots históricos.
- Flujo de servicios con proyectos, archivos y mensajería.
- Mensajería directa entre perfiles.
- Generación de PDFs profesionales de licencias y facturas.
- Contacto SMTP sin almacenamiento en BD.
- Diseño visual unificado oscuro/minimalista.
- Entorno Docker local con PHP/Apache, MySQL y phpMyAdmin.

