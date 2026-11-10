# HuatulcoTravel — Plataforma de Hospedaje y Experiencias

Plataforma integral de reservas, hospedaje y experiencias turísticas de lujo en Bahías de Huatulco, Oaxaca. Desarrollada en **ASP.NET Core MVC (.NET 10)** con **PostgreSQL**, **Entity Framework Core**, **Three.js (WebGL 360°)** y **Leaflet con ArcGIS Satellite Imagery**.

---

## 🌟 Características Principales

### 1. Landing Satelital Interactiva (WebGL & GIS)
- **Vista Aérea y Zoom Satelital**: Animación fluida de aproximación desde el espacio hasta las 9 bahías de Huatulco utilizando **Leaflet** y satélite de alta resolución **ArcGIS Esri World Imagery**.
- **Marcadores Pulsantes**: Coordenadas geográficas reales con anillos dorados pulsantes y ventanas modales de información, fotografías y tarifas en vivo.
- **Contador Dinámico**: Métrica en tiempo real de propiedades activas en el mapa.

### 2. Catálogo de Alojamientos y Experiencias 3D
- **Tarjetas de Propiedad Responsivas**: Diseño limpio con capacidad de huéspedes, recámaras, baños y tarifa por noche en pesos mexicanos (MXN).
- **Visor Fotográfico Modal (`📷 Fotos`)**: Carrusel interactivo con tira de miniaturas y contador de fotografías.
- **Recorrido Virtual 360° (`🌐 Recorrido 3D`)**: Experiencia inmersiva esférica con **Three.js** con soporte táctil para rotar con el dedo en celulares, zoom y selector de ambientes (Sala principal, Recámara Máster, Alberca con vista al mar).
- **Sembrado Inicial**: 8 propiedades de muestra con fotos reales y coordenadas en Tangolunda, Marina Chahué, Península Conejos, Santa Cruz, La Bocana, Bahía Maguey y Playa Arrocito.

### 3. Ficha de Detalle y Navegación GPS en Tiempo Real
- **Hero Banner Panorámico**: Portada con accesos directos flotantes a la galería y tour 3D.
- **Distribución Móvil 2x2**: Especificaciones clave (Capacidad, Habitaciones, Baños y Tipo) estructuradas de forma simétrica para celulares.
- **Tarjeta de Reservación Integrada**: Muestra el desglose de precios, disponibilidad y enlace directo de reservación por WhatsApp.
- **Ruta GPS en Vivo**:
  - Botón **`📍 Iniciar camino desde mi ubicación`**: Utiliza el GPS real de tu dispositivo para trazar una polilínea hacia la propiedad, calcular la distancia en kilómetros y el tiempo estimado en vehículo, con enlace a Google Maps.

### 4. Panel de Administración de Lujo (`/Admin/Alojamientos`)
- **Diseño Visual Huatulco Premium**: Interfaz moderna con la paleta de colores corporativa (tonos esmeralda, dorado y arena), lejos de formatos en texto plano.
- **Tarjetas KPI**: Métricas inmediatas de total de residencias, porcentaje activo, tarifa promedio por noche y capacidad total de huéspedes.
- **Tabla Responsiva de Control**: Miniaturas de imágenes, insignias de estado (*Publicado*, *Borrador*), especificaciones rápidas y botones de acción rápida (`👁️ Ver en web`, `✏️ Editar`, `🗑️ Desactivar`).
- **Formularios Estructurados (`Crear` y `Editar`)**:
  - Subida múltiple de fotografías (`JPG`, `PNG`, `WEBP`) con validación de magic bytes (máx. 5 MB por archivo).
  - Configuración de coordenadas geográficas (Latitud y Longitud) para geolocalización en mapa satelital.
  - Baja lógica (*soft delete*) para proteger el historial sin pérdida de datos.

### 5. Control de Acceso y Roles (ASP.NET Core Identity)
- **Administrador Único**: **Únicamente** la cuenta oficial `admin@huatulcotravel.com` posee el rol de `Administrador`.
- **Registro Público de Clientes**: Todo usuario que se registre a través del sitio web recibe exclusivamente el rol de `Cliente` (visitante/huésped). Los clientes no tienen acceso al panel de administración.
- **Protección contra Fuerza Bruta**: Bloqueo temporal configurable ante múltiples intentos fallidos de inicio de sesión.

---

## 🔐 Credenciales del Sistema

| Rol | Correo Electrónico | Contraseña | Permisos |
| :--- | :--- | :--- | :--- |
| **Administrador Único** | `admin@huatulcotravel.com` | `Admin1234#*` | Acceso completo al Panel de Administración (`/Admin/Alojamientos`), creación, edición y baja de residencias. |
| **Cliente / Visitante** | *(Cualquiera registrado en `/Cuenta/Registro`)* | *(La definida por el usuario)* | Exploración del catálogo, visualización de tours 3D, navegación GPS y solicitudes de reservación. |

---

## 🛠️ Requisitos Previos

- [.NET 10 SDK](https://dotnet.microsoft.com/download)
- [PostgreSQL 15+](https://www.postgresql.org/download/) en ejecución local (puerto estándar `5432`).
- [Git](https://git-scm.com/)

---

## 🚀 Guía de Instalación y Ejecución

### 1. Clonar el repositorio
```bash
git clone https://github.com/edgar-106/HUATULCO.git
cd HUATULCO
```

### 2. Configurar la Base de Datos
Asegúrate de que el servicio de PostgreSQL esté activo. Crea la base de datos desde `psql` o `pgAdmin`:
```sql
CREATE DATABASE huatulco_travel_db;
```

Verifica la cadena de conexión en `appsettings.json`. Si tu contraseña de PostgreSQL es diferente a `postgres`, cámbiala:
```json
"ConnectionStrings": {
  "DefaultConnection": "Host=localhost;Port=5432;Database=huatulco_travel_db;Username=postgres;Password=TU_PASSWORD"
}
```

### 3. Aplicar Migraciones y Sembrado de Datos
Si no tienes la herramienta de Entity Framework Core instalada globalmente:
```bash
dotnet tool install --global dotnet-ef
```

Aplica las migraciones:
```bash
dotnet ef database update
```
*(Al iniciar por primera vez, el sistema ejecutará automáticamente `SeedData.cs`, creando el usuario administrador y las propiedades de muestra).*

### 4. Iniciar el Servidor Web
```bash
dotnet run
```

La aplicación se iniciará vinculada a todas las interfaces (`http://0.0.0.0:5000`):
- **Desde tu PC**: 👉 [http://localhost:5000](http://localhost:5000)
- **Desde tu Celular o Tablet en la misma red Wi-Fi**: 👉 `http://<TU_IP_LOCAL>:5000` (Ejemplo: `http://192.168.0.106:5000`)

---

## 📱 Cómo Probar el Sistema desde tu Celular

1. Conecta tu teléfono a la **misma red Wi-Fi** que la computadora donde corre el proyecto.
2. Obtén la IP local de tu PC ejecutando en PowerShell o CMD:
   ```powershell
   ipconfig
   ```
   *(Busca el campo `Dirección IPv4` de tu adaptador Wi-Fi, por ejemplo: `192.168.0.106`).*
3. Abre el navegador de tu celular (Google Chrome, Safari, Firefox) e ingresa:
   ```text
   http://192.168.0.106:5000
   ```
4. **Verificación de experiencia móvil**:
   - **Menú Offcanvas**: Pulsa el botón de tres líneas `☰` en la esquina superior para acceder a todas las opciones de navegación sin desbordes.
   - **Recorridos 3D táctiles**: Entra a cualquier residencia, presiona `🌐 Recorrido 3D Interactivo` y desliza tu dedo para explorar 360°.
   - **Navegación GPS**: En la vista de detalle, pulsa `📍 Iniciar camino desde mi ubicación` y permite el acceso al GPS para trazar tu ruta a la playa.
   - **Panel Admin**: Inicia sesión con `admin@huatulcotravel.com` / `Admin1234#*` para probar la gestión de alojamientos en diseño adaptativo.

---

## 🗺️ Mapa de Rutas Principales

| Sección | Ruta URL | Roles Permitidos |
| :--- | :--- | :--- |
| **Inicio / Landing** | `/` | Público (Todos) |
| **Catálogo de Alojamientos** | `/Alojamientos` | Público (Todos) |
| **Ficha de Detalle** | `/Alojamientos/Detalle/{id}` | Público (Todos) |
| **Iniciar Sesión** | `/Cuenta/Ingresar` | Público (Todos) |
| **Registro de Nuevos Clientes** | `/Cuenta/Registro` | Público (Todos) |
| **Panel de Administración** | `/Admin/Alojamientos` | Solo `Administrador` |
| **Registrar Nueva Propiedad** | `/Admin/Alojamientos/Crear` | Solo `Administrador` |
| **Editar Propiedad** | `/Admin/Alojamientos/Editar/{id}` | Solo `Administrador` |

---

## 📂 Arquitectura de Directorios

```text
HUATULCO/
├── Areas/
│   └── Admin/
│       ├── Controllers/      # AlojamientosController (CRUD protegido por rol Administrador)
│       └── Views/            # Vistas ejecutivas: Index, Crear y Editar con diseño de lujo
├── Controllers/              # Controladores públicos (Home, Alojamientos, Cuenta)
├── Data/                     # DbContext, migraciones EF Core y SeedData
├── Models/                   # Entidades principales (Alojamiento, ImagenAlojamiento, ApplicationUser)
├── Services/                 # Capa de servicios (AlojamientoService)
├── ViewModels/               # Modelos para formularios validados (Login, Registro, AlojamientoForm)
├── Views/                    # Vistas Razor con Bootstrap 5, Three.js y Leaflet
│   ├── Alojamientos/         # Catálogo interactivo y Detalle con mapa GPS
│   ├── Cuenta/               # Ingreso y Registro de usuarios
│   ├── Home/                 # Landing interactiva con mapa satelital Esri
│   └── Shared/               # _Layout principal con menú lateral Offcanvas
└── wwwroot/
    ├── css/site.css          # Estilos personalizados, tipografías y reglas responsive
    └── uploads/              # Directorio de almacenamiento de imágenes cargadas
```

---

© 2026 HuatulcoTravel. Bahías de Huatulco, Oaxaca, México. Todos los derechos reservados.
