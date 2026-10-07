<?php
$page_title = 'Blog';
$page_desc  = 'Prop trading insights — challenges, risk management and funded-trader playbooks.';
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/header.php';
$posts = site_q("SELECT * FROM posts WHERE status='published' ORDER BY published_at DESC");
?>
<section class="page-hero">
  <div class="wrap">
    <span class="eyebrow rv">Insights</span>
    <h1 class="h-page rv">Learn before <span class="gold-text">you arrive.</span></h1>
    <p class="lead rv">Prop trading basics, challenge mechanics and risk frameworks.</p>
  </div>
</section>

<section>
  <div class="wrap">
    <?php if (!$posts): ?>
      <div class="empty" style="padding:60px;text-align:center;color:var(--muted)">Articles are on the way.</div>
    <?php else: ?>
    <div class="post-grid">
      <?php foreach ($posts as $i => $p): ?>
      <a class="post rv<?= $i%3==1?' rv-d1':($i%3==2?' rv-d2':'') ?>" href="post.php?slug=<?= site_esc($p['slug']) ?>">
        <?php if ($p['cover']): ?><div class="thumb"><img src="../<?= site_esc(upload_public($p['cover'])) ?>" alt="<?= site_esc($p['title']) ?>" loading="lazy"></div><?php endif; ?>
        <div class="body">
          <h3><?= site_esc($p['title']) ?></h3>
          <p><?= site_esc($p['excerpt']) ?></p>
          <div class="meta"><span><?= $p['published_at'] ? date('d M Y', strtotime($p['published_at'])) : '' ?></span><span class="more">Read →</span></div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
