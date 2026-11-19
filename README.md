# HuatulcoTravel — Plataforma de Hospedaje y Experiencias

Plataforma de reservas, hospedaje y experiencias turísticas en Bahías de Huatulco, Oaxaca. Reimplementada en **PHP 8.2 + MySQL/MaríaDB**, HTML5, CSS y JavaScript vanilla, con mapa satelital (Leaflet + ArcGIS) y un panel administrativo completo para reservaciones, validación de pagos, eventos y transporte.

---

## Características principales

- **Landing satelital interactiva**: aproximación a las bahías de Huatulco con Leaflet y ArcGIS World Imagery, marcadores de las residencias y contador dinámico.
- **Catálogo de alojamientos**: tarjetas con tipo, capacidad, recámaras, baños y tarifa por noche; galería de fotos y recorrido virtual.
- **Ficha de detalle**: descripción, servicios, mapa, enlace GPS a Google Maps y botón **Reservar ahora**.
- **Reservación en línea**:
  - Formulario público con fechas, huéspedes y notas.
  - Cálculo automático de noches, limpieza, impuestos (19 %) y total estimado.
  - Folio único (`HT-AAAA-XXXXXX`) y apartado con vigencia configurable.
  - Validación de disponibilidad para evitar traslapes.
- **Comprobantes de pago**: el huésped sube JPG, PNG, WEBP o PDF (máximo 5 MB) y consulta el estado de su reservación por folio.
- **Panel administrativo**:
  - **Reservaciones**: filtros por residencia y estado, detalle del huésped, importes, confirmación manual, cancelación y accesos rápidos a eventos y transporte.
  - **Pagos**: bandeja de comprobantes en revisión, visor seguro del archivo, aprobación (libera la residencia y confirma la reservación) o rechazo.
  - **Eventos**: culturales, deportivos y sociales; fijos o esporádicos; vinculados a residencia, huésped y reservación; coordinación con centro de atracción y proveedor externo.
  - **Transporte**: asignación de vehículo, placas, chofer, fechas de recogida/regreso y estado del servicio.
  - **Alojamientos** y **Actividades**: CRUD y revisión de solicitudes existentes.
- **Cuentas y seguridad**: registro e inicio de sesión con `password_hash`, bloqueo por intentos fallidos, roles (`guest`/`admin`), CSRF por token de sesión y comprobantes almacenados fuera de `public/`.

---

## Requisitos

- **PHP 8.2+** con las extensiones `pdo_mysql`, `fileinfo` y `mbstring` (incluidas en XAMPP).
- **MySQL 8** o **MariaDB 10.4+** (XAMPP incluye MariaDB).
- **Git** (opcional).

En este equipo PHP está en `C:\xampp\php\php.exe` y MySQL/MaríaDB escucha en `127.0.0.1:3306` con usuario `root` sin contraseña.

---

## Instalación y ejecución

### 1. Clonar el repositorio

```bash
git clone https://github.com/edgar-106/HUATULCO.git
cd HUATULCO
```

### 2. Configurar la base de datos

Copia la plantilla y ajusta tus credenciales si es necesario:

```bash
copy config\database.example.php config\database.php
```

Contenido esperado:

```php
return [
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => 'huatulco',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
];
```

### 3. Crear el esquema, datos demo y usuarios

```powershell
C:\xampp\php\php.exe scripts\setup.php
```

El script crea la base `huatulco`, importa `database/schema.sql`, `demo.sql` y `catalog.sql`, y siembra las cuentas de acceso. Si ya existe la base, solo aplica cambios pendientes y conserva los datos.

### 4. Iniciar el servidor

```powershell
C:\xampp\php\php.exe -d upload_max_filesize=5M -d post_max_size=32M -S 127.0.0.1:8080 -t public scripts/dev-router.php
```

O simplemente:

```powershell
.\iniciar.ps1
```

Abre **http://127.0.0.1:8080**.

Para probar desde un celular en la misma red Wi-Fi:

```powershell
.\iniciar.ps1 -EnRedLocal
```

y visita `http://<TU_IP_LOCAL>:8080`.

---

## Credenciales de demostración

| Rol | Correo | Contraseña |
| :--- | :--- | :--- |
| Administrador | `admin@huatulcotravel.com` | `Admin1234#*` |
| Cliente | `cliente@huatulcotravel.com` | `Cliente1234#*` |

Al iniciar sesión como administrador se abre directamente `/admin/reservaciones`.

---

## Rutas principales

| Sección | Ruta | Descripción |
| :--- | :--- | :--- |
| Inicio | `/` | Landing con mapa satelital y estancias destacadas. |
| Alojamientos | `/alojamientos` | Catálogo completo. |
| Detalle | `/alojamientos/detalle?id=1` | Ficha del alojamiento. |
| Reservar | `/alojamientos/reservar?id=1` | Formulario público de reservación. |
| Estado de reserva | `/reservas?folio=HT-2026-XXXXXX` | Detalle, instrucciones y carga de comprobante. |
| Mis reservas | `/mis-reservas` | Reservaciones de la cuenta autenticada. |
| Cuenta | `/cuenta/ingresar` · `/cuenta/registro` | Acceso y alta de clientes. |
| Panel: reservaciones | `/admin/reservaciones` | Listado, filtros y detalle. |
| Panel: pagos | `/admin/pagos` | Aprobar o rechazar comprobantes. |
| Panel: eventos | `/admin/eventos` | CRUD y coordinación externa. |
| Panel: transporte | `/admin/transportes` | Asignación logística. |
| Panel: alojamientos | `/admin/alojamientos` | CRUD de propiedades. |
| Panel: actividades | `/admin/actividades` | Catálogo y solicitudes. |

---

## Estructura del proyecto

```text
HUATULCO/
├── app/
│   ├── Controllers/          # Controladores públicos y Admin/
│   ├── Core/                 # Router, Database (PDO), Controller base
│   ├── Models/               # Reservation, Payment, Event, Transport, etc.
│   └── Views/                # Vistas PHP (HTML + CSS)
├── config/
│   ├── app.php               # Nombre, zona horaria, vigencia de apartado
│   └── database.example.php  # Plantilla de conexión MySQL
├── database/
│   ├── schema.sql            # Tablas, columnas y estados
│   ├── demo.sql              # Alojamientos y actividades de muestra
│   └── catalog.sql           # Catálogo complementario
├── public/
│   ├── index.php             # Punto de entrada
│   └── assets/               # CSS e imágenes
├── routes/web.php            # Registro de rutas
├── scripts/
│   ├── setup.php             # Instalación de BD y usuarios
│   └── dev-router.php        # Router para el servidor embebido de PHP
├── storage/receipts/         # Comprobantes privados (generado, no versionado)
└── iniciar.ps1               # Arranque rápido en Windows
```

---

## Notas de operación

- Los comprobantes se guardan en `storage/receipts/AAAA/MM/` y solo se sirven a administradores mediante `/admin/pagos/comprobante?id=…`; nunca se exponen en `public/`.
- Aprobar un pago cambia el comprobante a **Aprobado**, confirma la reservación y libera la residencia. Rechazarlo permite al huésped subir un nuevo archivo.
- El apartado vence según `reservation_hold_days` en `config/app.php` (3 días por defecto); las fechas ocupadas por apartados vencidos quedan disponibles de nuevo.
- Los importes se calculan como: noches × tarifa + limpieza + 19 % de impuestos (16 % IVA + 3 % hospedaje).
- `config/database.php`, `storage/` y `public/uploads/` están excluidos de Git.
