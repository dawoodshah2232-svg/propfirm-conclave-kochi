<?php
require_once __DIR__ . '/_layout.php';
require_login();
layout_head('Dashboard');

$tickets_sold = (int)qv("SELECT COALESCE(SUM(qty),0) FROM ticket_orders WHERE status='paid'");
$ticket_rev   = (float)qv("SELECT COALESCE(SUM(amount),0) FROM ticket_orders WHERE status='paid'");
$pending      = (int)qv("SELECT COUNT(*) FROM ticket_orders WHERE status='pending'");
$exhibitors   = (int)qv("SELECT COUNT(*) FROM exhibitors WHERE status='confirmed'");
$booths_sold  = (int)qv("SELECT COUNT(*) FROM booths WHERE status='sold'");
$booths_total = (int)qv("SELECT COUNT(*) FROM booths");
$income       = (float)qv("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE ttype='income'");
$expense      = (float)qv("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE ttype='expense'");
$subs         = (int)qv("SELECT COUNT(*) FROM subscribers");
$new_enq      = (int)qv("SELECT COUNT(*) FROM enquiries WHERE status='new'");
$booth_pct    = $booths_total ? round($booths_sold / $booths_total * 100) : 0;

$recent_orders = q("SELECT o.*, t.name AS type_name FROM ticket_orders o
                   LEFT JOIN ticket_types t ON t.id=o.type_id
                   ORDER BY o.created_at DESC LIMIT 6");
$recent_enq = q("SELECT * FROM enquiries ORDER BY created_at DESC LIMIT 5");
?>
<div class="cards">
  <div class="card"><small>Tickets sold</small><b><?= $tickets_sold ?></b><div class="sub"><?= $pending ?> pending payment</div></div>
  <div class="card"><small>Ticket revenue</small><b><?= money($ticket_rev) ?></b><div class="sub">paid orders only</div></div>
  <div class="card"><small>Exhibitors</small><b><?= $exhibitors ?></b><div class="sub">confirmed</div></div>
  <div class="card"><small>Booths sold</small><b><?= $booths_sold ?>/<?= $booths_total ?></b>
    <div class="sub"><div class="progress" style="margin-top:8px"><i style="width:<?= $booth_pct ?>%"></i></div></div></div>
  <div class="card"><small>P&amp;L — net</small><b style="color:<?= ($income-$expense)>=0?'var(--green)':'var(--red)' ?>"><?= money($income - $expense) ?></b><div class="sub"><?= money($income) ?> in · <?= money($expense) ?> out</div></div>
  <div class="card"><small>Audience</small><b><?= $subs ?></b><div class="sub">subscribers · <?= $new_enq ?> new enquiries</div></div>
</div>

<div class="split2">
  <div class="panel">
    <h2>Recent ticket orders <a class="btn btn-sm" href="<?= ADMIN_BASE ?>/orders.php">View all</a></h2>
    <?php if (!$recent_orders): ?><div class="empty">No orders yet.</div>
    <?php else: ?>
    <table class="tbl">
      <tr><th>Buyer</th><th>Type</th><th>Amount</th><th>Status</th></tr>
      <?php foreach ($recent_orders as $o): ?>
      <tr>
        <td><?= esc($o['buyer_name']) ?><br><small style="color:var(--muted)"><?= esc($o['buyer_email']) ?></small></td>
        <td><?= esc($o['type_name'] ?? '—') ?> × <?= $o['qty'] ?></td>
        <td><?= money($o['amount']) ?></td>
        <td><span class="badge <?= $o['status']==='paid'?'b-green':($o['status']==='cancelled'?'b-red':'b-gold') ?>"><?= esc($o['status']) ?></span></td>
      </tr>
      <?php endforeach; ?>
    </table>
    <?php endif; ?>
  </div>
  <div class="panel">
    <h2>Latest enquiries <a class="btn btn-sm" href="<?= ADMIN_BASE ?>/enquiries.php">Inbox</a></h2>
    <?php if (!$recent_enq): ?><div class="empty">No enquiries yet.</div>
    <?php else: ?>
    <table class="tbl">
      <tr><th>From</th><th>Subject</th><th>Status</th></tr>
      <?php foreach ($recent_enq as $e): ?>
      <tr>
        <td><?= esc($e['name']) ?><br><small style="color:var(--muted)"><?= esc($e['email']) ?></small></td>
        <td><?= esc($e['subject'] ?: '(no subject)') ?></td>
        <td><span class="badge <?= $e['status']==='new'?'b-gold':($e['status']==='replied'?'b-green':'b-grey') ?>"><?= esc($e['status']) ?></span></td>
      </tr>
      <?php endforeach; ?>
    </table>
    <?php endif; ?>
  </div>
</div>
<?php layout_foot(); ?>
