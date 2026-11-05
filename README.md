# Huatulco

Plataforma de hospedaje y experiencias turísticas en Huatulco, organizada con arquitectura MVC en PHP.

## Requisitos

- PHP 8.1 o superior
- Apache con `mod_rewrite` (por ejemplo, XAMPP)
- MySQL 8 o MariaDB 10.4+

## Inicio local

1. Coloca el proyecto dentro de la carpeta pública de Apache (por ejemplo, `htdocs/huatulco`).
2. Crea una base MySQL llamada `huatulco` e importa `database/schema.sql`.
3. Copia `config/database.example.php` a `config/database.php` y ajusta tus credenciales.
4. Abre `http://localhost/huatulco/public/`.

El proyecto usa `public/index.php` como punto de entrada. No se requiere Composer para el esqueleto inicial.

## Estructura

```text
app/
  Controllers/     Acciones HTTP y flujo de la aplicación
  Core/            Router, controlador base y conexión a datos
  Models/          Acceso a entidades del dominio
  Views/           Plantillas HTML/PHP
config/            Configuración local
database/          Esquema inicial de MySQL
public/            Único directorio público, punto de entrada y recursos
routes/            Definición de rutas
```

## Módulos previstos

- Alojamientos y disponibilidad
- Reservaciones y comprobantes de pago
- Actividades, proveedores y traslados
- Pedidos de despensa y limpieza
- Usuarios, visitas recurrentes y puntos
- Administración y contenido en español, inglés y francés

## Regla inicial de apartado

Una reserva pendiente bloquea las fechas durante tres días desde su creación. Si no se registra el pago dentro del plazo, pasa a vencida y las fechas vuelven a estar disponibles. La política monetaria de penalización queda pendiente de definición; el sistema guarda el estado y el importe del depósito para que esa regla pueda configurarse después. El administrador actualiza la disponibilidad y las actividades. La estructura contempla un rol de proveedor de actividades para una futura etapa.

Este es un esqueleto de aplicación; autenticación, pantallas administrativas, carga segura de comprobantes y operaciones de reserva se desarrollarán en siguientes etapas.
