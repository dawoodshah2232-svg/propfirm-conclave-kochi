<?php
require_once __DIR__ . '/_auth.php';
logout_admin();
header('Location: ' . ADMIN_BASE . '/index.php');
exit;
