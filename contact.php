<?php
$page_title = 'Contact';
$page_desc  = 'Get in touch with the PropFirm Conclave Kochi 2026 team.';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';

$sent = false; $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? ''); $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? ''); $message = trim($_POST['message'] ?? '');
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
        $err = 'Please fill your name, a valid email and your message.';
    } else {
        $db = site_db();
        if ($db) {
            try {
                $db->prepare('INSERT INTO enquiries (name, email, subject, message) VALUES (?,?,?,?)')
                   ->execute([$name, $email, $subject, $message]);
                $sent = true;
            } catch (Throwable $e) { $err = 'Something went wrong — please try again.'; }
        } else { $err = 'Message service is temporarily unavailable.'; }
    }
}
$email = site_setting('contact_email', 'events@finfluenze.com');
?>
<section class="page-hero">
  <div class="wrap">
    <span class="eyebrow rv">Contact</span>
    <h1 class="h-page rv">Talk <span class="gold-text">to us.</span></h1>
    <p class="lead rv">Tickets, sponsorships, speaking, press — we reply within one business day.</p>
  </div>
</section>

<section>
  <div class="wrap">
    <div class="grid-3" style="grid-template-columns:1fr;gap:26px;max-width:880px;margin:0 auto">
    <?php if ($sent): ?>
      <div class="panel" style="text-align:center;padding:60px 30px">
        <h2 style="margin-bottom:12px">Message received. ✓</h2>
        <p style="color:var(--muted)">Thanks <?= site_esc($_POST['name'] ?? '') ?> — we'll get back to you shortly.</p>
      </div>
    <?php else: ?>
      <?php if ($err): ?><div class="alert alert-err"><?= site_esc($err) ?></div><?php endif; ?>
      <div class="panel rv">
        <h2>Send a message</h2>
        <form method="post">
          <div class="form-grid">
            <div class="field"><label>Name</label><input name="name" required value="<?= site_esc($_POST['name'] ?? '') ?>"></div>
            <div class="field"><label>Email</label><input name="email" type="email" required value="<?= site_esc($_POST['email'] ?? '') ?>"></div>
            <div class="field full"><label>Subject</label>
              <select name="subject">
                <option>General enquiry</option><option>Tickets</option>
                <option>Sponsorship</option><option>Speaking</option><option>Press / Media</option>
              </select></div>
            <div class="field full"><label>Message</label><textarea name="message" required style="min-height:150px"><?= site_esc($_POST['message'] ?? '') ?></textarea></div>
          </div>
          <button class="btn btn-gold" type="submit" style="margin-top:20px">Send Message</button>
        </form>
      </div>
      <div class="panel rv rv-d1">
        <h2>Direct</h2>
        <p style="color:var(--muted);margin-bottom:14px">Prefer email? Reach us at</p>
        <p style="margin-bottom:8px"><a href="mailto:<?= site_esc($email) ?>" style="color:var(--gold-hi);font-size:1.1rem"><?= site_esc($email) ?></a></p>
        <p><a href="mailto:<?= site_esc(site_setting('sponsor_email', 'sponsors@finfluenze.com')) ?>" style="color:var(--gold-hi)"><?= site_esc(site_setting('sponsor_email', 'sponsors@finfluenze.com')) ?></a> <small style="color:var(--muted)">— sponsorships</small></p>
      </div>
    <?php endif; ?>
    </div>
  </div>
</section>

<style>
.alert{padding:13px 18px;border-radius:12px;margin-bottom:20px;font-size:14px}
.alert-err{background:rgba(255,93,108,.1);border:1px solid rgba(255,93,108,.35);color:#ffb3bb}
.form-grid{display:grid;gap:16px;grid-template-columns:1fr}
@media(min-width:640px){.form-grid{grid-template-columns:1fr 1fr}.form-grid .full{grid-column:1/-1}}
.field label{display:block;font-size:11.5px;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);margin-bottom:8px;font-weight:600}
.field input,.field select,.field textarea{width:100%;background:rgba(255,255,255,.045);border:1px solid var(--line);border-radius:10px;padding:12px 15px;color:var(--text);font-size:15px;font-family:inherit}
.field select option{background:#121218}
.panel{background:var(--surface);border:1px solid var(--line);border-radius:var(--radius);padding:28px}
.panel h2{font-size:1.3rem;margin-bottom:18px}
</style>
<?php include __DIR__ . '/includes/footer.php'; ?>
