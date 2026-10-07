<?php
$page_title = 'FAQ';
$page_desc  = 'Frequently asked questions about PropFirm Conclave Kochi 2026.';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
$faqs = faqs_list();
?>
<section class="page-hero">
  <div class="wrap">
    <span class="eyebrow rv">FAQ</span>
    <h1 class="h-page rv">Questions, <span class="gold-text">answered.</span></h1>
  </div>
</section>

<section>
  <div class="wrap" style="max-width:820px">
    <?php if (!$faqs): ?>
      <div class="empty" style="padding:60px;text-align:center;color:var(--muted)">FAQs are being put together — check back soon.</div>
    <?php else: ?>
    <div class="faq-list">
      <?php foreach ($faqs as $i => $f): ?>
      <details class="faq-item rv<?= $i%3==1?' rv-d1':($i%3==2?' rv-d2':'') ?>">
        <summary><?= site_esc($f['question']) ?></summary>
        <p><?= nl2br(site_esc($f['answer'])) ?></p>
      </details>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <div class="rv" style="text-align:center;margin-top:36px">
      <p style="color:var(--muted);margin-bottom:16px">Still curious about something?</p>
      <a class="btn btn-ghost btn-sm" href="contact.php">Ask us directly</a>
    </div>
  </div>
</section>

<style>
.faq-list{display:grid;gap:12px}
.faq-item{background:var(--surface);border:1px solid var(--line);border-radius:var(--radius);overflow:hidden}
.faq-item summary{padding:20px 22px;cursor:pointer;font-weight:600;font-size:1.02rem;list-style:none;display:flex;justify-content:space-between;align-items:center;gap:14px}
.faq-item summary::-webkit-details-marker{display:none}
.faq-item summary::after{content:'+';color:var(--gold-hi);font-size:1.4rem;flex:none;transition:transform .3s var(--ease)}
.faq-item[open] summary::after{transform:rotate(45deg)}
.faq-item p{padding:0 22px 22px;color:var(--muted);font-size:15px}
</style>
<?php include __DIR__ . '/includes/footer.php'; ?>
