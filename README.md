# Travel Booking System

Proyecto final de 2º DAW desarrollado como sistema web de gestion de reservas de viajes entre usuarios.

La aplicacion permite gestionar usuarios, viajes y reservas, con acceso diferenciado para usuario normal y administrador. El objetivo del proyecto ha sido construir una base funcional, ordenada y ampliable, aplicando contenidos de DWES, DWEC, diseno responsive, base de datos y organizacion del codigo.

## Que hace el proyecto

- Registro, login, logout y recuperacion de contrasena
- Roles `user` y `admin`
- Baja logica y reactivacion de cuenta
- CRUD de viajes
- CRUD de reservas con validaciones
- Panel de usuario
- Panel de administracion
- Exportacion de reservas en CSV y PDF
- API REST basica
- Instalacion inicial desde navegador con `/setup`

## Tecnologias usadas

- PHP
- MySQL / MariaDB
- JavaScript
- Bootstrap 5
- HTML y CSS

## Estructura principal

```text
app/
  Controllers/
  Core/
  Middleware/
  Models/
  Services/
config/
database/
docs/
public/
resources/views/
routes/
storage/
tests/

