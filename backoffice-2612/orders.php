<?php
// Ticket orders / attendees: status changes, check-in, manual entry.
require_once __DIR__ . '/_layout.php';
require_login();

$act = $_GET['act'] ?? '';
$id  = (int)($_GET['id'] ?? 0);
if ($act && $id) {
    csrf_check_get();
    if ($act === 'paid')      db()->prepare("UPDATE ticket_orders SET status='paid' WHERE id=?")->execute([$id]);
    if ($act === 'cancel')    db()->prepare("UPDATE ticket_orders SET status='cancelled' WHERE id=?")->execute([$id]);
    if ($act === 'checkin')   db()->prepare("UPDATE ticket_orders SET checked_in=1 WHERE id=?")->execute([$id]);
    if ($act === 'uncheckin') db()->prepare("UPDATE ticket_orders SET checked_in=0 WHERE id=?")->execute([$id]);
    if ($act === 'delete')    db()->prepare("DELETE FROM ticket_orders WHERE id=?")->execute([$id]);
    flash('Order updated.');
    header('Location: ' . ADMIN_BASE . '/orders.php'); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buyer_name'])) {
    csrf_check();
    $type = q1('SELECT * FROM ticket_types WHERE id=?', [(int)$_POST['type_id']]);
    $qty = max(1, (int)$_POST['qty']);
    $amount = $type ? (float)$type['price'] * $qty : 0;
    db()->prepare('INSERT INTO ticket_orders (type_id, buyer_name, buyer_email, buyer_phone, qty, amount, status)
                   VALUES (?,?,?,?,?,?,?)')
       ->execute([(int)$_POST['type_id'], $_POST['buyer_name'], $_POST['buyer_email'], $_POST['buyer_phone'], $qty, $amount, $_POST['status']]);
    flash('Order added.');
    header('Location: ' . ADMIN_BASE . '/orders.php'); exit;
}

$types = q('SELECT * FROM ticket_types WHERE status="active" ORDER BY sort');
$st = $_GET['st'] ?? '';
$where = $st ? 'WHERE o.status = ' . db()->quote($st) : '';
$list = q("SELECT o.*, t.name AS type_name FROM ticket_orders o LEFT JOIN ticket_types t ON t.id=o.type_id $where ORDER BY o.created_at DESC LIMIT 200");
layout_head('Ticket Orders');
?>
<div class="toolbar">
  <div class="left">
    <div class="tabs" style="margin:0">
      <a class="tab <?= $st===''?'active':'' ?>" href="<?= ADMIN_BASE ?>/orders.php">All</a>
      <a class="tab <?= $st==='paid'?'active':'' ?>" href="<?= ADMIN_BASE ?>/orders.php?st=paid">Paid</a>
      <a class="tab <?= $st==='pending'?'active':'' ?>" href="<?= ADMIN_BASE ?>/orders.php?st=pending">Pending</a>
      <a class="tab <?= $st==='cancelled'?'active':'' ?>" href="<?= ADMIN_BASE ?>/orders.php?st=cancelled">Cancelled</a>
    </div>
  </div>
</div>

<div class="panel"><h2>Record manual order</h2>
  <form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <div class="form-grid">
      <div class="field"><label>Buyer name</label><input name="buyer_name" required></div>
      <div class="field"><label>Email</label><input name="buyer_email" type="email" required></div>
      <div class="field"><label>Phone</label><input name="buyer_phone"></div>
      <div class="field"><label>Ticket type</label><select name="type_id" required>
        <?php foreach ($types as $t): ?><option value="<?= $t['id'] ?>"><?= esc($t['name']) ?> — <?= money($t['price']) ?></option><?php endforeach; ?>
      </select></div>
      <div class="field"><label>Qty</label><input name="qty" type="number" min="1" value="1" required></div>
      <div class="field"><label>Status</label><select name="status">
        <option value="paid">Paid</option><option value="pending">Pending</option></select></div>
    </div>
    <div style="margin-top:16px"><button class="btn btn-gold" type="submit">Add Order</button></div>
  </form></div>

<div class="panel"><h2>Orders (<?= count($list) ?>)</h2>
<?php if (!$list): ?><div class="empty">No orders yet.</div><?php else: ?>
<table class="tbl"><tr><th>Buyer</th><th>Type</th><th>Qty</th><th>Amount</th><th>Status</th><th>Check-in</th><th>Date</th><th></th></tr>
<?php foreach ($list as $o): ?>
<tr>
  <td><b><?= esc($o['buyer_name']) ?></b><br><small style="color:var(--muted)"><?= esc($o['buyer_email']) ?><?= $o['buyer_phone'] ? ' · '.esc($o['buyer_phone']) : '' ?></small></td>
  <td><?= esc($o['type_name'] ?? '—') ?></td><td><?= $o['qty'] ?></td><td><?= money($o['amount']) ?></td>
  <td><span class="badge <?= $o['status']==='paid'?'b-green':($o['status']==='cancelled'?'b-red':'b-gold') ?>"><?= esc($o['status']) ?></span></td>
  <td><?= $o['checked_in'] ? '<span class="badge b-green">in</span>' : '<span class="badge b-grey">no</span>' ?></td>
  <td><small style="color:var(--muted)"><?= date('d M, H:i', strtotime($o['created_at'])) ?></small></td>
  <td style="white-space:nowrap">
    <?php if ($o['status']==='pending'): ?><a class="btn btn-sm" href="<?= ADMIN_BASE ?>/orders.php?act=paid&id=<?= $o['id'] ?>&csrf=<?= csrf_token() ?>">Mark paid</a><?php endif; ?>
    <?php if (!$o['checked_in']): ?><a class="btn btn-sm" href="<?= ADMIN_BASE ?>/orders.php?act=checkin&id=<?= $o['id'] ?>&csrf=<?= csrf_token() ?>">Check in</a>
    <?php else: ?><a class="btn btn-sm" href="<?= ADMIN_BASE ?>/orders.php?act=uncheckin&id=<?= $o['id'] ?>&csrf=<?= csrf_token() ?>">Undo</a><?php endif; ?>
    <?php if ($o['status']!=='cancelled'): ?><a class="btn btn-sm btn-danger" data-confirm="Cancel this order?" href="<?= ADMIN_BASE ?>/orders.php?act=cancel&id=<?= $o['id'] ?>&csrf=<?= csrf_token() ?>">Cancel</a><?php endif; ?>
  </td>
</tr>
<?php endforeach; ?></table><?php endif; ?></div>
<?php layout_foot(); ?>
