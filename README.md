# HuatulcoTravel — Plataforma de Hospedaje y Experiencias

Plataforma moderna de reservas, hospedaje y experiencias turísticas en Bahías de Huatulco, Oaxaca. Desarrollada en **ASP.NET Core MVC (.NET 10)** con **PostgreSQL**, **Entity Framework Core**, **Three.js (WebGL 360°)** y **Leaflet con ArcGIS Satellite Imagery**.

---

## Características Principales

- **Landing Satelital Interactiva**:
  - Vista aérea con animación de aproximación desde el espacio hasta las bahías de Huatulco vía Leaflet y ArcGIS Esri World Imagery.
  - Marcadores de ubicación con anillos dorados pulsantes y popups con detalles, fotos y precios en vivo.
  - Contador dinámico de residencias ubicadas y controles de mapa.
- **Catálogo de Alojamientos Optimizado**:
  - Tarjetas limpias con información esencial: tipo, capacidad, número de habitaciones, baños y tarifa (`MXN por noche`).
  - Botón flotante **`📷 Fotos`**: Visor modal interactivo con carrusel, contador de fotografías y tira de miniaturas.
  - Botón flotante **`🌐 Recorrido 3D`**: Visor panorámico 360° esférico con **Three.js** con rotación por arrastre/touch, zoom y cambio de ambientes (Sala, Recámara Master, Alberca con vista al mar).
  - 8 alojamientos de muestra con coordenadas satelitales reales en Tangolunda, Chahué, Conejos, Santa Cruz, Bocana, Maguey y Arrocito.
- **Ficha de Detalle de Alojamiento**:
  - Imagen principal expandida a pantalla completa (hero banner) con acceso directo a fotos y recorrido 3D.
  - Resumen de características, descripción completa y lista de servicios incluidos.
  - Tarjeta lateral de reservación con precio por noche, disponibilidad y botón de solicitud vía WhatsApp.
  - **Mapa Amplio con Navegación GPS**:
    - Botón **`📍 Iniciar camino desde mi ubicación`**: Detecta tus coordenadas GPS reales, ubica tu marcador pulsante azul, traza la ruta directa sobre el mapa, calcula la distancia en km y tiempo estimado en auto, y ofrece el enlace a Google Maps en tiempo real.
- **Diseño 100% Responsivo y Móvil (Mobile-First)**:
  - Cabecera limpia y compacta en celulares con menú lateral desplegable tipo **Offcanvas** (estilo Airbnb).
  - Gestos táctiles optimizados (`touch-action: none`) para rotar el 3D en smartphones sin interferir con el scroll.
  - Compatible para navegarse desde cualquier celular en la misma red local Wi-Fi.
- **Sistema de Autenticación y Cuentas (ASP.NET Core Identity)**:
  - Registro seguro de clientes (`/Cuenta/Registro`).
  - Inicio de sesión (`/Cuenta/Ingresar`) con protección contra ataques de fuerza bruta y bloqueo temporal.
  - Roles definidos: `Administrador`, `Cliente`, `Operativo`, `Proveedor`.
- **Panel de Administración (`/Admin/Alojamientos`)**:
  - CRUD completo para gestionar residencias.
  - Subida múltiple de fotografías (`JPG`, `PNG`, `WEBP`) con validación de magic bytes (máximo 5 MB por archivo).
  - Baja lógica (*soft delete*) para proteger el historial de reservas y pagos.

---

## Requisitos Previos

- [.NET 10 SDK](https://dotnet.microsoft.com/download)
- [PostgreSQL 15+](https://www.postgresql.org/download/) en ejecución local (puerto por defecto: `5432`).
- [Git](https://git-scm.com/)

---

## Instrucciones para Ejecutar el Proyecto

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

Revisa tu cadena de conexión en `appsettings.json`. Si tu contraseña de PostgreSQL es diferente a `postgres`, cámbiala:
```json
"ConnectionStrings": {
  "DefaultConnection": "Host=localhost;Port=5432;Database=huatulco_travel_db;Username=postgres;Password=TU_PASSWORD"
}
```

### 3. Aplicar las Migraciones y Sembrado de Datos
Si no tienes la herramienta global de Entity Framework, instálala una sola vez:
```bash
dotnet tool install --global dotnet-ef
```

Aplica las migraciones para generar las tablas en PostgreSQL:
```bash
dotnet ef database update
```

*(Al arrancar por primera vez, `SeedData.cs` insertará automáticamente los 8 alojamientos con sus fotos y el usuario administrador).*

### 4. Iniciar la Aplicación en tu Computadora
```bash
dotnet run
```

La aplicación se iniciará y escuchará en todas las interfaces de red:
- Desde tu PC: 👉 **http://localhost:5000**
- Desde tu celular en la misma red Wi-Fi: 👉 **http://<TU_IP_LOCAL>:5000** (por ejemplo: `http://192.168.0.106:5000`)

---

## Cómo Ver y Probar desde el Navegador de tu Celular

1. Conecta tu celular a la **misma red Wi-Fi** que tu computadora.
2. Averigua la dirección IP de tu computadora abriendo una terminal y ejecutando `ipconfig` (busca la línea *Dirección IPv4* en tu adaptador Wi-Fi, por ejemplo: `192.168.0.106`).
3. En el navegador de tu celular (Chrome, Safari, etc.) entra a:
   ```
   http://192.168.0.106:5000
   ```
4. **Funcionalidades móviles destacadas**:
   - Pulsa el botón de menú hamburguesa **`☰`** para abrir el panel lateral Offcanvas.
   - Entra a cualquier alojamiento y prueba el **Recorrido 3D** deslizando con tu dedo sobre la pantalla táctil.
   - Presiona **`📍 Iniciar camino desde mi ubicación`** en el mapa: tu teléfono usará su sensor GPS real para calcular la ruta exacta hacia la residencia.

---

## Rutas y Acceso

| Sección | Ruta | Descripción |
| :--- | :--- | :--- |
| **Inicio** | `/` | Landing con mapa satelital interactivo y estancias destacadas. |
| **Catálogo** | `/Alojamientos` | Listado completo con filtros, fotos y recorridos 3D. |
| **Detalle** | `/Alojamientos/Detalle/{id}` | Ficha completa, mapa grande y botón de ruta GPS. |
| **Ingreso** | `/Cuenta/Ingresar` | Formulario de inicio de sesión. |
| **Registro** | `/Cuenta/Registro` | Alta de nuevos clientes. |
| **Panel Admin** | `/Admin/Alojamientos` | Gestión administrativa de propiedades (requiere rol `Administrador`). |

### Credenciales del Administrador por Defecto:
- **Correo**: `admin@huatulcotravel.com`
- **Contraseña**: `Admin1234#*`

---

## Arquitectura del Proyecto

```text
HUATULCO/
├── Areas/
│   └── Admin/            # Panel administrativo (Controladores y vistas CRUD)
├── Controllers/          # Controladores públicos (HomeController, AlojamientosController, CuentaController)
├── Data/                 # ApplicationDbContext, migraciones EF Core y SeedData
├── Models/               # Entidades del dominio (Alojamiento, ImagenAlojamiento, etc.)
├── Services/             # Lógica de negocio (AlojamientoService)
├── ViewModels/           # Modelos de vista para formularios (Login, Registro, etc.)
├── Views/                # Vistas Razor con diseño responsivo y Three.js 3D
│   ├── Alojamientos/     # Catálogo Index y Detalle con mapa interactivo y GPS
│   ├── Home/             # Landing satelital interactiva Leaflet / Esri
│   └── Shared/           # _Layout con menú Offcanvas para móviles
└── wwwroot/              # CSS (site.css), JavaScript y uploads de imágenes
```
