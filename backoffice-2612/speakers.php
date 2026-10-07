<?php
require_once __DIR__ . '/_layout.php';
require_login();

// ---- save new sort order (drag & drop) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order'])) {
    header('Content-Type: application/json');
    try {
        csrf_check();
        $order = json_decode($_POST['order'], true) ?: [];
        $st = db()->prepare('UPDATE speakers SET sort = ? WHERE id = ?');
        foreach ($order as $i => $id) $st->execute([$i, (int)$id]);
        echo json_encode(['ok' => true]);
    } catch (Throwable $e) { echo json_encode(['ok' => false]); }
    exit;
}

// ---- delete ----
if (isset($_GET['delete'])) {
    csrf_check_get();
    db()->prepare('DELETE FROM speakers WHERE id = ?')->execute([(int)$_GET['delete']]);
    flash('Speaker deleted.');
    header('Location: ' . ADMIN_BASE . '/speakers.php'); exit;
}

// ---- save (add/edit) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    csrf_check();
    try {
        $photo = $_POST['existing_photo'] ?? '';
        $up = upload_image($_FILES['photo'] ?? [], 'speaker');
        if ($up !== '') $photo = $up;
        $data = [$_POST['name'], $_POST['role'], $_POST['company'], $_POST['bio'],
                 $photo, $_POST['status']];
        if (!empty($_POST['id'])) {
            $data[] = (int)$_POST['id'];
            db()->prepare('UPDATE speakers SET name=?, role=?, company=?, bio=?, photo=?, status=? WHERE id=?')
               ->execute($data);
            flash('Speaker updated.');
        } else {
            $max = (int)qv('SELECT COALESCE(MAX(sort),-1) FROM speakers') + 1;
            $data[] = $max;
            db()->prepare('INSERT INTO speakers (name, role, company, bio, photo, status, sort)
                           VALUES (?,?,?,?,?,?,?)')->execute($data);
            flash('Speaker added.');
        }
    } catch (Throwable $e) { flash($e->getMessage(), 'err'); }
    header('Location: ' . ADMIN_BASE . '/speakers.php'); exit;
}

$edit = isset($_GET['edit']) ? q1('SELECT * FROM speakers WHERE id=?', [(int)$_GET['edit']]) : null;
$list = q('SELECT * FROM speakers ORDER BY sort, id');
layout_head('Speakers');
?>
<div class="toolbar">
  <div class="left"><span style="color:var(--muted);font-size:13.5px">Drag cards to reorder — the order shows on the website.</span></div>
  <a class="btn btn-gold" href="<?= ADMIN_BASE ?>/speakers.php?edit=new">+ Add Speaker</a>
</div>

<?php if ($edit || isset($_GET['edit'])): $e = $edit ?: ['id'=>'','name'=>'','role'=>'','company'=>'','bio'=>'','photo'=>'','status'=>'published']; ?>
<div class="panel">
  <h2><?= $edit ? 'Edit Speaker' : 'Add Speaker' ?></h2>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <input type="hidden" name="id" value="<?= esc($e['id']) ?>">
    <input type="hidden" name="existing_photo" value="<?= esc($e['photo']) ?>">
    <div class="form-grid">
      <div class="field"><label>Name</label><input name="name" required value="<?= esc($e['name']) ?>"></div>
      <div class="field"><label>Role</label><input name="role" placeholder="e.g. Funded Trader" value="<?= esc($e['role']) ?>"></div>
      <div class="field"><label>Company</label><input name="company" placeholder="e.g. Apex Funding" value="<?= esc($e['company']) ?>"></div>
      <div class="field"><label>Status</label>
        <select name="status">
          <option value="published" <?= $e['status']==='published'?'selected':'' ?>>Published</option>
          <option value="draft" <?= $e['status']==='draft'?'selected':'' ?>>Draft</option>
        </select></div>
      <div class="field full"><label>Bio</label><textarea name="bio"><?= esc($e['bio']) ?></textarea></div>
      <div class="field full"><label>Photo</label>
        <input type="file" name="photo" accept="image/*" data-preview="photoPrev">
        <?php if ($e['photo']): ?><img id="photoPrev" class="preview-img" src="../<?= esc(upload_url($e['photo'])) ?>"><?php else: ?><img id="photoPrev" class="preview-img" style="display:none"><?php endif; ?>
        <div class="hint">Square headshot works best. JPG/PNG/WebP, max 5 MB. Faces must stay fully visible.</div>
      </div>
    </div>
    <div style="margin-top:20px;display:flex;gap:10px">
      <button class="btn btn-gold" type="submit">Save Speaker</button>
      <a class="btn" href="<?= ADMIN_BASE ?>/speakers.php">Cancel</a>
    </div>
  </form>
</div>
<?php endif; ?>

<div class="panel">
  <h2>Lineup (<?= count($list) ?>)</h2>
  <?php if (!$list): ?><div class="empty">No speakers yet — add the first one.</div>
  <?php else: ?>
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <div class="drag-list" data-sortable data-save-url="<?= ADMIN_BASE ?>/speakers.php">
    <?php foreach ($list as $s): ?>
    <div class="drag-item" data-id="<?= $s['id'] ?>">
      <span class="grip">⋮⋮</span>
      <?php if ($s['photo']): ?><img src="../<?= esc(upload_url($s['photo'])) ?>" style="width:52px;height:52px;object-fit:cover;padding:0"><?php endif; ?>
      <div class="grow"><b><?= esc($s['name']) ?></b><small><?= esc($s['role']) ?> · <?= esc($s['company']) ?></small></div>
      <span class="badge <?= $s['status']==='published'?'b-green':'b-grey' ?>"><?= esc($s['status']) ?></span>
      <a class="btn btn-sm" href="<?= ADMIN_BASE ?>/speakers.php?edit=<?= $s['id'] ?>">Edit</a>
      <a class="btn btn-sm btn-danger" data-confirm="Delete this speaker?" href="<?= ADMIN_BASE ?>/speakers.php?delete=<?= $s['id'] ?>&csrf=<?= csrf_token() ?>">Delete</a>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
<?php layout_foot(); ?>
