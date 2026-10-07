<?php
// Page Content Editor: change banners, headings, text and images per page.
// Left: editable blocks. Right: live preview of how the page looks.
require_once __DIR__ . '/_layout.php';
require_login();

$pages = [
  'home'     => ['Homepage', 'index.php'],
  'about'    => ['About', 'about.php'],
  'speakers' => ['Speakers', 'speakers.php'],
  'agenda'   => ['Agenda', 'agenda.php'],
  'tickets'  => ['Tickets', 'tickets.php'],
  'sponsors' => ['Sponsors', 'sponsors.php'],
  'venue'    => ['Venue', 'venue.php'],
  'faq'      => ['FAQ', 'faq.php'],
  'contact'  => ['Contact', 'contact.php'],
];
$active = $_GET['page'] ?? 'home';
if (!isset($pages[$active])) $active = 'home';
[$label, $file] = $pages[$active];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_blocks'])) {
    csrf_check();
    try {
        foreach ($_POST['blocks'] ?? [] as $id => $b) {
            $id = (int)$id;
            $row = q1('SELECT * FROM content_blocks WHERE id=?', [$id]);
            if (!$row) continue;
            $val = $b['value'] ?? $row['fvalue'];
            if ($row['ftype'] === 'image' && isset($_FILES['img_' . $id]) && $_FILES['img_' . $id]['error'] === UPLOAD_ERR_OK) {
                $up = upload_image($_FILES['img_' . $id], 'page');
                if ($up !== '') $val = UPLOAD_URL . '/' . $up;
            }
            db()->prepare('UPDATE content_blocks SET fvalue=? WHERE id=?')->execute([$val, $id]);
        }
        // add brand-new block
        if (!empty($_POST['new_section']) && !empty($_POST['new_field'])) {
            db()->prepare('INSERT INTO content_blocks (page_slug, section, field, ftype, fvalue, sort)
                           VALUES (?,?,?,?,?,?)')
               ->execute([$active, $_POST['new_section'], $_POST['new_field'], $_POST['new_ftype'], $_POST['new_value'] ?? '',
                          (int)qv('SELECT COALESCE(MAX(sort),-1) FROM content_blocks WHERE page_slug=?', [$active]) + 1]);
        }
        flash('Page content saved — the website now shows the new version.');
    } catch (Throwable $e) { flash($e->getMessage(), 'err'); }
    header('Location: ' . ADMIN_BASE . '/pages.php?page=' . $active); exit;
}
if (isset($_GET['del_block'])) {
    csrf_check_get();
    db()->prepare('DELETE FROM content_blocks WHERE id=? AND page_slug=?')->execute([(int)$_GET['del_block'], $active]);
    flash('Block deleted.');
    header('Location: ' . ADMIN_BASE . '/pages.php?page=' . $active); exit;
}

$blocks = q('SELECT * FROM content_blocks WHERE page_slug=? ORDER BY section, sort', [$active]);
$grouped = [];
foreach ($blocks as $b) $grouped[$b['section']][] = $b;
layout_head('Pages & Banners');
?>
<div class="toolbar"><div class="left"><div class="tabs" style="margin:0">
  <?php foreach ($pages as $slug => [$l]): ?>
  <a class="tab <?= $slug===$active?'active':'' ?>" href="<?= ADMIN_BASE ?>/pages.php?page=<?= $slug ?>"><?= esc($l) ?></a>
  <?php endforeach; ?>
</div></div></div>

<div class="split2" style="grid-template-columns:1fr;gap:22px">
<?php if (true): ?>
<div style="display:grid;grid-template-columns:minmax(340px,460px) 1fr;gap:22px;align-items:start">
<div>
<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <input type="hidden" name="save_blocks" value="1">
  <?php if (!$blocks): ?><div class="panel"><div class="empty">No editable blocks for this page yet — add the first one below.</div></div>
  <?php else: foreach ($grouped as $section => $items): ?>
  <div class="panel">
    <h2 style="text-transform:capitalize"><?= esc(str_replace('_',' ', $section)) ?></h2>
    <?php foreach ($items as $b): ?>
    <div class="field" style="margin-bottom:18px">
      <label><?= esc(str_replace('_',' ', $b['field'])) ?> <small style="text-transform:none;letter-spacing:0">(<?= esc($b['ftype']) ?>)</small></label>
      <?php if ($b['ftype'] === 'text'): ?>
        <input name="blocks[<?= $b['id'] ?>][value]" value="<?= esc($b['fvalue']) ?>">
      <?php elseif ($b['ftype'] === 'textarea' || $b['ftype'] === 'html'): ?>
        <textarea name="blocks[<?= $b['id'] ?>][value]" style="<?= $b['ftype']==='html' ? 'font-family:monospace;font-size:13px;min-height:160px' : '' ?>"><?= esc($b['fvalue']) ?></textarea>
      <?php elseif ($b['ftype'] === 'image'): ?>
        <?php if ($b['fvalue']): ?><img class="preview-img" style="margin:0 0 10px;max-height:90px" src="../<?= esc($b['fvalue']) ?>"><?php endif; ?>
        <input type="file" name="img_<?= $b['id'] ?>" accept="image/*">
        <div class="hint">Current: <?= esc($b['fvalue'] ?: '—') ?></div>
      <?php endif; ?>
      <div style="margin-top:6px"><a style="font-size:12px;color:var(--red)" data-confirm="Delete this block?" href="<?= ADMIN_BASE ?>/pages.php?page=<?= $active ?>&del_block=<?= $b['id'] ?>&csrf=<?= csrf_token() ?>">delete block</a></div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endforeach; endif; ?>

  <div class="panel"><h2>Add new block</h2>
    <div class="form-grid">
      <div class="field"><label>Section</label><input name="new_section" placeholder="e.g. hero"></div>
      <div class="field"><label>Field</label><input name="new_field" placeholder="e.g. headline"></div>
      <div class="field"><label>Type</label><select name="new_ftype">
        <option value="text">Text</option><option value="textarea">Textarea</option>
        <option value="html">HTML</option><option value="image">Image</option></select></div>
      <div class="field"><label>Value</label><input name="new_value" placeholder="optional"></div>
    </div>
  </div>

  <div style="position:sticky;bottom:20px;background:var(--surface);border:1px solid rgba(201,162,75,.4);border-radius:14px;padding:16px;display:flex;gap:10px;align-items:center">
    <button class="btn btn-gold" type="submit" style="flex:1;justify-content:center">Save All Changes</button>
  </div>
</form>
</div>

<div>
  <div class="panel">
    <h2>Live preview — <?= esc($label) ?>
      <a class="btn btn-sm" href="/<?= $file ?>" target="_blank">Open full page</a></h2>
    <div class="preview-frame"><iframe src="/<?= $file ?>" title="preview"></iframe></div>
    <div class="hint" style="margin-top:10px">This is exactly how the page looks on the website right now. Edit on the left, save, and the preview updates.</div>
  </div>
</div>
</div>
<?php endif; ?>
</div>
<?php layout_foot(); ?>
