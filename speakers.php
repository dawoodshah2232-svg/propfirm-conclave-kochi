<?php
$page_title = 'Speakers';
$page_desc  = 'Meet the funded traders, prop firm founders and educators on stage at PropFirm Conclave Kochi 2026.';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
$list = speakers_list();
?>
<section class="page-hero">
  <div class="wrap">
    <span class="eyebrow rv">Speakers</span>
    <h1 class="h-page rv">Learn from traders <span class="gold-text">who've been funded.</span></h1>
    <p class="lead rv">Funded traders, prop firm founders, risk managers and trading psychologists on one stage.</p>
  </div>
</section>

<section>
  <div class="wrap">
    <?php if (!$list): ?>
      <div class="empty" style="padding:60px;text-align:center;color:var(--muted)">Speaker announcements begin soon.</div>
    <?php else: ?>
    <div class="speaker-grid photo-grid">
      <?php foreach ($list as $i => $p): ?>
      <div class="speaker-card rv<?= $i%3==1?' rv-d1':($i%3==2?' rv-d2':'') ?>">
        <?php if ($p['photo']): ?>
        <div class="photo"><img src="<?= site_esc(upload_public($p['photo'])) ?>" alt="<?= site_esc($p['name'].' — '.$p['role']) ?>" loading="lazy"></div>
        <?php endif; ?>
        <div class="info">
          <div class="role"><?= site_esc($p['role']) ?></div>
          <h3><?= site_esc($p['name']) ?></h3>
          <?php if ($p['company']): ?><span class="co"><?= site_esc($p['company']) ?></span><?php endif; ?>
          <?php if ($p['bio']): ?><p class="bio"><?= site_esc($p['bio']) ?></p><?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <div class="rv" style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-top:34px">
      <a class="btn btn-gold btn-sm" href="tickets.php">Get Your Pass</a>
      <a class="btn btn-ghost btn-sm" href="contact.php">Apply to speak</a>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
