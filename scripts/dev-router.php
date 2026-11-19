<?php
declare(strict_types=1);

$root = realpath(__DIR__ . '/../public');
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
if (preg_match('~(?:^|[/\\\\])\.~', $path)) {
    http_response_code(404);
    return true;
}
$file = realpath($root . $path);
if ($file && str_starts_with($file, $root . DIRECTORY_SEPARATOR) && is_file($file)) {
    return false;
}
require $root . '/index.php';
