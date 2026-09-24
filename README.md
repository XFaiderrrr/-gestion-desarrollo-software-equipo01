# tiendaVideojuegos_mc

**Nombre del Repositorio:** `tiendaVideojuegos_mc`
**Proyecto:** Tienda de Venta y Gestión de Videojuegos.

## Descripción

Este proyecto académico consiste en el desarrollo de una plataforma web para la venta y gestión de videojuegos, diseñada bajo una **arquitectura de microservicios**.

El sistema permite a los usuarios registrarse, iniciar sesión, consultar el catálogo de videojuegos y simular compras. Por otra parte, los usuarios con rol de administrador pueden gestionar usuarios e inventario mediante operaciones de consulta, registro, actualización y eliminación.

La arquitectura divide las funcionalidades del sistema en servicios independientes, permitiendo un desarrollo modular, mantenimiento aislado y una mejor separación de responsabilidades.

---

## Integrantes del Equipo

| **Nombre Completo**                | **Rol**                     | **Microservicio Asignado** |
| ---------------------------------- | --------------------------- | -------------------------- |
| **Uscanga Lara Ivan**              | Líder de Proyecto / Backend | `auth-service`             |
| **Camacho Hernandez Donovan Emir** | Backend Developer           | `user-service`             |
| **Medina Estrada Ethan Issac**     | Backend Developer           | `game-service`             |
| **Alavez Hernandez Alan Cesar**    | Backend Developer           | `purchase-service`         |
| **Osorio Angeles Monica Lizeth**   | Backend Developer           | `purchase-service`         |

> Los roles y microservicios pueden modificarse conforme avance el desarrollo y la distribución de actividades del equipo.

---

## Tecnologías Utilizadas

* **Frontend:** HTML5, CSS3 y JavaScript.
* **Backend:** PHP.
* **Base de Datos:** MySQL.
* **Servidor Web:** Apache mediante AppServ.
* **Control de Versiones:** Git y GitHub.
* **Arquitectura:** Microservicios.
* **Comunicación:** APIs HTTP/REST.

---

## Arquitectura y Microservicios

El sistema se encuentra organizado mediante una arquitectura de microservicios, donde cada servicio tiene una responsabilidad específica y puede desarrollarse y mantenerse de manera independiente.

La estructura principal del repositorio será:

```text
tiendaVideojuegos_mc/
├── .gitignore
├── README.md
│
├── frontend/
│   ├── index.html
│   ├── login.html
│   ├── registro.html
│   ├── css/
│   │   └── styles.css
│   └── js/
│       ├── app.js
│       ├── login.js
│       └── registro.js
│
├── api-gateway/
│   └── index.php
│
├── auth-service/
│   ├── config/
│   │   └── database.php
│   ├── controllers/
│   │   └── AuthController.php
│   ├── models/
│   │   └── User.php
│   ├── routes/
│   │   └── auth.php
│   └── public/
│       └── index.php
│
├── user-service/
│   ├── config/
│   ├── controllers/
│   ├── models/
│   ├── routes/
│   └── public/
│
├── game-service/
│   ├── config/
│   ├── controllers/
│   ├── models/
│   ├── routes/
│   └── public/
│
├── purchase-service/
│   ├── config/
│   ├── controllers/
│   ├── models/
│   ├── routes/
│   └── public/
│
└── database/
    ├── auth.sql
    ├── users.sql
    ├── games.sql
    └── purchases.sql
```

---

## Flujo General del Sistema

La comunicación entre los componentes seguirá, de manera general, el siguiente flujo:

```text
Usuario
   │
   ▼
Frontend
   │
   ▼
API Gateway
   │
   ├──────────────► auth-service
   │
   ├──────────────► user-service
   │
   ├──────────────► game-service
   │
   └──────────────► purchase-service
```

Cada microservicio será responsable de su propia lógica de negocio y de los datos relacionados con su función.

---

## Microservicios

### 1. Auth Service

El `auth-service` será responsable de la autenticación de los usuarios.

### Funcionalidades

* Registro de usuarios.
* Inicio de sesión.
* Cierre de sesión.
* Validación de sesión.
* Generación y validación de tokens.
* Manejo de roles de usuario.

Los roles contemplados inicialmente serán:

```text
usuario
admin
```

---

### 2. User Service

El `user-service` será responsable de la administración de los usuarios del sistema.

### Funcionalidades

* Consulta de usuarios.
* Consulta de información de usuarios.
* Administración de roles.
* Actualización de información.
* Eliminación de usuarios.

El acceso a las funciones administrativas estará restringido al rol `admin`.

---

### 3. Game Service

El `game-service` será responsable de la gestión del catálogo e inventario de videojuegos.

### Funcionalidades

* Consultar videojuegos.
* Registrar videojuegos.
* Editar videojuegos.
* Eliminar videojuegos.
* Consultar inventario.
* Actualizar existencias.
* Mostrar información del producto.

---

### 4. Purchase Service

El `purchase-service` será responsable de gestionar las compras simuladas realizadas por los usuarios.

### Funcionalidades

* Registrar compras.
* Procesar compras simuladas.
* Consultar historial de compras.
* Asociar compras con usuarios.
* Actualizar existencias relacionadas con una compra.

---

## Roles del Sistema

### Usuario

El usuario podrá:

* Registrarse.
* Iniciar sesión.
* Consultar la tienda.
* Ver videojuegos.
* Simular compras.
* Consultar sus compras.

### Administrador

El administrador podrá:

* Iniciar sesión.
* Consultar usuarios.
* Gestionar usuarios.
* Consultar inventario.
* Agregar videojuegos.
* Editar videojuegos.
* Eliminar videojuegos.
* Gestionar existencias.

---

## Base de Datos

El proyecto utilizará **MySQL** como sistema de gestión de bases de datos.

Se plantea una separación lógica de datos por microservicio:

```text
tienda_auth
tienda_users
tienda_games
tienda_purchases
```

Cada base de datos estará relacionada con las responsabilidades del microservicio correspondiente.

---

## Entorno de Desarrollo

El proyecto se desarrollará localmente utilizando **AppServ**, que proporcionará el servidor Apache, PHP y MySQL necesarios para ejecutar la aplicación.

La ubicación local del proyecto será:

```text
C:\AppServ\www\tiendaVideojuegos_mc
```

La aplicación será accesible mediante:

```text
http://localhost/tiendaVideojuegos_mc/
```

---

## Control de Versiones

El proyecto utiliza **Git y GitHub** para llevar el control de versiones y facilitar el trabajo colaborativo.

Flujo básico de trabajo:

```text
Modificar código
      ↓
git status
      ↓
git add .
      ↓
git commit
      ↓
git push
```

Para obtener los cambios realizados por otros integrantes:

```text
git pull
```

El repositorio permitirá mantener un historial de cambios y facilitar la integración del trabajo realizado por los diferentes integrantes del equipo.

---

## Estado del Proyecto

**Fase actual:** Configuración inicial del proyecto.

### Progreso

* [x] Definición de la arquitectura.
* [x] Definición de microservicios.
* [x] Creación de la estructura inicial.
* [ ] Configuración de `auth-service`.
* [ ] Registro de usuarios.
* [ ] Login.
* [ ] Implementación de roles.
* [ ] `user-service`.
* [ ] `game-service`.
* [ ] `purchase-service`.
* [ ] `api-gateway`.
* [ ] Desarrollo del frontend.
* [ ] Integración completa.
* [ ] Pruebas.
* [ ] Documentación final.

---

## Objetivo

Desarrollar una aplicación web funcional para la venta y gestión de videojuegos utilizando una arquitectura de microservicios, aplicando buenas prácticas de desarrollo, control de versiones y separación de responsabilidades.

El proyecto busca demostrar el funcionamiento de una aplicación distribuida mediante servicios independientes que puedan comunicarse entre sí para proporcionar las funcionalidades de la plataforma.


## Integración Continua

El proyecto utiliza GitHub Actions para validar automáticamente el código mediante compilación/validación, pruebas y generación de artefactos en cambios realizados mediante `push` y Pull Requests hacia `main`.