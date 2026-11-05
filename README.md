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

El área administrativa requiere iniciar sesión con un usuario que tenga el rol `Administrador`. Este paquete crea los roles, pero deliberadamente NO crea una contraseña ni un administrador predeterminado. El siguiente paso es implementar el alta segura del primer administrador y las pantallas de autenticación.

## Importante

- XAMPP/Apache no ejecuta directamente ASP.NET Core MVC. Para desarrollo local se utiliza Kestrel con `dotnet run`.
- No subir `appsettings.json` con credenciales reales a GitHub.
- El CRUD inicial de alojamientos es una base de desarrollo. Antes de producción se debe implementar baja lógica, carga segura de imágenes, auditoría y pruebas.
- La validación de disponibilidad y el flujo de pagos aún no están implementados; son los siguientes módulos.
