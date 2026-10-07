<?php
$page_title = 'Agenda';
$page_desc  = 'Two days of keynotes, panels, live trading and workshops at PropFirm Conclave Kochi 2026.';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
$days = agenda_list();
?>
<section class="page-hero">
  <div class="wrap">
    <span class="eyebrow rv">Agenda</span>
    <h1 class="h-page rv">Two days, <span class="gold-text">zero fluff.</span></h1>
    <p class="lead rv">Keynotes, panels, live trading sessions and hands-on workshops.</p>
  </div>
</section>

<section>
  <div class="wrap">
    <?php if (!$days): ?>
      <div class="empty" style="padding:60px;text-align:center;color:var(--muted)">Agenda drops soon.</div>
    <?php else: ?>
    <div class="tabs" style="justify-content:center;margin-bottom:30px">
      <?php foreach ($days as $i => $d): ?>
      <a class="tab<?= $i===0?' active':'' ?>" href="#day-<?= $d['id'] ?>" data-day="<?= $d['id'] ?>"><?= site_esc($d['day_label']) ?></a>
      <?php endforeach; ?>
    </div>
    <?php foreach ($days as $i => $d): ?>
    <div class="agenda-day" id="day-<?= $d['id'] ?>" style="<?= $i ? 'display:none' : '' ?>">
      <p class="lead" style="text-align:center;margin-bottom:26px;color:var(--muted)"><?= site_esc($d['day_date']) ?></p>
      <?php if (empty($d['sessions'])): ?>
        <div class="empty" style="text-align:center;color:var(--muted);padding:30px">Sessions for this day will be announced soon.</div>
      <?php else: ?>
      <div class="timeline">
        <?php foreach ($d['sessions'] as $s): ?>
        <div class="t-row rv">
          <div class="t-time"><?= site_esc($s['start_time']) ?><?php if ($s['end_time']): ?><span>– <?= site_esc($s['end_time']) ?></span><?php endif; ?></div>
          <div class="t-body">
            <?php if ($s['tag']): ?><span class="badge b-gold" style="margin-bottom:8px"><?= site_esc($s['tag']) ?></span><?php endif; ?>
            <h3><?= site_esc($s['title']) ?></h3>
            <?php if ($s['speaker']): ?><div class="t-spk"><?= site_esc($s['speaker']) ?></div><?php endif; ?>
            <?php if ($s['description']): ?><p><?= site_esc($s['description']) ?></p><?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<style>
.timeline{display:grid;gap:14px;max-width:860px;margin:0 auto}
.t-row{display:grid;grid-template-columns:110px 1fr;gap:18px;background:var(--surface);border:1px solid var(--line);border-radius:var(--radius);padding:20px 22px}
.t-time{font-family:var(--font-display);color:var(--gold-hi);font-size:1.05rem}
.t-time span{display:block;font-size:.8rem;color:var(--muted);font-family:var(--font-body)}
.t-body h3{font-size:1.15rem;margin-bottom:6px}
.t-spk{color:var(--gold-hi);font-size:13px;letter-spacing:.1em;text-transform:uppercase;margin-bottom:8px}
.t-body p{color:var(--muted);font-size:14.5px}
@media(max-width:600px){.t-row{grid-template-columns:1fr;gap:8px}}
</style>
<script>
document.querySelectorAll('[data-day]').forEach(function(t){
  t.addEventListener('click', function(e){
    e.preventDefault();
    document.querySelectorAll('[data-day]').forEach(function(x){x.classList.remove('active')});
    t.classList.add('active');
    document.querySelectorAll('.agenda-day').forEach(function(d){d.style.display='none'});
    document.getElementById('day-'+t.getAttribute('data-day')).style.display='block';
  });
});
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
