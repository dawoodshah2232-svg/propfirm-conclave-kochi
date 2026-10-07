<?php
$page_title = 'Tickets';
$page_desc  = 'Get your pass for PropFirm Conclave Kochi 2026 — 12–13 December at Adlux Convention Centre.';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';

$done = isset($_GET['done']);
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = null;
    foreach (ticket_types_list() as $t) if ((int)$t['id'] === (int)($_POST['type_id'] ?? 0)) $type = $t;
    $name = trim($_POST['name'] ?? ''); $email = trim($_POST['email'] ?? '');
    if (!$type) $err = 'Please choose a ticket type.';
    elseif ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $err = 'Please enter your name and a valid email.';
    else {
        $qty = max(1, min(10, (int)($_POST['qty'] ?? 1)));
        $db = site_db();
        if ($db) {
            try {
                $db->prepare('INSERT INTO ticket_orders (type_id, buyer_name, buyer_email, buyer_phone, qty, amount, status)
                              VALUES (?,?,?,?,?,?,?)')
                   ->execute([$type['id'], $name, $email, trim($_POST['phone'] ?? ''), $qty, (float)$type['price'] * $qty, 'pending']);
                header('Location: tickets.php?done=1'); exit;
            } catch (Throwable $e) { $err = 'Something went wrong — please try again.'; }
        } else { $err = 'Booking is temporarily unavailable — please try again shortly.'; }
    }
}
$types = ticket_types_list();
?>
<section class="page-hero">
  <div class="wrap">
    <span class="eyebrow rv">Tickets</span>
    <h1 class="h-page rv">Get your <span class="gold-text">pass.</span></h1>
    <p class="lead rv">Two days. Twenty speakers. Fifteen prop firms. Be in the room.</p>
  </div>
</section>

<section>
  <div class="wrap">
    <?php if ($done): ?>
      <div class="panel" style="text-align:center;padding:60px 30px">
        <h2 style="margin-bottom:12px">You're on the list. 🎟️</h2>
        <p style="color:var(--muted);max-width:520px;margin:0 auto">Your booking request is received — our team will confirm payment and send your pass to your email shortly.</p>
        <div style="margin-top:24px"><a class="btn btn-gold" href="index.php">Back to home</a></div>
      </div>
    <?php else: ?>
    <?php if ($err): ?><div class="alert alert-err"><?= site_esc($err) ?></div><?php endif; ?>
    <div class="grid-3" style="margin-bottom:50px">
      <?php foreach ($types as $i => $t): ?>
      <div class="panel rv<?= $i%3==1?' rv-d1':($i%3==2?' rv-d2':'') ?>" style="<?= $t['name']==='Standard' ? 'border-color:rgba(201,162,75,.55);box-shadow:0 20px 60px -20px rgba(201,162,75,.3)' : '' ?>">
        <?php if ($t['name']==='Standard'): ?><span class="badge b-gold" style="margin-bottom:12px">Most popular</span><?php endif; ?>
        <h2 style="font-size:1.3rem"><?= site_esc($t['name']) ?></h2>
        <div style="font-family:var(--font-display);font-size:2.4rem;color:var(--gold-hi);margin:10px 0">₹<?= number_format((float)$t['price']) ?></div>
        <p style="color:var(--muted);font-size:14.5px;min-height:44px"><?= site_esc($t['description']) ?></p>
        <button class="btn <?= $t['name']==='Standard' ? 'btn-gold' : '' ?>" style="width:100%;justify-content:center;margin-top:14px" onclick="document.getElementById('type_id').value='<?= $t['id'] ?>';document.getElementById('book').scrollIntoView({behavior:'smooth'})">Choose <?= site_esc($t['name']) ?></button>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="panel rv" id="book" style="max-width:640px;margin:0 auto">
      <h2>Book your pass</h2>
      <form method="post">
        <div class="form-grid" style="grid-template-columns:1fr 1fr">
          <div class="field"><label>Ticket type</label>
            <select name="type_id" id="type_id" required>
              <?php foreach ($types as $t): ?><option value="<?= $t['id'] ?>"><?= site_esc($t['name']) ?> — ₹<?= number_format((float)$t['price']) ?></option><?php endforeach; ?>
            </select></div>
          <div class="field"><label>Quantity</label>
            <select name="qty"><?php for ($q=1;$q<=10;$q++): ?><option><?= $q ?></option><?php endfor; ?></select></div>
          <div class="field"><label>Full name</label><input name="name" required autocomplete="name"></div>
          <div class="field"><label>Email</label><input name="email" type="email" required autocomplete="email"></div>
          <div class="field full"><label>Phone</label><input name="phone" autocomplete="tel" placeholder="+91 …"></div>
        </div>
        <button class="btn btn-gold" type="submit" style="width:100%;justify-content:center;margin-top:20px">Reserve My Pass</button>
        <p style="color:var(--muted);font-size:12.5px;text-align:center;margin-top:12px">Pay on confirmation — UPI, cards and bank transfer accepted.</p>
      </form>
    </div>
    <?php endif; ?>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
