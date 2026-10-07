<?php
// Site settings: identity, contact, social links, event facts.
require_once __DIR__ . '/_layout.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach ($_POST['s'] ?? [] as $k => $v) save_setting($k, trim($v));
    flash('Settings saved — the website now uses the new values.');
    header('Location: ' . ADMIN_BASE . '/settings.php'); exit;
}

$groups = [
  'Event' => ['site_name','tagline','event_dates','venue_name','venue_city','currency'],
  'Contact' => ['contact_email','sponsor_email'],
  'Social links' => ['instagram_url','facebook_url','x_url','youtube_url','linkedin_url'],
];
$labels = [
  'site_name'=>'Site name','tagline'=>'Tagline','event_dates'=>'Event dates','venue_name'=>'Venue name',
  'venue_city'=>'Venue city','currency'=>'Currency symbol','contact_email'=>'Contact email',
  'sponsor_email'=>'Sponsor email','instagram_url'=>'Instagram URL','facebook_url'=>'Facebook URL',
  'x_url'=>'X (Twitter) URL','youtube_url'=>'YouTube URL','linkedin_url'=>'LinkedIn URL',
];
layout_head('Settings');
?>
<form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>">
<?php foreach ($groups as $g => $keys): ?>
<div class="panel"><h2><?= esc($g) ?></h2>
  <div class="form-grid">
    <?php foreach ($keys as $k): ?>
    <div class="field"><label><?= esc($labels[$k]) ?></label>
      <input name="s[<?= esc($k) ?>]" value="<?= esc(setting($k)) ?>"></div>
    <?php endforeach; ?>
  </div>
</div>
<?php endforeach; ?>
<div style="position:sticky;bottom:20px;background:var(--surface);border:1px solid rgba(201,162,75,.4);border-radius:14px;padding:16px">
  <button class="btn btn-gold" type="submit" style="width:100%;justify-content:center">Save All Settings</button>
</div>
</form>
<?php layout_foot(); ?>
