<?php
// P&L: income/expense ledger + summary report.
require_once __DIR__ . '/_layout.php';
require_login();

if (isset($_GET['delete'])) {
    csrf_check_get();
    db()->prepare('DELETE FROM transactions WHERE id = ?')->execute([(int)$_GET['delete']]);
    flash('Entry deleted.');
    header('Location: ' . ADMIN_BASE . '/pnl.php'); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['description'])) {
    csrf_check();
    db()->prepare('INSERT INTO transactions (ttype, category, description, amount, txn_date) VALUES (?,?,?,?,?)')
       ->execute([$_POST['ttype'], $_POST['category'], $_POST['description'], (float)$_POST['amount'], $_POST['txn_date']]);
    // Auto-sync paid ticket revenue into the ledger view is done via the summary below.
    flash('Entry added.');
    header('Location: ' . ADMIN_BASE . '/pnl.php'); exit;
}

$income  = (float)qv("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE ttype='income'");
$expense = (float)qv("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE ttype='expense'");
$ticket_rev = (float)qv("SELECT COALESCE(SUM(amount),0) FROM ticket_orders WHERE status='paid'");
$sponsor_rev = (float)qv("SELECT COALESCE(SUM(amount),0) FROM exhibitors WHERE status='confirmed'");
$total_in = $income + $ticket_rev + $sponsor_rev;

$by_cat = q("SELECT ttype, category, COALESCE(SUM(amount),0) AS total FROM transactions GROUP BY ttype, category ORDER BY total DESC");
$list = q('SELECT * FROM transactions ORDER BY txn_date DESC, id DESC LIMIT 200');
$cats = ['Sponsorship','Tickets','Booth sales','Venue','Marketing','Staff','Travel','Food & Beverage','Production','Misc'];
layout_head('P&L');
?>
<div class="cards">
  <div class="card"><small>Total income</small><b style="color:var(--green)"><?= money($total_in) ?></b>
    <div class="sub">ledger <?= money($income) ?> · tickets <?= money($ticket_rev) ?> · exhibitors <?= money($sponsor_rev) ?></div></div>
  <div class="card"><small>Total expenses</small><b style="color:var(--red)"><?= money($expense) ?></b></div>
  <div class="card"><small>Net P&amp;L</small><b style="color:<?= ($total_in-$expense)>=0?'var(--green)':'var(--red)' ?>"><?= money($total_in - $expense) ?></b></div>
</div>

<div class="split2">
<div class="panel"><h2>Add entry</h2>
  <form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <div class="form-grid">
      <div class="field"><label>Type</label><select name="ttype">
        <option value="income">Income</option><option value="expense">Expense</option></select></div>
      <div class="field"><label>Date</label><input type="date" name="txn_date" required value="<?= date('Y-m-d') ?>"></div>
      <div class="field"><label>Category</label><select name="category">
        <?php foreach ($cats as $c): ?><option><?= esc($c) ?></option><?php endforeach; ?></select></div>
      <div class="field"><label>Amount (₹)</label><input name="amount" type="number" step="0.01" min="0" required></div>
      <div class="field full"><label>Description</label><input name="description" required placeholder="e.g. Stage production advance"></div>
    </div>
    <div style="margin-top:16px"><button class="btn btn-gold" type="submit">Add Entry</button></div>
  </form></div>

<div class="panel"><h2>By category</h2>
  <?php if (!$by_cat): ?><div class="empty">No ledger entries yet.</div><?php else: ?>
  <table class="tbl"><tr><th>Category</th><th>Type</th><th style="text-align:right">Total</th></tr>
  <?php foreach ($by_cat as $r): ?>
  <tr><td><?= esc($r['category']) ?></td>
    <td><span class="badge <?= $r['ttype']==='income'?'b-green':'b-red' ?>"><?= esc($r['ttype']) ?></span></td>
    <td style="text-align:right"><?= money($r['total']) ?></td></tr>
  <?php endforeach; ?></table><?php endif; ?></div>
</div>

<div class="panel"><h2>Ledger (<?= count($list) ?>)</h2>
<?php if (!$list): ?><div class="empty">Ledger is empty.</div><?php else: ?>
<table class="tbl"><tr><th>Date</th><th>Description</th><th>Category</th><th>Type</th><th style="text-align:right">Amount</th><th></th></tr>
<?php foreach ($list as $t): ?>
<tr>
  <td><small style="color:var(--muted)"><?= date('d M Y', strtotime($t['txn_date'])) ?></small></td>
  <td><?= esc($t['description']) ?></td><td><?= esc($t['category']) ?></td>
  <td><span class="badge <?= $t['ttype']==='income'?'b-green':'b-red' ?>"><?= esc($t['ttype']) ?></span></td>
  <td style="text-align:right"><?= money($t['amount']) ?></td>
  <td><a class="btn btn-sm btn-danger" data-confirm="Delete this entry?" href="<?= ADMIN_BASE ?>/pnl.php?delete=<?= $t['id'] ?>&csrf=<?= csrf_token() ?>">Delete</a></td>
</tr>
<?php endforeach; ?></table><?php endif; ?></div>
<?php layout_foot(); ?>
