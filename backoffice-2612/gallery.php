<?php
// Gallery: add / remove / reorder images shown on the website gallery.
require_once __DIR__ . '/_layout.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order'])) {
    header('Content-Type: application/json');
    try {
        csrf_check();
        $order = json_decode($_POST['order'], true) ?: [];
        $st = db()->prepare('UPDATE gallery SET sort = ? WHERE id = ?');
        foreach ($order as $i => $id) $st->execute([$i, (int)$id]);
        echo json_encode(['ok' => true]);
    } catch (Throwable $e) { echo json_encode(['ok' => false]); }
    exit;
}
if (isset($_GET['delete'])) {
    csrf_check_get();
    db()->prepare('DELETE FROM gallery WHERE id = ?')->execute([(int)$_GET['delete']]);
    flash('Image removed from gallery.');
    header('Location: ' . ADMIN_BASE . '/gallery.php'); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['images'])) {
    csrf_check();
    $added = 0;
    try {
        $max = (int)qv('SELECT COALESCE(MAX(sort),-1) FROM gallery') + 1;
        foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
            $file = ['tmp_name'=>$tmp,'name'=>$_FILES['images']['name'][$i],'size'=>$_FILES['images']['size'][$i],
                     'error'=>$_FILES['images']['error'][$i],'type'=>$_FILES['images']['type'][$i]];
            $name = upload_image($file, 'gallery');
            if ($name === '') continue;
            db()->prepare('INSERT INTO gallery (title, image, sort) VALUES (?,?,?)')
               ->execute([pathinfo($_FILES['images']['name'][$i], PATHINFO_FILENAME), $name, $max++]);
            $added++;
        }
        flash($added ? "$added image(s) added to gallery." : 'No images were uploaded.');
    } catch (Throwable $e) { flash($e->getMessage(), 'err'); }
    header('Location: ' . ADMIN_BASE . '/gallery.php'); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    csrf_check();
    db()->prepare('UPDATE gallery SET title = ? WHERE id = ?')->execute([$_POST['title'], (int)$_POST['id']]);
    flash('Caption updated.');
    header('Location: ' . ADMIN_BASE . '/gallery.php'); exit;
}

$list = q('SELECT * FROM gallery ORDER BY sort, id');
layout_head('Gallery');
?>
<div class="panel">
  <h2>Add images</h2>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <div class="field">
      <label>Select images (you can pick several at once)</label>
      <input type="file" name="images[]" accept="image/*" multiple required>
      <div class="hint">JPG / PNG / WebP, max 5 MB each. They appear on the website gallery in the order below.</div>
    </div>
    <div style="margin-top:16px"><button class="btn btn-gold" type="submit">Upload to Gallery</button></div>
  </form>
</div>

<div class="panel">
  <h2>Gallery images (<?= count($list) ?>) <span style="color:var(--muted);font-size:12.5px;font-weight:400">— drag to reorder</span></h2>
  <?php if (!$list): ?><div class="empty">Gallery is empty — upload the first images above.</div>
  <?php else: ?>
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <div class="drag-list" data-sortable data-save-url="<?= ADMIN_BASE ?>/gallery.php">
    <?php foreach ($list as $g): ?>
    <div class="drag-item" data-id="<?= $g['id'] ?>">
      <span class="grip">⋮⋮</span>
      <img src="../<?= esc(upload_url($g['image'])) ?>" style="width:90px;height:60px;object-fit:cover;padding:0" alt="">
      <div class="grow">
        <form method="post" style="display:flex;gap:8px;align-items:center">
          <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
          <input type="hidden" name="id" value="<?= $g['id'] ?>">
          <input name="title" value="<?= esc($g['title']) ?>" placeholder="Caption…" style="background:rgba(255,255,255,.045);border:1px solid var(--line);border-radius:8px;padding:8px 12px;color:var(--text);font-size:13.5px;width:100%;max-width:320px">
          <button class="btn btn-sm" type="submit">Save</button>
        </form>
      </div>
      <a class="btn btn-sm btn-danger" data-confirm="Remove this image from the gallery?" href="<?= ADMIN_BASE ?>/gallery.php?delete=<?= $g['id'] ?>&csrf=<?= csrf_token() ?>">Remove</a>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
<?php layout_foot(); ?>
