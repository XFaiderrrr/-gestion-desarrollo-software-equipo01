
**Nombre del Repositorio:** `gestion-desarrollo-software-equipo01`  
**Proyecto:** Tienda de Venta y Gestion de Videojuegos Bugistore.

**Descripción:**  
Este proyecto académico es una solución de comercio electrónico diseñada bajo una **arquitectura de microservicios**. Permite la administración desacoplada de usuarios, catálogo de productos y procesamiento de ventas. Cada servicio funciona de manera independiente, permitiendo un escalamiento modular, mantenimiento aislado y facilidades para la integración continua.

---

## Integrantes del Equipo

| Nombre Completo | Rol | Microservicio Asignado |
| :--- | :--- | :--- |
| **Uscanga Lara Ivan** | Lider de Proyecto / Backend | `servicio-usuarios` |
| **Camacho Hernandez Donovan Emir** | Backend Developer | `servicio-usuarios` |
| **Medina Estrada Ethan Issac** | Backend Developer | `servicio-productos` |
| **Alavez Hernandez Alan Cesar** | Backend Developer | `servicio-ventas` |
| **Osorio Angeles Monica Lizeth** | Backend Developer | `servicio-ventas` |

---

## Tecnologías Utilizadas

* **Lenguaje de Programación:** JavaScript, HTML, Css
* **Control de Versiones:** Git & GitHub

---

## Arquitectura y Microservicios

El sistema está organizado de manera modular. La estructura del repositorio sigue el siguiente esquema:

```text
proyecto-microservicios/
├── .gitignore
├── README.md
├── servicio-usuarios/
│   ├── src/
│   │   └── index.js
│   ├── package.json
│   └── README.md
├── servicio-productos/
│   ├── src/
│   │   └── index.js
│   ├── package.json
│   └── README.md
├── servicio-ventas/
│   ├── src/
│   │   └── index.js
│   ├── package.json
│   └── README.md
└── entregables/
    └── practica1_reporte.pdf