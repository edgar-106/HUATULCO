# HuatulcoTravel — Plataforma de Hospedaje y Experiencias

Plataforma moderna de reservas, hospedaje y experiencias turísticas en Bahías de Huatulco, Oaxaca. Desarrollada en **ASP.NET Core MVC (.NET 10)** con **PostgreSQL** y **Entity Framework Core**.

---

## Características Principales

- **Landing Satelital Interactiva**:
  - Vista aérea con animación de aproximación desde el espacio hasta las bahías de Huatulco vía Leaflet y ArcGIS Esri World Imagery.
  - Marcadores de ubicación con anillos dorados pulsantes y popups con detalles, fotos y precios en vivo.
  - Contador dinámico de residencias ubicadas y controles de mapa.
- **Catálogo de Alojamientos**:
  - Exploración de casas, departamentos, habitaciones y villas con capacidad, recámaras y tarifas por noche.
  - 8 alojamientos de muestra con coordenadas reales en Tangolunda, Chahué, Conejos, Santa Cruz, Bocana, Maguey y Arrocito.
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

### 4. Iniciar la Aplicación
```bash
dotnet run
```

La aplicación se iniciará y escuchará en:
👉 **http://localhost:5000**

---

## Rutas y Acceso

| Sección | Ruta | Descripción |
| :--- | :--- | :--- |
| **Inicio** | `/` | Landing con mapa satelital interactivo y estancias destacadas. |
| **Catálogo** | `/Alojamientos` | Listado completo de alojamientos disponibles. |
| **Ingreso** | `/Cuenta/Ingresar` | Formulario de inicio de sesión. |
| **Registro** | `/Cuenta/Registro` | Alta de nuevos clientes. |
| **Panel Admin** | `/Admin/Alojamientos` | Gestión administrativa de propiedades (requiere rol `Administrador`). |

### Credenciales del Administrador por Defecto:
- **Correo**: `admin@huatulcotravel.com`
- **Contraseña**: `Admin1234#*`

*(Para configurar un administrador personalizado antes del primer arranque, puedes usar `dotnet user-secrets set "BootstrapAdmin:Email" "tu@correo.com"` y `dotnet user-secrets set "BootstrapAdmin:Password" "TuClaveSegura!123"`).*

---

## Arquitectura del Proyecto

```text
HUATULCO/
├── Areas/
│   └── Admin/            # Panel administrativo (Controladores y vistas de gestión)
├── Controllers/          # Controladores públicos (Home, Alojamientos, Cuenta)
├── Data/                 # ApplicationDbContext, migraciones EF y SeedData
├── Models/               # Entidades del dominio (Alojamiento, Reservacion, Pago, etc.)
├── Services/             # Lógica de negocio (AlojamientoService)
├── ViewModels/           # Modelos de vista para formularios (Login, Registro, etc.)
├── Views/                # Plantillas Razor con diseño HuatulcoTravel
└── wwwroot/              # Recursos estáticos (CSS, JS, uploads de imágenes)
```
