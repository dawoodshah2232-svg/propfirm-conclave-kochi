<?php
// PDO singleton — MySQL with UTF8MB4.
require_once __DIR__ . '/_config.php';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

function q(string $sql, array $params = []): array {
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
}

function q1(string $sql, array $params = []): ?array {
    $st = db()->prepare($sql);
    $st->execute($params);
    $row = $st->fetch();
    return $row === false ? null : $row;
}

function qv(string $sql, array $params = []) {
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st->fetchColumn();
}

function setting(string $key, string $default = ''): string {
    static $cache = [];
    if (!array_key_exists($key, $cache)) {
        $v = qv('SELECT svalue FROM settings WHERE skey = ?', [$key]);
        $cache[$key] = $v === false ? $default : (string)$v;
    }
    return $cache[$key];
}

function save_setting(string $key, string $value): void {
    db()->prepare('INSERT INTO settings (skey, svalue) VALUES (?, ?)
                   ON DUPLICATE KEY UPDATE svalue = VALUES(svalue)')
      ->execute([$key, $value]);
}

function content_block(string $page, string $section, string $field, string $default = ''): string {
    $v = qv('SELECT fvalue FROM content_blocks WHERE page_slug=? AND section=? AND field=?',
            [$page, $section, $field]);
    return $v === false ? $default : (string)$v;
}

function esc(?string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function money($n): string {
    return '₹' . number_format((float)$n, 2);
}

function flash(?string $msg = null, string $type = 'ok'): ?array {
    if ($msg !== null) { $_SESSION['flash'] = ['msg' => $msg, 'type' => $type]; return null; }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

// Secure image upload → returns stored filename or throws.
function upload_image(array $file, string $prefix = 'img'): string {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return '';
    if ($file['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Upload failed.');
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $exts = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($exts[$mime])) throw new RuntimeException('Only JPG, PNG or WebP images allowed.');
    if ($file['size'] > 5 * 1024 * 1024) throw new RuntimeException('Image must be under 5 MB.');
    if (!is_dir(UPLOAD_DIR) && !mkdir(UPLOAD_DIR, 0755, true)) throw new RuntimeException('Upload dir missing.');
    $name = $prefix . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $exts[$mime];
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . '/' . $name)) {
        throw new RuntimeException('Could not save upload.');
    }
    return $name;
}

function upload_url(string $name): string {
    if ($name === '') return '';
    if (str_starts_with($name, 'assets/') || str_starts_with($name, 'http')) return $name;
    return UPLOAD_URL . '/' . $name;
}
