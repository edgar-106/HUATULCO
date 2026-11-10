# Huatulco Travel

Sitio de hospedaje y experiencias en Bahías de Huatulco. Usa PHP con MVC, HTML, CSS, JavaScript y MariaDB de XAMPP. La raíz pública es `public/`.

## Desarrollo en Visual Studio Code

Descarga o clona la rama `codex/huatulco-php` y abre esa carpeta en VS Code.

```powershell
git clone --branch codex/huatulco-php https://github.com/edgar-106/HUATULCO.git
cd HUATULCO
```

Inicia MySQL en XAMPP y ejecuta este comando desde la terminal PowerShell de VS Code. Si ya tenías la rama, actualízala con `git pull` antes de iniciar.

```powershell
powershell.exe -NoProfile -ExecutionPolicy Bypass -File .\iniciar.ps1
```

El script crea la configuración local si falta, prepara o actualiza el esquema de MariaDB, incorpora las actividades del catálogo y arranca el servidor PHP. En una instalación nueva también carga ocho alojamientos de muestra. Usa el usuario `root` sin contraseña de la configuración de ejemplo. Si tu XAMPP usa otros datos, edita `config/database.php` y vuelve a ejecutar el script. Se requiere PHP 8.2 con `pdo_mysql`, `mbstring` y `fileinfo`.

Si PHP está instalado en otra carpeta o el puerto 8080 está ocupado, puedes pasar `-Php "D:\xampp\php\php.exe"` o `-Puerto 8081` al script.

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
El script de arranque aplica los cambios al esquema y al catálogo en una instalación existente.

[Créditos de las fotos del catálogo](public/assets/img/activities/CREDITOS.md).
