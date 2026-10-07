<?php
$page_title = 'Gallery';
$page_desc  = 'Glimpses from PropFirm Conclave — the stage, the floor and the people.';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
$photos = gallery_list();
?>
<section class="page-hero">
  <div class="wrap">
    <span class="eyebrow rv">Gallery</span>
    <h1 class="h-page rv">Moments from <span class="gold-text">the conclave.</span></h1>
    <p class="lead rv">The stage, the expo floor and the people who make it happen.</p>
  </div>
</section>

<section>
  <div class="wrap">
    <?php if (!$photos): ?>
      <div class="empty" style="padding:60px;text-align:center;color:var(--muted)">Gallery opens soon — check back closer to the event.</div>
    <?php else: ?>
    <div class="gal-grid">
      <?php foreach ($photos as $i => $g): ?>
      <a class="gal-item rv<?= $i%3==1?' rv-d1':($i%3==2?' rv-d2':'') ?>" href="<?= site_esc(upload_public($g['image'])) ?>" data-lightbox>
        <img src="<?= site_esc(upload_public($g['image'])) ?>" alt="<?= site_esc($g['title'] ?: 'PropFirm Conclave gallery photo') ?>" loading="lazy">
        <?php if ($g['title']): ?><span class="cap"><?= site_esc($g['title']) ?></span><?php endif; ?>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<style>
.gal-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}
@media(min-width:760px){.gal-grid{grid-template-columns:repeat(3,1fr);gap:18px}}
.gal-item{position:relative;border-radius:var(--radius);overflow:hidden;border:1px solid var(--line);display:block;aspect-ratio:4/3}
.gal-item img{width:100%;height:100%;object-fit:cover;transition:transform .8s var(--ease)}
.gal-item:hover img{transform:scale(1.06)}
.gal-item .cap{position:absolute;left:0;right:0;bottom:0;padding:26px 16px 12px;font-size:12.5px;color:#eee;background:linear-gradient(180deg,transparent,rgba(0,0,0,.8))}
.lightbox{position:fixed;inset:0;z-index:200;background:rgba(0,0,0,.92);display:flex;align-items:center;justify-content:center;padding:24px;cursor:zoom-out}
.lightbox img{max-width:92vw;max-height:88vh;border-radius:12px;border:1px solid rgba(201,162,75,.4)}
</style>
<script>
document.querySelectorAll('[data-lightbox]').forEach(function(a){
  a.addEventListener('click', function(e){
    e.preventDefault();
    var lb = document.createElement('div');
    lb.className = 'lightbox';
    lb.innerHTML = '<img src="' + a.href + '" alt="">';
    lb.addEventListener('click', function(){ lb.remove(); });
    document.addEventListener('keydown', function esc(ev){ if(ev.key==='Escape'){ lb.remove(); document.removeEventListener('keydown', esc); } });
    document.body.appendChild(lb);
  });
});
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
