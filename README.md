# Huatulco Travel

Sitio de hospedaje y experiencias en Bahías de Huatulco. Usa PHP con MVC, HTML, CSS, JavaScript y MariaDB de XAMPP. La raíz pública es `public/`.

## Desarrollo en Visual Studio Code

Abre la carpeta de HUATULCO e inicia MySQL en XAMPP. Desde la terminal de PowerShell, copia la configuración de ejemplo si aún no tienes una configuración local.

```powershell
if (!(Test-Path config/database.php)) {
    Copy-Item config/database.example.php config/database.php
}
& C:\xampp\mysql\bin\mysql.exe -u root -e "SOURCE database/schema.sql;"
& C:\xampp\mysql\bin\mysql.exe -u root -e "SOURCE database/demo.sql;"
& C:\xampp\mysql\bin\mysql.exe -u root -e "SOURCE database/catalog.sql;"
& C:\xampp\php\php.exe -d upload_max_filesize=5M -d post_max_size=32M -S 127.0.0.1:8080 -t public scripts/dev-router.php
```

El segundo comando de MySQL carga ocho alojamientos de muestra. Ajusta `config/database.php` y las credenciales de los comandos si tu instalación local usa otros valores. Se requiere PHP 8.2 con `pdo_mysql`, `mbstring` y `fileinfo`.

Abre [Huatulco en el navegador](http://127.0.0.1:8080/). El mapa necesita conexión a Internet para cargar Leaflet y las imágenes satelitales.

## Administrar alojamientos

Registra una cuenta desde `/cuenta/registro`. Para habilitar el panel, el administrador de la base de datos puede asignar el rol a esa cuenta en MariaDB.

```sql
USE huatulco;
UPDATE users SET role = 'admin' WHERE email = 'tu-correo@example.com';
```

En `/admin/alojamientos` puedes crear, editar, publicar y dar de baja alojamientos, además de cargar fotos JPG, PNG o WEBP. Las fotos locales quedan en `public/uploads/`.

Si usas Apache, apunta el `DocumentRoot` a `public/` y habilita `mod_rewrite` y las reglas de `.htaccess`. Configura en PHP los límites de carga indicados arriba.

El esquema de MariaDB y los datos de muestra son independientes de la antigua base PostgreSQL. Las cuentas y registros de esa base requieren una migración de datos aparte.

## Actividades y solicitudes

El administrador crea, edita y publica experiencias desde `/admin/actividades` y
elige en qué grupo del catálogo se muestran.
El cliente inicia sesión, elige una fecha y el tamaño de su grupo desde `/actividades`.
Cada solicitud queda pendiente hasta que el administrador confirme o rechace la
disponibilidad. El resultado y el mensaje opcional se consultan en **Mis solicitudes**.
En una instalación existente, vuelve a aplicar `database/schema.sql` y carga
`database/catalog.sql` para incorporar las actividades agrupadas.

[Créditos de las fotos del catálogo](public/assets/img/activities/CREDITOS.md).
