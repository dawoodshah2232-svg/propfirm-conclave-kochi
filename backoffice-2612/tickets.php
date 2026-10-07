<?php
// Ticket types: name, price, description, visibility, order.
require_once __DIR__ . '/_layout.php';
require_login();

if (isset($_GET['delete'])) {
    csrf_check_get();
    $used = (int)qv('SELECT COUNT(*) FROM ticket_orders WHERE type_id = ?', [(int)$_GET['delete']]);
    if ($used) flash('Cannot delete — orders exist for this type. Hide it instead.', 'err');
    else { db()->prepare('DELETE FROM ticket_types WHERE id = ?')->execute([(int)$_GET['delete']]); flash('Ticket type deleted.'); }
    header('Location: ' . ADMIN_BASE . '/tickets.php'); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    csrf_check();
    $data = [$_POST['name'], (float)$_POST['price'], $_POST['description'], $_POST['status']];
    if (!empty($_POST['id'])) {
        $data[] = (int)$_POST['id'];
        db()->prepare('UPDATE ticket_types SET name=?, price=?, description=?, status=? WHERE id=?')->execute($data);
        flash('Ticket type updated.');
    } else {
        $max = (int)qv('SELECT COALESCE(MAX(sort),-1) FROM ticket_types') + 1;
        $data[] = $max;
        db()->prepare('INSERT INTO ticket_types (name, price, description, status, sort) VALUES (?,?,?,?,?)')->execute($data);
        flash('Ticket type added.');
    }
    header('Location: ' . ADMIN_BASE . '/tickets.php'); exit;
}

$edit = isset($_GET['edit']) ? q1('SELECT * FROM ticket_types WHERE id=?', [(int)$_GET['edit']]) : null;
$list = q('SELECT t.*, (SELECT COUNT(*) FROM ticket_orders o WHERE o.type_id=t.id AND o.status="paid") AS sold,
           (SELECT COALESCE(SUM(amount),0) FROM ticket_orders o WHERE o.type_id=t.id AND o.status="paid") AS rev
           FROM ticket_types t ORDER BY sort');
layout_head('Ticket Types');
?>
<div class="toolbar"><div class="left"></div>
  <a class="btn btn-gold" href="<?= ADMIN_BASE ?>/tickets.php?edit=new">+ Add Type</a></div>

<?php if ($edit || isset($_GET['edit'])): $e = $edit ?: ['id'=>'','name'=>'','price'=>'','description'=>'','status'=>'active']; ?>
<div class="panel"><h2><?= $edit ? 'Edit' : 'Add' ?> Ticket Type</h2>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <input type="hidden" name="id" value="<?= esc($e['id']) ?>">
    <div class="form-grid">
      <div class="field"><label>Name</label><input name="name" required value="<?= esc($e['name']) ?>"></div>
      <div class="field"><label>Price (₹)</label><input name="price" type="number" step="0.01" min="0" required value="<?= esc($e['price']) ?>"></div>
      <div class="field"><label>Status</label><select name="status">
        <option value="active" <?= $e['status']==='active'?'selected':'' ?>>Active</option>
        <option value="hidden" <?= $e['status']==='hidden'?'selected':'' ?>>Hidden</option></select></div>
      <div class="field"><label>Description</label><input name="description" value="<?= esc($e['description']) ?>"></div>
    </div>
    <div style="margin-top:20px;display:flex;gap:10px">
      <button class="btn btn-gold" type="submit">Save</button>
      <a class="btn" href="<?= ADMIN_BASE ?>/tickets.php">Cancel</a>
    </div>
  </form></div>
<?php endif; ?>

<div class="panel"><h2>Types (<?= count($list) ?>)</h2>
<table class="tbl"><tr><th>Type</th><th>Price</th><th>Sold</th><th>Revenue</th><th>Status</th><th></th></tr>
<?php foreach ($list as $t): ?>
<tr>
  <td><b><?= esc($t['name']) ?></b><br><small style="color:var(--muted)"><?= esc($t['description']) ?></small></td>
  <td><?= money($t['price']) ?></td><td><?= (int)$t['sold'] ?></td><td><?= money($t['rev']) ?></td>
  <td><span class="badge <?= $t['status']==='active'?'b-green':'b-grey' ?>"><?= esc($t['status']) ?></span></td>
  <td style="white-space:nowrap">
    <a class="btn btn-sm" href="<?= ADMIN_BASE ?>/tickets.php?edit=<?= $t['id'] ?>">Edit</a>
    <a class="btn btn-sm btn-danger" data-confirm="Delete this ticket type?" href="<?= ADMIN_BASE ?>/tickets.php?delete=<?= $t['id'] ?>&csrf=<?= csrf_token() ?>">Delete</a>
  </td>
</tr>
<?php endforeach; ?></table></div>
<?php layout_foot(); ?>
