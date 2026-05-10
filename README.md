# Travel Booking System

Proyecto final de 2º DAW desarrollado como una aplicacion web de gestion de reservas de viajes entre usuarios.

El sistema permite gestionar usuarios, viajes y reservas con roles diferenciados, panel de usuario, panel de administracion y validaciones tanto en frontend como en backend.

## Funcionalidades principales

- Registro, login y logout
- Recuperacion de contrasena y reactivacion de cuenta
- Roles `user` y `admin`
- Edicion de perfil y baja logica
- CRUD de viajes
- CRUD de reservas
- Historial de reservas terminadas
- Panel de usuario
- Panel de administracion
- Exportacion en CSV y PDF
- API REST basica
- Publicacion de viajes por parte de usuarios como conductores
- Instalacion inicial desde `/setup`

## Tecnologias usadas

- PHP
- MySQL / MariaDB
- JavaScript
- Bootstrap 5
- HTML5
- CSS3

## Estructura principal

```text
app/
  Controllers/
  Core/
  Middleware/
  Models/
  Services/
bootstrap/
config/
database/
  migrations/
docs/
public/
resources/views/
routes/
storage/
tests/
```

## Instalacion recomendada desde GitHub

El proyecto ya incluye un esquema completo en `database/schema.sql`, asi que para una instalacion nueva **no hace falta ejecutar migraciones sueltas**.

### Paso 1. Copiar el archivo de entorno

```bash
cp .env.example .env
```

### Paso 2. Revisar la configuracion de base de datos

Edita `.env` y comprueba estos valores:

```dotenv
APP_BASE_URL=/Travel_Booking_System/public
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=travel_booking_system
DB_USERNAME=root
DB_PASSWORD=
DB_CHARSET=utf8mb4
```

Si en tu XAMPP MySQL usa otro puerto, cambia `DB_PORT`.

### Paso 3. Importar el proyecto con un solo comando

Instalacion simplificada:

```bash
php database/install.php
```

Si usas XAMPP en macOS:

```bash
/Applications/XAMPP/xamppfiles/bin/php database/install.php
```

Ese script:

- crea la base de datos si no existe
- importa `database/schema.sql`
- deja cargadas tablas, relaciones y datos demo

## Alternativa desde navegador

Tambien puedes usar:

```text
/setup
```

La instalacion web:

- guarda el `.env`
- crea la base de datos si hace falta
- importa el esquema completo

## URL local

```text
http://localhost/Travel_Booking_System/public
```

## Credenciales demo

### Administrador

- Email: `admin@travelbooking.local`
- Password: `Admin123!`

### Usuario

- Email: `user@travelbooking.local`
- Password: `User123!`

## Modulos del sistema

### Usuarios y autenticacion

Incluye:

- registro
- login
- logout
- recuperacion de contrasena
- reactivacion de cuenta
- edicion de perfil
- baja logica

Las contrasenas se almacenan cifradas y el acceso se controla segun el rol del usuario.

### Viajes

Los viajes pueden ser gestionados por el administrador y tambien publicados por usuarios autenticados como conductores.

Cada viaje incluye:

- nombre
- descripcion
- origen
- destino
- fecha y hora de salida
- vehiculo
- plazas disponibles
- imagen
- estado
- usuario creador, cuando aplica

### Reservas

Los usuarios pueden:

- crear reservas
- editar reservas activas
- cancelar reservas
- consultar su historial

Validaciones principales:

- no overbooking
- no duplicidad de reserva activa sobre el mismo viaje
- no conflictos de horario
- no reservas sobre viajes pasados
- no reservar un viaje publicado por el mismo usuario

Las reservas de viajes ya finalizados se mueven al historial y se muestran como `finished`.

### Panel de usuario

Desde el dashboard, el usuario puede:

- ver reservas activas
- ver historial de reservas
- gestionar su perfil
- publicar y gestionar sus propios viajes

### Panel de administracion

El administrador puede gestionar:

- usuarios
- viajes
- reservas

Tambien dispone de:

- filtros
- paginacion
- historial
- estadisticas basicas
- actividad reciente

## Base de datos

El proyecto incluye dos formas de versionado:

- `database/schema.sql` como snapshot completo listo para importar
- `database/migrations/` como referencia incremental de desarrollo

Para una instalacion nueva, la ruta recomendada es:

- `database/install.php`

No necesitas ejecutar `database/migrate.php` para una instalacion limpia desde GitHub.

## API

El proyecto incluye una API REST basica para viajes y reservas.

Documentacion:

- `docs/API.md`

## Subida de imagenes

Las imagenes de viajes se guardan en:

```text
public/uploads/trips
```

Si una subida falla en local, revisa permisos de escritura sobre esa carpeta.

## Tests

Hay tests basicos del core:

```bash
php tests/run.php
```

En macOS con XAMPP:

```bash
/Applications/XAMPP/xamppfiles/bin/php tests/run.php
```

## Documentacion adicional

- `docs/ERD.md`
- `docs/API.md`
- `docs/DEPLOYMENT.md`

## Autor

Desarrollado por **Samuel Buitrago Alonso**

## Nota final

El proyecto esta preparado como entrega academica funcional y tambien como base ampliable para seguir evolucionando el sistema.
