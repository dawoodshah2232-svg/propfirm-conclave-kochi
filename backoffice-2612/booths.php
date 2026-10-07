<?php
// Booths: codes, sizes, prices, availability.
require_once __DIR__ . '/_layout.php';
require_login();

if (isset($_GET['delete'])) {
    csrf_check_get();
    $used = (int)qv('SELECT COUNT(*) FROM exhibitors WHERE booth_id = ?', [(int)$_GET['delete']]);
    if ($used) flash('Booth is assigned to an exhibitor — unassign first.', 'err');
    else { db()->prepare('DELETE FROM booths WHERE id = ?')->execute([(int)$_GET['delete']]); flash('Booth deleted.'); }
    header('Location: ' . ADMIN_BASE . '/booths.php'); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
    csrf_check();
    $data = [$_POST['code'], $_POST['size'], (float)$_POST['price'], $_POST['status']];
    if (!empty($_POST['id'])) {
        $data[] = (int)$_POST['id'];
        db()->prepare('UPDATE booths SET code=?, size=?, price=?, status=? WHERE id=?')->execute($data);
        flash('Booth updated.');
    } else {
        db()->prepare('INSERT INTO booths (code, size, price, status) VALUES (?,?,?,?)')->execute($data);
        flash('Booth added.');
    }
    header('Location: ' . ADMIN_BASE . '/booths.php'); exit;
}

$edit = isset($_GET['edit']) ? q1('SELECT * FROM booths WHERE id=?', [(int)$_GET['edit']]) : null;
$list = q('SELECT b.*, e.company FROM booths b LEFT JOIN exhibitors e ON e.booth_id=b.id ORDER BY b.code');
layout_head('Booths');
?>
<div class="toolbar"><div class="left"></div>
  <a class="btn btn-gold" href="<?= ADMIN_BASE ?>/booths.php?edit=new">+ Add Booth</a></div>

<?php if ($edit || isset($_GET['edit'])): $e = $edit ?: ['id'=>'','code'=>'','size'=>'3x3 m','price'=>'','status'=>'available']; ?>
<div class="panel"><h2><?= $edit ? 'Edit' : 'Add' ?> Booth</h2>
  <form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <input type="hidden" name="id" value="<?= esc($e['id']) ?>">
    <div class="form-grid">
      <div class="field"><label>Booth code</label><input name="code" required placeholder="e.g. A5" value="<?= esc($e['code']) ?>"></div>
      <div class="field"><label>Size</label><input name="size" value="<?= esc($e['size']) ?>"></div>
      <div class="field"><label>Price (₹)</label><input name="price" type="number" step="0.01" min="0" value="<?= esc($e['price']) ?>"></div>
      <div class="field"><label>Status</label><select name="status">
        <?php foreach (['available','reserved','sold'] as $s): ?>
        <option <?= $e['status']===$s?'selected':'' ?>><?= $s ?></option><?php endforeach; ?>
      </select></div>
    </div>
    <div style="margin-top:20px;display:flex;gap:10px">
      <button class="btn btn-gold" type="submit">Save</button>
      <a class="btn" href="<?= ADMIN_BASE ?>/booths.php">Cancel</a>
    </div></form></div>
<?php endif; ?>

<div class="panel"><h2>Booth map (<?= count($list) ?>)</h2>
<table class="tbl"><tr><th>Code</th><th>Size</th><th>Price</th><th>Status</th><th>Exhibitor</th><th></th></tr>
<?php foreach ($list as $b): ?>
<tr>
  <td><b><?= esc($b['code']) ?></b></td><td><?= esc($b['size']) ?></td><td><?= money($b['price']) ?></td>
  <td><span class="badge <?= $b['status']==='sold'?'b-red':($b['status']==='reserved'?'b-gold':'b-green') ?>"><?= esc($b['status']) ?></span></td>
  <td><?= $b['company'] ? esc($b['company']) : '<span style="color:var(--muted)">—</span>' ?></td>
  <td style="white-space:nowrap">
    <a class="btn btn-sm" href="<?= ADMIN_BASE ?>/booths.php?edit=<?= $b['id'] ?>">Edit</a>
    <a class="btn btn-sm btn-danger" data-confirm="Delete this booth?" href="<?= ADMIN_BASE ?>/booths.php?delete=<?= $b['id'] ?>&csrf=<?= csrf_token() ?>">Delete</a>
  </td>
</tr>
<?php endforeach; ?></table></div>
<?php layout_foot(); ?>
