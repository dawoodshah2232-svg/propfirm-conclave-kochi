<?php
$page_title = 'Sponsors';
$page_desc  = 'The prop firms, brokers and fintech brands exhibiting at PropFirm Conclave Kochi 2026.';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
$groups = sponsors_by_tier();
?>
<section class="page-hero">
  <div class="wrap">
    <span class="eyebrow rv">Sponsors</span>
    <h1 class="h-page rv">Brands on <span class="gold-text">the floor.</span></h1>
    <p class="lead rv">Prop firms, brokers and fintech companies meeting thousands of traders face to face.</p>
  </div>
</section>

<section>
  <div class="wrap">
    <?php if (!$groups): ?>
      <div class="empty" style="padding:60px;text-align:center;color:var(--muted)">Sponsor announcements begin soon.</div>
    <?php else: foreach ($groups as $gi => $g): ?>
    <div class="center" style="margin:<?= $gi ? '54' : '10' ?>px 0 22px">
      <span class="eyebrow rv" style="color:<?= site_esc($g['tier']['color'] ?? '#c9a24b') ?>"><?= site_esc($g['tier']['name']) ?></span>
    </div>
    <div class="logo-wall">
      <?php foreach ($g['sponsors'] as $i => $x): ?>
      <div class="logo-tile logo rv<?= $i%4==1?' rv-d1':($i%4==2?' rv-d2':($i%4==3?' rv-d3':'')) ?>">
        <?php if ($x['website']): ?><a href="<?= site_esc($x['website']) ?>" target="_blank" rel="noopener"><?php endif; ?>
        <img src="<?= site_esc(upload_public($x['logo'])) ?>" alt="<?= site_esc($x['name']) ?> logo" loading="lazy">
        <?php if ($x['website']): ?></a><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endforeach; endif; ?>

    <div class="cta-band rv" data-watermark="Partner" style="margin-top:70px">
              <span class="eyebrow" style="justify-content:center">Sponsorships</span>
        <h2>Put your brand <span class="gold-text">in the room.</span></h2>
        <p style="max-width:560px;margin-left:auto;margin-right:auto">Exhibition booths, speaking slots and branding across two days in front of India's funded-trading community.</p>
        <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:26px">
          <a class="btn btn-gold" href="contact.php">Become a Sponsor</a>
        </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
