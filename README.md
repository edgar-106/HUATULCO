# HuatulcoTravel — base MVC

Proyecto inicial de plataforma de alojamientos y experiencias turísticas.

## Requisitos

- .NET 10 SDK
- PostgreSQL 15 o superior
- Visual Studio Code
- Extensión C# Dev Kit (recomendada)

## 1. Crear la base de datos

Desde pgAdmin o psql, ejecutar:

```sql
CREATE DATABASE huatulco_travel_db;
```

## 2. Configurar la conexión

Editar `appsettings.json` y cambiar `CAMBIAR_ESTA_PASSWORD` por la contraseña local de PostgreSQL.

Para un entorno real, NO guardar contraseñas en `appsettings.json` ni subirlas a GitHub. Utilizar Secret Manager o variables de entorno.

## 3. Instalar herramienta EF

```bash
dotnet tool install --global dotnet-ef
```

Si ya está instalada:

```bash
dotnet tool update --global dotnet-ef
```

## 4. Restaurar paquetes y crear tablas

Desde la carpeta donde está `HuatulcoTravel.csproj`:

```bash
dotnet restore
dotnet ef migrations add InitialCreate
dotnet ef database update
```

## 5. Ejecutar

```bash
dotnet run
```

Abrir la dirección HTTPS que indique la terminal.

## 6. Rutas iniciales

- Sitio público: `/`
- Catálogo de alojamientos: `/Alojamientos`
- Administración de alojamientos: `/Admin/Alojamientos`

El área administrativa requiere iniciar sesión con un usuario que tenga el rol `Administrador`. La Etapa 2 incluye autenticación y creación segura del primer administrador mediante User Secrets; consulta las instrucciones de la sección Etapa 2.

## Importante

- XAMPP/Apache no ejecuta directamente ASP.NET Core MVC. Para desarrollo local se utiliza Kestrel con `dotnet run`.
- No subir `appsettings.json` con credenciales reales a GitHub.
- El CRUD de alojamientos es una base de desarrollo. Antes de producción se requiere auditoría, escaneo de archivos y pruebas.
- La validación de disponibilidad y el flujo de pagos aún no están implementados; son los siguientes módulos.


## Etapa 2: autenticación y administración

Incluye registro de clientes, inicio/cierre de sesión, bootstrap del primer administrador por User Secrets, baja lógica y carga múltiple de imágenes JPG/PNG/WEBP (máximo 5 MB por archivo).

### Crear el primer administrador (solo una vez)
Antes de iniciar la aplicación, configura: 
```bash
dotnet user-secrets set "BootstrapAdmin:Email" "admin@tudominio.com"
dotnet user-secrets set "BootstrapAdmin:Password" "UnaClaveLarga!2026"
dotnet user-secrets set "BootstrapAdmin:Name" "Administrador principal"
```
Inicia la aplicación una vez; el administrador se crea solo si todavía no existe uno. Después elimina esos secretos con `dotnet user-secrets remove "BootstrapAdmin:Email"` y lo mismo para Password y Name. No publiques credenciales en GitHub.

### Migración si ya aplicaste InitialCreate
```bash
dotnet ef migrations add AddAlojamientoActivo
dotnet ef database update
```
Si todavía no aplicaste migraciones, genera la migración inicial con el modelo actualizado.

La carga guarda imágenes bajo `wwwroot/uploads/alojamientos`. Para producción se requiere revisión de seguridad, escaneo de archivos, almacenamiento dedicado, recuperación de contraseña, confirmación de correo, rate limiting y pruebas automatizadas.
