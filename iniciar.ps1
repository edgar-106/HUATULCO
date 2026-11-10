param(
    [string]$Php = 'C:\xampp\php\php.exe',
    [ValidateRange(1, 65535)]
    [int]$Puerto = 8080
)

$ErrorActionPreference = 'Stop'
if (-not (Get-Command $Php -ErrorAction SilentlyContinue)) {
    throw 'No se encontró PHP. Instala XAMPP o indica la ruta a php.exe con -Php.'
}

Push-Location $PSScriptRoot
try {
    & $Php scripts/setup.php
    if ($LASTEXITCODE -ne 0) {
        exit $LASTEXITCODE
    }
    & $Php -d upload_max_filesize=5M -d post_max_size=32M -S "127.0.0.1:${Puerto}" -t public scripts/dev-router.php
    exit $LASTEXITCODE
} finally {
    Pop-Location
}
