# Bugistore

Sistema web de gestión y venta de videojuegos desarrollado bajo una arquitectura de microservicios. El proyecto integra autenticación, gestión de usuarios, catálogo de videojuegos, compras, control de inventario, panel administrativo y una Progressive Web App (PWA).

---

## 1. Descripción del proyecto

Bugistore es una plataforma web orientada a la venta y administración de videojuegos.

El sistema fue desarrollado utilizando una arquitectura de microservicios, donde cada servicio posee una responsabilidad específica y cuenta con su propia base de datos.

La comunicación entre los servicios se realiza mediante APIs HTTP y las solicitudes externas son centralizadas mediante un API Gateway.

Además, el frontend fue convertido en una Progressive Web App (PWA), permitiendo instalar Bugistore como una aplicación y utilizar determinados recursos de manera offline mediante un Service Worker.

---

## 2. Objetivos

### Objetivo general

Desarrollar una plataforma web para la gestión y venta de videojuegos utilizando una arquitectura de microservicios que permita separar responsabilidades, facilitar el mantenimiento y mejorar la escalabilidad del sistema.

### Objetivos específicos

* Implementar autenticación y autorización de usuarios.
* Gestionar usuarios y roles.
* Administrar un catálogo de videojuegos.
* Implementar control de inventario.
* Permitir la realización de compras.
* Registrar y consultar compras realizadas.
* Implementar funciones administrativas.
* Centralizar las solicitudes mediante un API Gateway.
* Separar la información mediante bases de datos independientes.
* Implementar pruebas funcionales de los servicios.
* Configurar integración continua mediante GitHub Actions.
* Convertir el frontend en una Progressive Web App.

---

## 3. Alcance

El sistema permite:

* Registrar usuarios.
* Iniciar y cerrar sesión.
* Validar sesiones mediante tokens.
* Manejar roles de usuario y administrador.
* Consultar videojuegos.
* Consultar información individual de videojuegos.
* Crear, modificar y eliminar videojuegos mediante funciones administrativas.
* Controlar el stock disponible.
* Realizar compras.
* Consultar las compras del usuario.
* Consultar todas las compras mediante el panel administrativo.
* Administrar usuarios.
* Eliminar usuarios.
* Instalar Bugistore como aplicación PWA.
* Utilizar recursos estáticos previamente almacenados mediante caché.

Las operaciones que requieren comunicación con los microservicios necesitan conexión a Internet o a la red donde se encuentre desplegado el backend.

---

# 4. Tecnologías utilizadas

## Frontend

* HTML5
* CSS3
* JavaScript
* Progressive Web App (PWA)
* Manifest Web App
* Service Worker

## Backend

* PHP 7.3.10
* APIs HTTP
* Arquitectura de microservicios
* Apache / AppServ

## Bases de datos

* MySQL
* phpMyAdmin

## Control de versiones

* Git
* GitHub

## Integración continua

* GitHub Actions

---

# 5. Arquitectura del sistema

El proyecto utiliza una arquitectura de microservicios.

```text
                         ┌──────────────────────┐
                         │      Frontend        │
                         │ HTML / CSS / JS / PWA│
                         └──────────┬───────────┘
                                    │
                                    ▼
                         ┌──────────────────────┐
                         │     API Gateway      │
                         └──────────┬───────────┘
                                    │
              ┌─────────────────────┼─────────────────────┐
              │                     │                     │
              ▼                     ▼                     ▼
     ┌────────────────┐    ┌────────────────┐    ┌────────────────┐
     │  Auth Service  │    │  User Service  │    │  Game Service  │
     └───────┬────────┘    └───────┬────────┘    └───────┬────────┘
             │                     │                     │
             ▼                     ▼                     ▼
     ┌────────────────┐    ┌────────────────┐    ┌────────────────┐
     │ tienda_auth    │    │ tienda_users   │    │ tienda_games   │
     └────────────────┘    └────────────────┘    └────────────────┘

                                    │
                                    ▼
                         ┌──────────────────────┐
                         │  Purchase Service    │
                         └──────────┬───────────┘
                                    │
                                    ▼
                         ┌──────────────────────┐
                         │ tienda_purchases     │
                         └──────────────────────┘
```

Cada microservicio se encarga de una responsabilidad específica.

---

# 6. Estructura del proyecto

```text
-gestion-desarrollo-software-equipo01/
│
├── .github/
│   └── workflows/
│       └── ...
│
├── api-gateway/
│   └── public/
│       └── index.php
│
├── auth-service/
│   ├── config/
│   ├── controllers/
│   ├── middleware/
│   ├── models/
│   ├── public/
│   └── services/
│
├── database/
│   └── ...
│
├── frontend/
│   ├── index.html
│   ├── login.html
│   ├── registro.html
│   ├── tienda.html
│   ├── admin.html
│   ├── manifest.json
│   ├── service-worker.js
│   │
│   ├── css/
│   │   └── styles.css
│   │
│   ├── js/
│   │   ├── auth.js
│   │   ├── tienda.js
│   │   ├── admin.js
│   │   └── pwa.js
│   │
│   └── images/
│       ├── games/
│       ├── icons/
│       │   ├── icon-192.png
│       │   └── icon-512.png
│       └── README/
│
├── game-service/
│   ├── config/
│   ├── controllers/
│   ├── middleware/
│   ├── models/
│   ├── public/
│   └── services/
│
├── purchase-service/
│   ├── config/
│   ├── controllers/
│   ├── middleware/
│   ├── models/
│   ├── public/
│   └── services/
│
├── user-service/
│   ├── config/
│   ├── controllers/
│   ├── middleware/
│   ├── models/
│   ├── public/
│   └── services/
│
├── .gitignore
└── README.md
```

---

# 7. Microservicios

## 7.1 Auth Service

Responsable de la autenticación y autorización.

Funciones principales:

* Registro de usuarios.
* Inicio de sesión.
* Validación de tokens.
* Cierre de sesión.
* Expiración de tokens.
* Manejo de roles.
* Comunicación interna con User Service.

El sistema utiliza tokens Bearer para autenticar las solicitudes protegidas.

El token tiene una vigencia determinada y debe enviarse mediante el encabezado:

```text
Authorization: Bearer TOKEN
```

---

## 7.2 User Service

Responsable de la gestión de usuarios.

Funciones:

* Listar usuarios.
* Consultar usuarios.
* Actualizar información.
* Eliminar usuarios.
* Sincronizar información con Auth Service.
* Validar permisos administrativos.

Las operaciones administrativas requieren autenticación y autorización.

---

## 7.3 Game Service

Responsable del catálogo de videojuegos.

Funciones:

* Consultar todos los videojuegos.
* Consultar un videojuego.
* Crear videojuegos.
* Actualizar videojuegos.
* Eliminar videojuegos.
* Consultar y controlar stock.
* Disminuir stock durante una compra.

Las operaciones de modificación están protegidas para usuarios administradores.

---

## 7.4 Purchase Service

Responsable de las compras.

Funciones:

* Crear compras.
* Validar usuario autenticado.
* Validar existencia del videojuego.
* Validar disponibilidad de stock.
* Disminuir el inventario.
* Registrar la compra.
* Consultar compras del usuario.
* Consultar todas las compras para administradores.

Durante una compra se realiza una comunicación entre Purchase Service y Game Service para validar y disminuir el inventario.

---

# 8. API Gateway

El API Gateway funciona como punto de entrada principal para las solicitudes del frontend.

Permite centralizar las peticiones hacia los diferentes microservicios.

Las principales acciones son:

```text
?action=auth
?action=users
?action=games
?action=purchases
```

Para compras se manejan operaciones como:

```text
purchase_action=create
purchase_action=my-purchases
purchase_action=all
```

El Gateway también se encarga de recibir y reenviar los encabezados de autorización correspondientes.

---

# 9. Bases de datos

Cada microservicio utiliza una base de datos independiente.

```text
tienda_auth
tienda_users
tienda_games
tienda_purchases
```

Esta separación permite mantener aislada la información correspondiente a cada dominio del sistema.

### tienda_auth

Contiene la información necesaria para autenticación y credenciales.

### tienda_users

Contiene la información de los usuarios.

### tienda_games

Contiene el catálogo y stock de videojuegos.

### tienda_purchases

Contiene los registros de compras.

---

# 10. Frontend

El frontend está desarrollado con:

* HTML5
* CSS3
* JavaScript

Las principales páginas son:

```text
index.html
login.html
registro.html
tienda.html
admin.html
```

### Página principal

Presenta la plataforma y permite acceder a las funciones principales.

![Inicio](frontend/images/README/inicio.png)

### Login

Permite iniciar sesión mediante las credenciales registradas.

![Login](frontend/images/README/login.png)

### Registro

Permite crear una nueva cuenta.

![Registro](frontend/images/README/registro.png)

### Tienda

Presenta el catálogo de videojuegos y permite realizar compras.

![Tienda](frontend/images/README/tienda.png)

### Administración

Permite a los usuarios administradores gestionar videojuegos, usuarios y compras.

![Administración](frontend/images/README/admin.png)

---

# 11. Progressive Web App

Bugistore fue convertida en una Progressive Web App.

La implementación PWA se realizó sin modificar la arquitectura de microservicios existente.

La PWA utiliza:

```text
manifest.json
service-worker.js
js/pwa.js
images/icons/icon-192.png
images/icons/icon-512.png
```

## Manifest

El archivo `manifest.json` define la información principal de la aplicación:

```json
{
    "name": "Bugistore",
    "short_name": "Bugistore",
    "description": "Tienda web de venta y gestión de videojuegos",
    "start_url": "./index.html",
    "display": "standalone",
    "background_color": "#111111",
    "theme_color": "#111111",
    "orientation": "portrait-primary",
    "icons": [
        {
            "src": "images/icons/icon-192.png",
            "sizes": "192x192",
            "type": "image/png"
        },
        {
            "src": "images/icons/icon-512.png",
            "sizes": "512x512",
            "type": "image/png"
        }
    ]
}
```

## Service Worker

El Service Worker permite almacenar recursos estáticos de Bugistore en caché.

Se almacenan principalmente:

* Archivos HTML.
* CSS.
* JavaScript.
* Manifest.
* Iconos.

Las peticiones a los microservicios no son almacenadas por el Service Worker, debido a que operaciones como autenticación, compras y administración requieren comunicación con el backend.

## Funcionamiento offline

La aplicación puede recuperar recursos previamente almacenados aunque no exista conexión.

Sin embargo, las operaciones que requieren comunicación con los microservicios necesitan conexión:

```text
Login                 → Requiere backend
Registro              → Requiere backend
Carga del catálogo    → Requiere backend
Compra                → Requiere backend
Administración        → Requiere backend
```

La implementación offline está enfocada en la disponibilidad de los recursos estáticos.

## Iconos

La PWA utiliza:

```text
icon-192.png
icon-512.png
```

con dimensiones:

```text
192 × 192 px
512 × 512 px
```

---

# 12. Seguridad

El sistema implementa diferentes mecanismos de seguridad:

* Autenticación mediante tokens.
* Autorización mediante roles.
* Middleware para validar tokens.
* Middleware para validar administradores.
* Protección de endpoints administrativos.
* Validación de existencia de usuarios.
* Validación de videojuegos.
* Validación de stock.
* Control de acceso a las compras.
* Comunicación controlada entre microservicios.

Las credenciales no deben almacenarse directamente dentro del código fuente en un entorno de producción.

---

# 13. Flujo de autenticación

El proceso general de autenticación es:

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
Auth Service
   │
   ▼
Validación de credenciales
   │
   ▼
Generación de token
   │
   ▼
Frontend
```

Posteriormente el frontend utiliza el token para realizar solicitudes protegidas.

---

# 14. Flujo de compra

El flujo de compra es:

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
   │       └── Validación del usuario
   │
   └──► Game Service
           ├── Validar videojuego
           ├── Validar stock
           └── Disminuir stock
                    │
                    ▼
             Purchase Service
                    │
                    ▼
             Registrar compra
```

Este flujo permite mantener separadas las responsabilidades de cada servicio.

---

# 15. Funciones administrativas

Los usuarios con rol de administrador pueden acceder a funciones adicionales.

Entre ellas:

* Crear videojuegos.
* Modificar videojuegos.
* Eliminar videojuegos.
* Consultar usuarios.
* Actualizar usuarios.
* Eliminar usuarios.
* Consultar todas las compras.

Las operaciones administrativas están protegidas mediante autenticación y autorización.

---

# 16. Pruebas realizadas

Se realizaron pruebas funcionales sobre los diferentes componentes del sistema.

## Auth Service

* Registro.
* Login.
* Validación.
* Logout.
* Expiración de tokens.
* Roles.
* Eliminación de usuarios.
* Sincronización con User Service.

## User Service

* Consulta de usuarios.
* Actualización.
* Eliminación.
* Protección administrativa.
* Sincronización con Auth Service.

## Game Service

* Consulta del catálogo.
* Consulta individual.
* Creación.
* Actualización.
* Eliminación.
* Control de stock.
* Disminución de stock durante compras.

## Purchase Service

* Creación de compras.
* Validación de usuario.
* Validación de videojuegos.
* Validación de stock.
* Registro de compras.
* Consulta de compras personales.
* Consulta administrativa.

## Frontend

* Registro.
* Login.
* Navegación.
* Catálogo.
* Compra.
* Panel administrativo.
* Gestión de usuarios.
* Gestión de videojuegos.

## PWA

* Registro del Service Worker.
* Reconocimiento del Manifest.
* Validación de iconos.
* Instalación como aplicación.
* Funcionamiento en modo `standalone`.
* Almacenamiento de recursos en Cache Storage.
* Prueba offline de recursos estáticos.

---

# 17. Integración continua

El proyecto utiliza GitHub Actions para automatizar procesos de integración continua.

La configuración se encuentra dentro de:

```text
.github/workflows/
```

Los workflows permiten comprobar automáticamente el estado del proyecto ante cambios realizados en el repositorio.

---

# 18. Instalación local

Para ejecutar el proyecto localmente se requiere:

* AppServ.
* Apache.
* PHP 7.3.10.
* MySQL.
* phpMyAdmin.
* Navegador web.
* Git.

El proyecto debe colocarse dentro del directorio:

```text
C:\AppServ\www\
```

Por ejemplo:

```text
C:\AppServ\www\-gestion-desarrollo-software-equipo01
```

---

# 19. Configuración de bases de datos

Se deben crear las siguientes bases de datos:

```text
tienda_auth
tienda_users
tienda_games
tienda_purchases
```

Los archivos correspondientes se encuentran dentro del directorio:

```text
database/
```

Cada servicio debe configurarse para conectarse con su respectiva base de datos.

---

# 20. Ejecución del proyecto

Con Apache y MySQL activos en AppServ, el frontend puede accederse desde:

```text
http://localhost/-gestion-desarrollo-software-equipo01/frontend/
```

El API Gateway se encuentra dentro de:

```text
api-gateway/
```

Los diferentes microservicios se encuentran dentro de sus respectivos directorios.

---

# 21. Control de versiones

El proyecto utiliza Git para el control de versiones.

Comandos principales utilizados durante el desarrollo:

```bash
git status
```

Muestra el estado actual del repositorio.

```bash
git add .
```

Agrega los cambios al área de preparación.

```bash
git commit -m "mensaje"
```

Crea un commit con los cambios realizados.

```bash
git push
```

Envía los cambios al repositorio remoto.

```bash
git pull
```

Obtiene los cambios realizados en el repositorio remoto.

```bash
git clone
```

Permite obtener una copia del repositorio.

---

# 22. Trabajo en equipo

Para trabajar en equipo, cada integrante puede obtener el proyecto mediante Git.

Después de realizar cambios:

```bash
git add .
git commit -m "Descripción del cambio"
git push
```

Antes de comenzar a trabajar se recomienda actualizar el repositorio:

```bash
git pull
```

Cuando existen modificaciones realizadas por diferentes integrantes, Git puede requerir un proceso de merge para combinar los cambios.

---

# 23. Repositorio

Repositorio oficial del proyecto:

https://github.com/XFaiderrrr/-gestion-desarrollo-software-equipo01

---

# 24. Equipo de desarrollo

| Integrante                     | Rol                         | Microservicio    |
| ------------------------------ | --------------------------- | ---------------- |
| Uscanga Lara Ivan              | Líder de Proyecto / Backend | Auth Service     |
| Camacho Hernandez Donovan Emir | Backend Developer           | User Service     |
| Medina Estrada Ethan Issac     | Backend Developer           | Game Service     |
| Alavez Hernandez Alan Cesar    | Backend Developer           | Purchase Service |
| Osorio Angeles Monica Lizeth   | Backend Developer           | Purchase Service |

---

# 25. Evidencia de arquitectura

La estructura general del proyecto puede consultarse en:

![Arquitectura y estructura](frontend/images/README/arquitectura-estructura.png)

---

# 26. Estado final del proyecto

El proyecto Bugistore se encuentra funcional y cuenta con:

* Arquitectura de microservicios.
* Auth Service.
* User Service.
* Game Service.
* Purchase Service.
* API Gateway.
* Bases de datos independientes.
* Sistema de autenticación.
* Sistema de autorización por roles.
* Catálogo de videojuegos.
* Control de inventario.
* Sistema de compras.
* Panel administrativo.
* Frontend funcional.
* Integración continua.
* Control de versiones mediante Git y GitHub.
* Progressive Web App.
* Manifest configurado.
* Service Worker funcionando.
* Caché de recursos estáticos.
* Instalación como aplicación.
* Prueba offline realizada correctamente.
* Documentación técnica.

---

# 27. Conclusión

Bugistore cumple con el objetivo de implementar una plataforma web para la gestión y venta de videojuegos mediante una arquitectura de microservicios.

La separación de responsabilidades permite que cada servicio administre un dominio específico del sistema, mientras que el API Gateway proporciona un punto de acceso centralizado para el frontend.

La implementación de autenticación, autorización, control de inventario y gestión de compras permite cubrir las principales necesidades funcionales de la plataforma.

Finalmente, la incorporación de una Progressive Web App amplía las capacidades del sistema al permitir su instalación como aplicación y proporcionar disponibilidad offline para los recursos estáticos previamente almacenados.

El proyecto queda preparado para continuar con futuras mejoras y posibles despliegues en un entorno de producción.
