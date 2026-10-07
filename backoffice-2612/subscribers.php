<?php
// Newsletter subscribers: view, export CSV, delete.
require_once __DIR__ . '/_layout.php';
require_login();

if (isset($_GET['export'])) {
    $rows = q('SELECT name, email, created_at FROM subscribers ORDER BY created_at DESC');
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="subscribers.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Name', 'Email', 'Subscribed']);
    foreach ($rows as $r) fputcsv($out, [$r['name'], $r['email'], $r['created_at']]);
    exit;
}
if (isset($_GET['delete'])) {
    csrf_check_get();
    db()->prepare('DELETE FROM subscribers WHERE id = ?')->execute([(int)$_GET['delete']]);
    flash('Subscriber removed.');
    header('Location: ' . ADMIN_BASE . '/subscribers.php'); exit;
}

$list = q('SELECT * FROM subscribers ORDER BY created_at DESC LIMIT 500');
layout_head('Subscribers');
?>
<div class="toolbar">
  <div class="left"><span style="color:var(--muted);font-size:13.5px"><?= count($list) ?> subscribers</span></div>
  <a class="btn" href="<?= ADMIN_BASE ?>/subscribers.php?export=1">Export CSV</a>
</div>
<div class="panel">
<?php if (!$list): ?><div class="empty">No subscribers yet — they appear here when visitors use the newsletter form.</div>
<?php else: ?>
<table class="tbl"><tr><th>Name</th><th>Email</th><th>Subscribed</th><th></th></tr>
<?php foreach ($list as $s): ?>
<tr>
  <td><?= esc($s['name'] ?: '—') ?></td><td><?= esc($s['email']) ?></td>
  <td><small style="color:var(--muted)"><?= date('d M Y', strtotime($s['created_at'])) ?></small></td>
  <td><a class="btn btn-sm btn-danger" data-confirm="Remove this subscriber?" href="<?= ADMIN_BASE ?>/subscribers.php?delete=<?= $s['id'] ?>&csrf=<?= csrf_token() ?>">Remove</a></td>
</tr>
<?php endforeach; ?></table><?php endif; ?>
</div>
<?php layout_foot(); ?>
