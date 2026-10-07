<?php
// Exhibitors: companies, booth assignment, packages, payments.
require_once __DIR__ . '/_layout.php';
require_login();

if (isset($_GET['delete'])) {
    csrf_check_get();
    db()->prepare('DELETE FROM exhibitors WHERE id = ?')->execute([(int)$_GET['delete']]);
    flash('Exhibitor deleted.');
    header('Location: ' . ADMIN_BASE . '/exhibitors.php'); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['company'])) {
    csrf_check();
    $booth = (int)($_POST['booth_id'] ?? 0) ?: null;
    $data = [$_POST['company'], $_POST['contact_name'], $_POST['email'], $_POST['phone'], $booth,
             $_POST['package'], (float)$_POST['amount'], $_POST['status']];
    if (!empty($_POST['id'])) {
        $data[] = (int)$_POST['id'];
        db()->prepare('UPDATE exhibitors SET company=?, contact_name=?, email=?, phone=?, booth_id=?, package=?, amount=?, status=? WHERE id=?')->execute($data);
        flash('Exhibitor updated.');
    } else {
        db()->prepare('INSERT INTO exhibitors (company, contact_name, email, phone, booth_id, package, amount, status) VALUES (?,?,?,?,?,?,?,?)')->execute($data);
        flash('Exhibitor added.');
    }
    if ($booth && $_POST['status'] === 'confirmed') {
        db()->prepare("UPDATE booths SET status='sold' WHERE id=? AND status='available'")->execute([$booth]);
    }
    header('Location: ' . ADMIN_BASE . '/exhibitors.php'); exit;
}

$booths = q('SELECT * FROM booths ORDER BY code');
$edit = isset($_GET['edit']) ? q1('SELECT * FROM exhibitors WHERE id=?', [(int)$_GET['edit']]) : null;
$list = q('SELECT e.*, b.code AS booth_code FROM exhibitors e LEFT JOIN booths b ON b.id=e.booth_id ORDER BY e.created_at DESC');
layout_head('Exhibitors');
?>
<div class="toolbar"><div class="left"></div>
  <a class="btn btn-gold" href="<?= ADMIN_BASE ?>/exhibitors.php?edit=new">+ Add Exhibitor</a></div>

<?php if ($edit || isset($_GET['edit'])): $e = $edit ?: ['id'=>'','company'=>'','contact_name'=>'','email'=>'','phone'=>'','booth_id'=>'','package'=>'','amount'=>'','status'=>'lead']; ?>
<div class="panel"><h2><?= $edit ? 'Edit' : 'Add' ?> Exhibitor</h2>
  <form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <input type="hidden" name="id" value="<?= esc($e['id']) ?>">
    <div class="form-grid">
      <div class="field"><label>Company</label><input name="company" required value="<?= esc($e['company']) ?>"></div>
      <div class="field"><label>Contact person</label><input name="contact_name" value="<?= esc($e['contact_name']) ?>"></div>
      <div class="field"><label>Email</label><input name="email" type="email" value="<?= esc($e['email']) ?>"></div>
      <div class="field"><label>Phone</label><input name="phone" value="<?= esc($e['phone']) ?>"></div>
      <div class="field"><label>Booth</label><select name="booth_id">
        <option value="0">— No booth —</option>
        <?php foreach ($booths as $b): ?>
        <option value="<?= $b['id'] ?>" <?= (string)$e['booth_id']===(string)$b['id']?'selected':'' ?>><?= esc($b['code']) ?> (<?= esc($b['size']) ?>) — <?= esc($b['status']) ?></option>
        <?php endforeach; ?></select></div>
      <div class="field"><label>Package</label><input name="package" placeholder="e.g. Gold Exhibitor" value="<?= esc($e['package']) ?>"></div>
      <div class="field"><label>Amount (₹)</label><input name="amount" type="number" step="0.01" min="0" value="<?= esc($e['amount']) ?>"></div>
      <div class="field"><label>Status</label><select name="status">
        <?php foreach (['lead','confirmed','cancelled'] as $s): ?>
        <option <?= $e['status']===$s?'selected':'' ?>><?= $s ?></option><?php endforeach; ?></select></div>
    </div>
    <div style="margin-top:20px;display:flex;gap:10px">
      <button class="btn btn-gold" type="submit">Save</button>
      <a class="btn" href="<?= ADMIN_BASE ?>/exhibitors.php">Cancel</a>
    </div></form></div>
<?php endif; ?>

<div class="panel"><h2>Exhibitors (<?= count($list) ?>)</h2>
<?php if (!$list): ?><div class="empty">No exhibitors yet.</div><?php else: ?>
<table class="tbl"><tr><th>Company</th><th>Contact</th><th>Booth</th><th>Package</th><th>Amount</th><th>Status</th><th></th></tr>
<?php foreach ($list as $x): ?>
<tr>
  <td><b><?= esc($x['company']) ?></b></td>
  <td><?= esc($x['contact_name']) ?><br><small style="color:var(--muted)"><?= esc($x['email']) ?></small></td>
  <td><?= $x['booth_code'] ? '<b>'.esc($x['booth_code']).'</b>' : '<span style="color:var(--muted)">—</span>' ?></td>
  <td><?= esc($x['package'] ?: '—') ?></td><td><?= money($x['amount']) ?></td>
  <td><span class="badge <?= $x['status']==='confirmed'?'b-green':($x['status']==='cancelled'?'b-red':'b-gold') ?>"><?= esc($x['status']) ?></span></td>
  <td style="white-space:nowrap">
    <a class="btn btn-sm" href="<?= ADMIN_BASE ?>/exhibitors.php?edit=<?= $x['id'] ?>">Edit</a>
    <a class="btn btn-sm btn-danger" data-confirm="Delete this exhibitor?" href="<?= ADMIN_BASE ?>/exhibitors.php?delete=<?= $x['id'] ?>&csrf=<?= csrf_token() ?>">Delete</a>
  </td>
</tr>
<?php endforeach; ?></table><?php endif; ?></div>
<?php layout_foot(); ?>
