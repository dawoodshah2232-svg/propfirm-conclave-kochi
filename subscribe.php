<?php
// Newsletter subscribe handler → stores in `subscribers`.
require_once __DIR__ . '/includes/site.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $db = site_db();
        if ($db) {
            try {
                $db->prepare('INSERT INTO subscribers (name, email) VALUES (?, ?)
                              ON DUPLICATE KEY UPDATE name = VALUES(name)')
                   ->execute([$name, $email]);
            } catch (Throwable $e) {}
        }
    }
}
$back = $_SERVER['HTTP_REFERER'] ?? 'index.php';
$back = strtok($back, '?');
header('Location: ' . $back . '?subscribed=1');
exit;
