<?php
// Contact enquiries inbox.
require_once __DIR__ . '/_layout.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$act = $_GET['act'] ?? '';
if ($id && $act) {
    csrf_check_get();
    if (in_array($act, ['new','replied','closed'], true)) {
        db()->prepare('UPDATE enquiries SET status=? WHERE id=?')->execute([$act, $id]);
        flash('Enquiry marked as ' . $act . '.');
    } elseif ($act === 'delete') {
        db()->prepare('DELETE FROM enquiries WHERE id=?')->execute([$id]);
        flash('Enquiry deleted.');
    }
    header('Location: ' . ADMIN_BASE . '/enquiries.php'); exit;
}

$view = $id && !$act ? q1('SELECT * FROM enquiries WHERE id=?', [$id]) : null;
if ($view && $view['status'] === 'new') db()->prepare("UPDATE enquiries SET status='replied' WHERE id=?")->execute([$id]);
$st = $_GET['st'] ?? '';
$where = $st ? 'WHERE status = ' . db()->quote($st) : '';
$list = q("SELECT * FROM enquiries $where ORDER BY created_at DESC LIMIT 200");
layout_head('Enquiries');
?>
<div class="toolbar"><div class="left"><div class="tabs" style="margin:0">
  <a class="tab <?= $st===''?'active':'' ?>" href="<?= ADMIN_BASE ?>/enquiries.php">All</a>
  <a class="tab <?= $st==='new'?'active':'' ?>" href="<?= ADMIN_BASE ?>/enquiries.php?st=new">New</a>
  <a class="tab <?= $st==='replied'?'active':'' ?>" href="<?= ADMIN_BASE ?>/enquiries.php?st=replied">Replied</a>
  <a class="tab <?= $st==='closed'?'active':'' ?>" href="<?= ADMIN_BASE ?>/enquiries.php?st=closed">Closed</a>
</div></div></div>

<?php if ($view): ?>
<div class="panel">
  <h2>From <?= esc($view['name']) ?> <small style="color:var(--muted);font-weight:400"><?= esc($view['email']) ?></small></h2>
  <p style="color:var(--muted);font-size:13px;margin-bottom:10px"><?= date('d M Y, H:i', strtotime($view['created_at'])) ?> · <?= esc($view['subject'] ?: '(no subject)') ?></p>
  <p style="white-space:pre-wrap"><?= esc($view['message']) ?></p>
  <div style="margin-top:18px;display:flex;gap:10px;flex-wrap:wrap">
    <a class="btn btn-sm btn-gold" href="mailto:<?= esc($view['email']) ?>?subject=Re: <?= esc($view['subject']) ?>">Reply by email</a>
    <a class="btn btn-sm" href="<?= ADMIN_BASE ?>/enquiries.php?id=<?= $view['id'] ?>&act=closed&csrf=<?= csrf_token() ?>">Mark closed</a>
    <a class="btn btn-sm" href="<?= ADMIN_BASE ?>/enquiries.php">Back to inbox</a>
  </div>
</div>
<?php endif; ?>

<div class="panel"><h2>Inbox (<?= count($list) ?>)</h2>
<?php if (!$list): ?><div class="empty">Inbox is empty.</div><?php else: ?>
<table class="tbl"><tr><th>From</th><th>Message</th><th>Status</th><th>Date</th><th></th></tr>
<?php foreach ($list as $e): ?>
<tr>
  <td><b><?= esc($e['name']) ?></b><br><small style="color:var(--muted)"><?= esc($e['email']) ?></small></td>
  <td><b><?= esc($e['subject'] ?: '(no subject)') ?></b><br><small style="color:var(--muted)"><?= esc(mb_substr($e['message'],0,80)) ?>…</small></td>
  <td><span class="badge <?= $e['status']==='new'?'b-gold':($e['status']==='replied'?'b-green':'b-grey') ?>"><?= esc($e['status']) ?></span></td>
  <td><small style="color:var(--muted)"><?= date('d M, H:i', strtotime($e['created_at'])) ?></small></td>
  <td style="white-space:nowrap">
    <a class="btn btn-sm" href="<?= ADMIN_BASE ?>/enquiries.php?id=<?= $e['id'] ?>">Open</a>
    <a class="btn btn-sm btn-danger" data-confirm="Delete this enquiry?" href="<?= ADMIN_BASE ?>/enquiries.php?id=<?= $e['id'] ?>&act=delete&csrf=<?= csrf_token() ?>">Delete</a>
  </td>
</tr>
<?php endforeach; ?></table><?php endif; ?></div>
<?php layout_foot(); ?>
