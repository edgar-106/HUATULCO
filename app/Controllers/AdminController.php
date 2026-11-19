<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use PDO;
use RuntimeException;
use Throwable;

final class AdminController extends Controller
{
    private const TYPES = ['casa', 'departamento', 'apartamento', 'habitacion'];

    public function index(): void
    {
        if ($db = $this->adminDatabase()) {
            $this->show($db);
        }
    }

    public function save(): void
    {
        $db = $this->adminDatabase();
        if (!$db || !$this->verifyToken()) {
            return;
        }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id === false || $id === null || $id < 0) {
            http_response_code(400);
            return;
        }
        if ($id && ($_POST['action'] ?? '') === 'deactivate') {
            $db->prepare('UPDATE accommodations SET is_active = 0 WHERE id = ?')->execute([$id]);
            $this->redirect('/admin/alojamientos');
            return;
        }

        $name = $this->field('name');
        $type = $this->field('accommodation_type');
        $area = $this->field('neighborhood');
        $rate = $this->field('nightly_rate');
        $capacity = filter_input(INPUT_POST, 'capacity', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]);
        $bedrooms = filter_input(INPUT_POST, 'bedrooms', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 65535]]);
        $bathrooms = filter_input(INPUT_POST, 'bathrooms', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 65535]]);
        $lat = $this->field('latitude');
        $lng = $this->field('longitude');
        $tour = $this->field('virtual_tour_url');
        if ($name === '' || mb_strlen($name) > 160 || mb_strlen($area) > 120 ||
            strlen($this->field('description')) > 65535 ||
            !in_array($type, self::TYPES, true) || !preg_match('/^\d{1,8}(?:\.\d{1,2})?$/D', $rate) ||
            !$capacity || !is_int($bedrooms) || !is_int($bathrooms) ||
            !$this->validCoordinate($lat, 90) || !$this->validCoordinate($lng, 180) ||
            ($lat === '') !== ($lng === '') || strlen($tour) > 500 ||
            ($tour !== '' && (!filter_var($tour, FILTER_VALIDATE_URL) || !in_array(parse_url($tour, PHP_URL_SCHEME), ['http', 'https'], true)))) {
            $this->show($db, 'Revisa los datos del alojamiento.');
            return;
        }

        $files = [];
        try {
            $uploads = $this->photos();
            $db->beginTransaction();
            $values = [$name, $type, $this->field('description'), $area, $lat === '' ? null : $lat,
                $lng === '' ? null : $lng, $capacity, $rate, $bedrooms, $bathrooms, isset($_POST['is_published']) ? 1 : 0,
                isset($_POST['virtual_tour_url']) ? $tour : null];
            $columns = 'name = ?, accommodation_type = ?, description = ?, neighborhood = ?, latitude = ?, longitude = ?, capacity = ?, nightly_rate = ?, bedrooms = ?, bathrooms = ?, is_published = ?';
            if ($id) {
                $check = $db->prepare('SELECT id FROM accommodations WHERE id = ? AND is_active = 1 FOR UPDATE');
                $check->execute([$id]);
                if (!$check->fetchColumn()) {
                    $db->rollBack();
                    http_response_code(404);
                    return;
                }
                $db->prepare("UPDATE accommodations SET $columns, virtual_tour_url = COALESCE(?, virtual_tour_url) WHERE id = ?")->execute([...$values, $id]);
            } else {
                $db->prepare("INSERT INTO accommodations SET $columns, virtual_tour_url = ?")->execute($values);
                $id = (int) $db->lastInsertId();
            }
            $directory = dirname(__DIR__, 2) . '/public/uploads';
            foreach ($uploads as [$source, $extension]) {
                if (!is_dir($directory) && !mkdir($directory) && !is_dir($directory)) {
                    throw new RuntimeException('No se pudo preparar el directorio de fotos.');
                }
                $name = bin2hex(random_bytes(16)) . '.' . $extension;
                $target = $directory . '/' . $name;
                if (!move_uploaded_file($source, $target)) {
                    throw new RuntimeException('No se pudo guardar una foto.');
                }
                $files[] = $target;
                $path = '/uploads/' . $name;
                $db->prepare('INSERT INTO accommodation_images (accommodation_id, path) VALUES (?, ?)')->execute([$id, $path]);
                $db->prepare("UPDATE accommodations SET cover_image = ? WHERE id = ? AND (cover_image IS NULL OR cover_image = '')")->execute([$path, $id]);
            }
            $db->commit();
        } catch (Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            foreach ($files as $file) {
                unlink($file);
            }
            if (get_class($exception) !== RuntimeException::class) {
                throw $exception;
            }
            $this->show($db, $exception->getMessage());
            return;
        }
        $this->redirect('/admin/alojamientos');
    }

    private function show(PDO $db, ?string $error = null): void
    {
        $includeInactive = ($_GET['inactivos'] ?? '') === '1';
        $mode = ($_GET['nuevo'] ?? '') === '1' ? 'create' : 'list';
        $stays = $db->query('SELECT * FROM accommodations' . ($includeInactive ? '' : ' WHERE is_active = 1') . ' ORDER BY name')->fetchAll();
        $editId = $error === null ? filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT) : filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $edit = $editId ? (array_column($stays, null, 'id')[$editId] ?? []) : [];
        $photos = [];
        if (!empty($edit['is_active'])) {
            $mode = 'edit';
            $query = $db->prepare('SELECT path FROM accommodation_images WHERE accommodation_id = ? ORDER BY id');
            $query->execute([$editId]);
            $photos = array_unique(array_filter([$edit['cover_image'], ...$query->fetchAll(PDO::FETCH_COLUMN)]));
        }
        if ($error !== null) {
            $mode = $edit ? 'edit' : 'create';
            foreach (['name', 'accommodation_type', 'description', 'neighborhood', 'latitude', 'longitude',
                'capacity', 'bedrooms', 'bathrooms', 'nightly_rate', 'virtual_tour_url'] as $field) {
                $edit[$field] = $this->field($field);
            }
            $edit['is_published'] = isset($_POST['is_published']);
        }
        $this->view('admin/accommodations', [
            'stays' => $stays, 'edit' => $edit, 'photos' => $photos, 'types' => self::TYPES,
            'error' => $error, 'mode' => $mode, 'includeInactive' => $includeInactive,
        ]);
    }

    private function field(string $name): string
    {
        $value = $_POST[$name] ?? '';
        return is_string($value) ? trim($value) : '';
    }

    private function validCoordinate(string $value, int $maximum): bool
    {
        return $value === '' || (preg_match('/^-?\d{1,3}(?:\.\d{1,7})?$/D', $value) && abs((float) $value) <= $maximum);
    }

    private function photos(): array
    {
        $uploads = [];
        $mimeTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        foreach ($_FILES['photos']['error'] ?? [] as $index => $error) {
            if ($error === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $file = $_FILES['photos']['tmp_name'][$index];
            if ($error !== UPLOAD_ERR_OK || !is_uploaded_file($file) || filesize($file) > 5 * 1024 * 1024) {
                throw new RuntimeException('Cada foto debe pesar como máximo 5 MB.');
            }
            $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file);
            if (!isset($mimeTypes[$mime])) {
                throw new RuntimeException('Sube imágenes JPG, PNG o WEBP.');
            }
            $uploads[] = [$file, $mimeTypes[$mime]];
        }
        return $uploads;
    }
}
