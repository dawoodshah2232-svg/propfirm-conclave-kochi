<?php
require_once __DIR__ . '/../includes/site.php';
$slug = preg_replace('/[^a-z0-9\-]/i', '', $_GET['slug'] ?? '');
$post = $slug ? site_q('SELECT * FROM posts WHERE slug=? AND status="published" LIMIT 1', [$slug])[0] ?? null : null;
if (!$post) { http_response_code(404); $post = ['title' => 'Article not found', 'body' => '<p>This article does not exist.</p>', 'cover' => '', 'excerpt' => '', 'published_at' => null]; }
$page_title = $post['title'];
$page_desc  = $post['excerpt'];
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/header.php';
$more = site_q("SELECT slug, title, cover FROM posts WHERE status='published' AND slug != ? ORDER BY published_at DESC LIMIT 2", [$slug]);
?>
<section class="page-hero" style="padding-bottom:30px">
  <div class="wrap" style="max-width:820px">
    <a href="index.php" style="color:var(--muted);font-size:13px">← All articles</a>
    <h1 class="h-page rv" style="margin-top:16px"><?= site_esc($post['title']) ?></h1>
    <?php if ($post['published_at']): ?><p style="color:var(--muted);font-size:13.5px"><?= date('d F Y', strtotime($post['published_at'])) ?> · Finfluenze</p><?php endif; ?>
  </div>
</section>

<section style="padding-top:0">
  <div class="wrap" style="max-width:820px">
    <?php if ($post['cover']): ?>
    <div class="img-frame rv" style="margin-bottom:34px"><img src="../<?= site_esc(upload_public($post['cover'])) ?>" alt="<?= site_esc($post['title']) ?>"></div>
    <?php endif; ?>
    <article class="article-body rv">
      <?= $post['body'] ?>
    </article>
    <div class="cta-band rv" data-watermark="Learn it live" style="margin-top:60px;padding:60px 26px">
        <h2 style="font-size:1.8rem">Your funded journey<br><span class="gold-text">starts in Kochi.</span></h2>
        <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:22px">
          <a class="btn btn-gold" href="../tickets.php">Get Your Pass</a>
        </div>
    </div>
    <?php if ($more): ?>
    <h2 style="margin:50px 0 20px">Keep reading</h2>
    <div class="post-grid" style="grid-template-columns:1fr 1fr">
      <?php foreach ($more as $m): ?>
      <a class="post" href="post.php?slug=<?= site_esc($m['slug']) ?>">
        <?php if ($m['cover']): ?><div class="thumb"><img src="../<?= site_esc(upload_public($m['cover'])) ?>" alt="" loading="lazy"></div><?php endif; ?>
        <div class="body"><h3 style="font-size:1.1rem"><?= site_esc($m['title']) ?></h3><div class="meta"><span class="more">Read →</span></div></div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<style>
.article-body{color:#cfccc4;font-size:1.05rem;line-height:1.8}
.article-body h2{color:var(--text);font-family:var(--font-display);font-size:1.7rem;margin:34px 0 14px}
.article-body h3{color:var(--text);font-size:1.25rem;margin:28px 0 12px}
.article-body p{margin-bottom:18px}
.article-body ul,.article-body ol{margin:0 0 20px 22px}
.article-body li{margin-bottom:10px}
.article-body img{border-radius:var(--radius);margin:24px 0;max-width:100%}
.article-body strong{color:var(--text)}
.article-body a{color:var(--gold-hi)}
</style>
<?php include __DIR__ . '/../includes/footer.php'; ?>
