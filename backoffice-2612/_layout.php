<?php
// Admin chrome: sidebar + topbar. Usage:
//   require '_auth.php'; require_login();
//   layout_head('Speakers'); ... page html ... layout_foot();
require_once __DIR__ . '/_auth.php';

function nav_groups(): array {
    return [
        'Overview' => [
            ['dashboard.php', 'Dashboard', 'M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10'],
        ],
        'Content' => [
            ['pages.php',   'Pages & Banners', 'M4 5h16v14H4zM4 9h16'],
            ['gallery.php', 'Gallery',         'M4 5h16v14H4zM8 11l2.5 2.5L16 8M4 5l4 4'],
            ['media.php',   'Media Library',   'M4 8h16v12H4zM8 4h8M12 12v6M9 15h6'],
        ],
        'People' => [
            ['speakers.php',     'Speakers',       'M16 7a4 4 0 11-8 0 4 4 0 018 0zM4 21v-1a7 7 0 0114 0v1'],
            ['sponsors.php',     'Sponsors',       'M12 3l2.5 5.5L20 9.8l-4 4 1 6-5-2.8L7 19.6l1-6-4-4 5.5-1.3z'],
            ['sponsor_tiers.php','Sponsor Tiers',  'M4 6h16M4 12h16M4 18h16'],
        ],
        'Event Ops' => [
            ['agenda.php',     'Agenda',     'M8 3v4M16 3v4M4 7h16v14H4zM4 12h16'],
            ['tickets.php',    'Ticket Types','M4 8h16v3a2 2 0 000 4v3H4v-3a2 2 0 000-4zM13 8v10'],
            ['orders.php',     'Orders',      'M6 6h15l-1.5 9h-12zM6 6L5 3H2M9 20a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM18 20a1.5 1.5 0 100-3 1.5 1.5 0 000 3z'],
            ['exhibitors.php', 'Exhibitors',  'M3 21h18M5 21V8l7-5 7 5v13M9 21v-6h6v6'],
            ['booths.php',     'Booths',      'M4 4h16v16H4zM4 9h16M9 4v16'],
            ['pnl.php',        'P&L',         'M4 20V10M10 20V4M16 20v-8M22 20H2'],
        ],
        'Engage' => [
            ['blog.php',        'Blog',        'M4 5h16v12H4zM8 21h8M12 17v4M7 9h10M7 12h6'],
            ['faq.php',         'FAQs',        'M9 9a3 3 0 115.8 1c-.7 1-1.8 1.6-1.8 3M12 17h.01M12 3a9 9 0 100 18 9 9 0 000-18z'],
            ['subscribers.php', 'Subscribers', 'M4 6h16v12H4zM4 7l8 6 8-6'],
            ['enquiries.php',   'Enquiries',   'M21 12a8 8 0 01-8 8H4l2-3a8 8 0 1115-5z'],
        ],
        'System' => [
            ['users.php',    'Admin Users', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM4 21v-1a7 7 0 0114 0v1M19 8v6M22 11h-6'],
            ['settings.php', 'Settings',    'M12 15a3 3 0 100-6 3 3 0 000 6zM19 12a7 7 0 01-.1 1.2l2 1.6-2 3.4-2.4-1a7 7 0 01-2 1.2L14 20h-4l-.5-2.6a7 7 0 01-2-1.2l-2.4 1-2-3.4 2-1.6A7 7 0 015 12a7 7 0 01.1-1.2l-2-1.6 2-3.4 2.4 1a7 7 0 012-1.2L10 4h4l.5 2.6a7 7 0 012 1.2l2.4-1 2 3.4-2 1.6c.06.4.1.8.1 1.2z'],
        ],
    ];
}

function layout_head(string $title): void {
    $me = current_admin();
    $page = basename($_SERVER['SCRIPT_NAME']);
    $flash = flash();
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= esc($title) ?> · Conclave Admin</title>
<link rel="stylesheet" href="<?= ADMIN_BASE ?>/assets/admin.css">
</head>
<body>
<div class="shell">
  <aside class="side">
    <a class="brand" href="<?= ADMIN_BASE ?>/dashboard.php">
      <span class="brand-mark">PC</span>
      <span class="brand-txt"><b>PropFirm Conclave</b><small>Admin Portal</small></span>
    </a>
    <nav class="nav">
    <?php foreach (nav_groups() as $group => $items): ?>
      <div class="nav-group"><?= esc($group) ?></div>
      <?php foreach ($items as [$href, $label, $d]):
        if ($href === 'users.php' && ($me['role'] ?? '') !== 'super') continue;
        $active = $page === $href ? ' active' : ''; ?>
        <a class="nav-link<?= $active ?>" href="<?= ADMIN_BASE ?>/<?= $href ?>">
          <svg viewBox="0 0 24 24"><path d="<?= esc($d) ?>"/></svg><?= esc($label) ?>
        </a>
      <?php endforeach; ?>
    <?php endforeach; ?>
    </nav>
    <div class="side-foot">
      <a class="nav-link" href="/" target="_blank">
        <svg viewBox="0 0 24 24"><path d="M14 4h6v6M20 4L10 14M18 13v7H4V6h7"/></svg>View Website
      </a>
      <a class="nav-link" href="<?= ADMIN_BASE ?>/logout.php">
        <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>Logout
      </a>
    </div>
  </aside>
  <div class="main">
    <header class="topbar">
      <h1><?= esc($title) ?></h1>
      <div class="me"><span class="dot"></span><?= esc($me['name'] ?? '') ?> <small><?= esc($me['role'] ?? '') ?></small></div>
    </header>
    <main class="page">
    <?php if ($flash): ?>
      <div class="alert <?= $flash['type'] === 'ok' ? 'alert-ok' : 'alert-err' ?>"><?= esc($flash['msg']) ?></div>
    <?php endif; ?>
    <?php
}

function layout_foot(): void {
    ?>
    </main>
  </div>
</div>
<script src="<?= ADMIN_BASE ?>/assets/admin.js"></script>
</body>
</html>
<?php
}
