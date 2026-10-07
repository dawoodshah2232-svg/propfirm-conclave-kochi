<?php
// Session auth guard for every admin page.
session_start();
require_once __DIR__ . '/_db.php';

function current_admin(): ?array {
    return $_SESSION['admin'] ?? null;
}

function require_login(): void {
    if (!current_admin()) {
        header('Location: ' . ADMIN_BASE . '/index.php');
        exit;
    }
}

function require_role(string $role): void {
    require_login();
    if (current_admin()['role'] !== $role && current_admin()['role'] !== 'super') {
        http_response_code(403);
        exit('Forbidden.');
    }
}

function login_admin(array $user): void {
    session_regenerate_id(true);
    $_SESSION['admin'] = [
        'id'    => $user['id'],
        'name'  => $user['name'],
        'email' => $user['email'],
        'role'  => $user['role'],
    ];
    db()->prepare('UPDATE admin_users SET last_login = UTC_TIMESTAMP() WHERE id = ?')
       ->execute([$user['id']]);
}

function logout_admin(): void {
    unset($_SESSION['admin']);
    session_destroy();
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function csrf_check(): void {
    $t = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $t)) {
        http_response_code(419);
        exit('Session expired — please go back and try again.');
    }
}

function csrf_check_get(): void {
    $t = $_GET['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $t)) {
        http_response_code(419);
        exit('Session expired — please go back and try again.');
    }
}
