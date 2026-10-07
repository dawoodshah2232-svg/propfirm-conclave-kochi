<?php
// ============================================================================
// PropFirm Conclave — Admin portal configuration
// Copy these credentials from your hosting control panel.
// ============================================================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'propfirm_conclave');
define('DB_USER', 'root');
define('DB_PASS', '');

// URL path where /admin lives, e.g. '/admin' or '/propfirm/admin'
define('ADMIN_BASE', '/backoffice-2612'); // secret path — NOT linked anywhere public

// Public uploads (relative to the site root)
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads');
define('UPLOAD_URL', 'assets/uploads');
