<?php
// Media library: every uploaded image in one place.
require_once __DIR__ . '/_layout.php';
require_login();

$files = [];
$dir = UPLOAD_DIR;
if (is_dir($dir)) {
    foreach (scandir($dir) as $f) {
        if ($f[0] === '.') continue;
        $p = $dir . '/' . $f;
        if (is_file($p)) $files[] = ['name' => $f, 'size' => filesize($p), 'mtime' => filemtime($p)];
    }
    usort($files, fn($a, $b) => $b['mtime'] <=> $a['mtime']);
}
layout_head('Media Library');
?>
<div class="panel">
  <h2><?= count($files) ?> files</h2>
  <?php if (!$files): ?><div class="empty">Nothing uploaded yet. Images added via Speakers, Sponsors or Gallery appear here.</div>
  <?php else: ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:14px">
    <?php foreach ($files as $f): ?>
    <div style="background:rgba(255,255,255,.03);border:1px solid var(--line);border-radius:12px;overflow:hidden">
      <img src="../<?= UPLOAD_URL ?>/<?= esc($f['name']) ?>" style="width:100%;height:110px;object-fit:cover;display:block" loading="lazy">
      <div style="padding:10px 12px">
        <div style="font-size:12px;word-break:break-all;color:var(--muted)"><?= esc($f['name']) ?></div>
        <div style="font-size:11.5px;color:var(--muted);margin-top:4px"><?= round($f['size']/1024) ?> KB · <?= date('d M Y', $f['mtime']) ?></div>
        <button class="btn btn-sm" style="margin-top:8px" onclick="navigator.clipboard.writeText('<?= UPLOAD_URL ?>/<?= esc($f['name']) ?>');this.textContent='Copied!'">Copy path</button>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
<?php layout_foot(); ?>
