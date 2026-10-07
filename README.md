# 🎮 Bugistore

## Tienda de Venta y Gestión de Videojuegos

**Bugistore** es una aplicación web de comercio electrónico desarrollada como proyecto académico para la venta y gestión de videojuegos.

El sistema utiliza una **arquitectura basada en microservicios**, separando las principales funcionalidades de la aplicación en servicios independientes para autenticación, usuarios, videojuegos y compras.

El proyecto fue desarrollado utilizando:

* HTML5
* CSS3
* JavaScript
* PHP
* MySQL
* Apache / AppServ
* Git
* GitHub
* GitHub Actions

---

# 👥 Equipo de desarrollo

| Integrante                         | Rol                         | Microservicio principal |
| ---------------------------------- | --------------------------- | ----------------------- |
| **Uscanga Lara Ivan**              | Líder de Proyecto / Backend | `auth-service`          |
| **Camacho Hernandez Donovan Emir** | Backend Developer           | `user-service`          |
| **Medina Estrada Ethan Issac**     | Backend Developer           | `game-service`          |
| **Alavez Hernandez Alan Cesar**    | Backend Developer           | `purchase-service`      |
| **Osorio Angeles Monica Lizeth**   | Backend Developer           | `purchase-service`      |

---

# 🏗️ Arquitectura del sistema

Bugistore utiliza una arquitectura basada en microservicios.

El usuario interactúa con el sistema mediante el frontend. Las solicitudes son enviadas al **API Gateway**, que funciona como punto de entrada principal y se encarga de dirigir cada petición al microservicio correspondiente.

Los principales servicios son:

* **Auth Service:** autenticación y autorización.
* **User Service:** gestión de usuarios.
* **Game Service:** catálogo e inventario.
* **Purchase Service:** procesamiento de compras.

Cada microservicio utiliza su propia base de datos.

### Diagrama de arquitectura

![Arquitectura de Bugistore](frontend/images/readme/arquitectura-estructura.png)

---

# 📁 Estructura del proyecto

La estructura del proyecto se encuentra organizada de acuerdo con las responsabilidades de cada microservicio.

```text
-gestion-desarrollo-software-equipo01/
│
├── .github/
│   └── workflows/
│       └── ci.yml
│
├── api-gateway/
│   ├── public/
│   │   └── index.php
│   ├── routes/
│   │   ├── auth.php
│   │   ├── users.php
│   │   ├── games.php
│   │   └── purchases.php
│   └── services/
│
├── auth-service/
│   ├── config/
│   ├── controllers/
│   ├── middleware/
│   ├── models/
│   ├── routes/
│   └── public/
│
├── user-service/
│   ├── config/
│   ├── controllers/
│   ├── middleware/
│   ├── models/
│   ├── routes/
│   ├── services/
│   └── public/
│
├── game-service/
│   ├── config/
│   ├── controllers/
│   ├── middleware/
│   ├── models/
│   ├── routes/
│   └── public/
│
├── purchase-service/
│   ├── config/
│   ├── controllers/
│   ├── middleware/
│   ├── models/
│   ├── routes/
│   ├── services/
│   └── public/
│
├── frontend/
│   ├── index.html
│   ├── login.html
│   ├── registro.html
│   ├── tienda.html
│   ├── admin.html
│   │
│   ├── css/
│   │   └── styles.css
│   │
│   ├── js/
│   │   ├── auth.js
│   │   ├── tienda.js
│   │   └── admin.js
│   │
│   └── images/
│       ├── games/
│       └── readme/
│
├── database/
│   ├── auth.sql
│   ├── users.sql
│   ├── games.sql
│   └── purchases.sql
│
├── .gitignore
└── README.md
```

### Vista gráfica de la estructura

La siguiente imagen muestra de manera visual la organización general del proyecto y sus principales componentes:

![Estructura del proyecto Bugistore](frontend/images/readme/arquitectura-estructura.png)

---

# 🔐 Auth Service

El `auth-service` se encarga de la autenticación y autorización de los usuarios.

Sus principales funciones son:

* Registro de usuarios.
* Inicio de sesión.
* Generación de tokens.
* Validación de tokens.
* Cierre de sesión.
* Control de roles.
* Comunicación interna con `user-service`.

Los usuarios pueden tener los siguientes roles:

```text
usuario
admin
```

Las solicitudes protegidas utilizan el siguiente formato:

```text
Authorization: Bearer TOKEN
```

---

# 👤 User Service

El `user-service` administra la información de los usuarios.

Permite:

* Listar usuarios.
* Consultar usuarios.
* Actualizar usuarios.
* Eliminar usuarios.
* Mantener sincronizada la información relacionada con autenticación.

Las operaciones administrativas están protegidas mediante autenticación y autorización por roles.

---

# 🎮 Game Service

El `game-service` administra el catálogo de videojuegos y el inventario.

Permite:

* Consultar todos los videojuegos.
* Consultar un videojuego.
* Crear videojuegos.
* Editar videojuegos.
* Eliminar videojuegos.
* Consultar existencias.
* Disminuir el stock después de una compra.

El catálogo incluye videojuegos de diferentes géneros y plataformas.

---

# 🛒 Purchase Service

El `purchase-service` se encarga del procesamiento de las compras.

El flujo principal es:

1. El usuario selecciona un videojuego.
2. Se valida su autenticación.
3. Se consulta la información del videojuego.
4. Se verifica que exista stock suficiente.
5. Se disminuye el inventario.
6. Se registra la compra.
7. La compra queda disponible en el historial del usuario.

Los administradores también pueden consultar las compras realizadas dentro del sistema.

---

# 🌐 API Gateway

El `api-gateway` funciona como punto de entrada principal para las peticiones del frontend.

El frontend no necesita comunicarse directamente con cada microservicio.

```text
Frontend
   │
   ▼
API Gateway
   │
   ├──► Auth Service
   │
   ├──► User Service
   │
   ├──► Game Service
   │
   └──► Purchase Service
```

Esto permite centralizar las peticiones y mantener una comunicación organizada entre el frontend y los servicios.

---

# 🗄️ Bases de datos

Cada microservicio mantiene su propia base de datos.

```text
auth-service
└── tienda_auth

user-service
└── tienda_users

game-service
└── tienda_games

purchase-service
└── tienda_purchases
```

Esta separación permite mantener los servicios desacoplados y facilita la administración independiente de cada componente.

---

# 🛍️ Funcionalidades del sistema

## 👤 Usuario

El usuario puede:

* Registrarse.
* Iniciar sesión.
* Consultar el catálogo.
* Visualizar videojuegos.
* Realizar compras.
* Consultar su historial de compras.
* Cerrar sesión.

---

## 👑 Administrador

El administrador cuenta con funcionalidades adicionales.

### Gestión de usuarios

* Consultar usuarios.
* Crear usuarios.
* Editar usuarios.
* Eliminar usuarios.

### Gestión de videojuegos

* Consultar inventario.
* Agregar videojuegos.
* Editar videojuegos.
* Eliminar videojuegos.

### Gestión de compras

* Consultar las compras realizadas por los usuarios.

---

# 🔒 Seguridad

El sistema implementa mecanismos de autenticación y autorización.

### Autenticación

Las operaciones protegidas requieren un token:

```text
Authorization: Bearer TOKEN
```

### Autorización por roles

Las operaciones administrativas requieren el rol:

```text
admin
```

Los usuarios normales no pueden acceder a las funciones administrativas.

### Protección de endpoints

Los servicios utilizan middleware para validar:

* Existencia del token.
* Validez del token.
* Usuario autenticado.
* Rol requerido.

---

# 🔄 Flujo de una compra

El proceso de compra sigue el siguiente flujo:

```text
Usuario
   │
   ▼
Frontend
   │
   ▼
API Gateway
   │
   ▼
Purchase Service
   │
   ├──► Auth Service
   │       │
   │       └── Validación del usuario
   │
   ├──► Game Service
   │       │
   │       ├── Consulta videojuego
   │       └── Disminución de stock
   │
   ▼
Purchase Database
   │
   ▼
Compra registrada
```

De esta manera, la compra afecta tanto al registro de la venta como al inventario disponible.

---

# 🧪 Pruebas realizadas

Durante el desarrollo se realizaron pruebas de los diferentes componentes del sistema.

### Autenticación

* Registro de usuarios.
* Inicio de sesión.
* Validación de tokens.
* Cierre de sesión.
* Validación de roles.

### Usuarios

* Listado.
* Consulta.
* Actualización.
* Eliminación.

### Videojuegos

* Listado.
* Consulta.
* Creación.
* Actualización.
* Eliminación.
* Disminución de stock.

### Compras

* Creación de compras.
* Validación de stock.
* Registro de compras.
* Historial del usuario.
* Consulta administrativa.

### Seguridad

* Bloqueo de funciones administrativas para usuarios normales.
* Validación mediante Bearer Token.

Las pruebas se realizaron mediante navegador y terminal utilizando solicitudes HTTP.

---

# 💻 Instalación y ejecución

## Requisitos

Para ejecutar el proyecto localmente se requiere:

* AppServ.
* Apache.
* MySQL.
* PHP 7.3 o superior.
* Git.
* Navegador web.

---

## 📥 Clonar el repositorio

Clonar el proyecto dentro del directorio `www` de AppServ:

```bash
git clone https://github.com/XFaiderrrr/-gestion-desarrollo-software-equipo01.git
```

La ubicación utilizada durante el desarrollo fue:

```text
C:\AppServ\www\-gestion-desarrollo-software-equipo01
```

---

# 🗄️ Configuración de las bases de datos

Los scripts SQL se encuentran dentro de:

```text
database/
```

Los archivos disponibles son:

```text
auth.sql
users.sql
games.sql
purchases.sql
```

Las bases de datos utilizadas son:

```text
tienda_auth
tienda_users
tienda_games
tienda_purchases
```

---

# 🌐 Ejecución local

Una vez iniciados Apache y MySQL desde AppServ, el proyecto puede accederse desde:

```text
http://localhost/-gestion-desarrollo-software-equipo01/
```

El frontend se encuentra dentro de:

```text
frontend/
```

---

# 📸 Capturas del sistema

A continuación se muestran algunas de las principales interfaces desarrolladas para Bugistore.

## 🏠 Página de inicio

La página principal presenta el acceso general al sistema.

![Página de inicio de Bugistore](frontend/images/readme/inicio.png)

---

## 🔐 Inicio de sesión

Los usuarios pueden iniciar sesión utilizando sus credenciales.

![Inicio de sesión de Bugistore](frontend/images/readme/login.png)

---

## 📝 Registro de usuarios

Los nuevos usuarios pueden crear una cuenta desde el formulario de registro.

![Registro de usuarios de Bugistore](frontend/images/readme/registro.png)

---

## 🎮 Tienda

La tienda muestra el catálogo disponible y permite a los usuarios realizar compras.

![Catálogo de videojuegos de Bugistore](frontend/images/readme/tienda.png)

---

## 👑 Panel de administración

El administrador dispone de herramientas para gestionar usuarios, videojuegos e información relacionada con las compras.

![Panel de administración de Bugistore](frontend/images/readme/admin.png)

---

# 🔄 Integración continua

El proyecto utiliza **GitHub Actions** para realizar comprobaciones automáticas.

El workflow se encuentra en:

```text
.github/workflows/ci.yml
```

El proceso se ejecuta mediante eventos como:

* `push`
* `pull_request`

El entorno de integración utiliza:

* Ubuntu.
* PHP 7.3.
* MySQL 8.0.
* Extensiones `pdo` y `pdo_mysql`.

Esto permite comprobar automáticamente la configuración del proyecto durante el desarrollo.

---

# 🌿 Control de versiones

El proyecto utiliza Git y GitHub para controlar las versiones del código.

El flujo general utilizado fue:

```text
Desarrollo local
      │
      ▼
Git
      │
      ▼
Commit
      │
      ▼
GitHub
      │
      ▼
GitHub Actions
      │
      ▼
Validación automática
```

---

# 📊 Estado final del proyecto

## Usuario

* [x] Registro
* [x] Login
* [x] Catálogo
* [x] Compra
* [x] Descuento de stock
* [x] Historial de compras

## Administrador

* [x] Login administrativo
* [x] Listar usuarios
* [x] Crear usuarios
* [x] Editar usuarios
* [x] Eliminar usuarios
* [x] Listar videojuegos
* [x] Crear videojuegos
* [x] Editar videojuegos
* [x] Eliminar videojuegos
* [x] Ver compras

## Seguridad

* [x] Autenticación mediante token
* [x] Autorización por roles
* [x] Protección de endpoints
* [x] Bloqueo de funciones administrativas para usuarios normales

## Microservicios

* [x] Auth Service
* [x] User Service
* [x] Game Service
* [x] Purchase Service
* [x] API Gateway

## Infraestructura

* [x] MySQL
* [x] Apache
* [x] AppServ
* [x] GitHub
* [x] GitHub Actions

---

# 🎯 Objetivo del proyecto

El objetivo principal de Bugistore es desarrollar una aplicación web funcional para la venta y gestión de videojuegos utilizando una arquitectura de microservicios.

El proyecto permite aplicar conocimientos relacionados con:

* Desarrollo web.
* APIs REST.
* PHP.
* JavaScript.
* MySQL.
* Arquitectura de microservicios.
* Autenticación.
* Autorización.
* Control de inventario.
* Procesamiento de compras.
* Git y GitHub.
* Integración continua.

---

# 🏁 Conclusión

Bugistore integra un sistema completo de venta y gestión de videojuegos mediante una arquitectura distribuida en diferentes microservicios.

La separación de responsabilidades permite que cada servicio gestione una parte específica del sistema, manteniendo independientes la autenticación, los usuarios, el catálogo y las compras.

El proyecto cuenta con:

* Frontend funcional.
* API Gateway.
* Microservicios.
* Bases de datos independientes.
* Autenticación mediante tokens.
* Control de acceso por roles.
* Gestión de inventario.
* Procesamiento de compras.
* Historial de compras.
* Panel administrativo.
* Integración continua mediante GitHub Actions.

De esta manera, Bugistore cumple con los objetivos establecidos para el proyecto académico y demuestra la implementación práctica de una arquitectura de microservicios aplicada a una tienda web de videojuegos
