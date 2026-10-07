<?php
require_once __DIR__ . '/_layout.php';
require_login();

// ---- save drag-drop order ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order'])) {
    header('Content-Type: application/json');
    try {
        csrf_check();
        $order = json_decode($_POST['order'], true) ?: [];
        $st = db()->prepare('UPDATE sponsors SET sort = ? WHERE id = ?');
        foreach ($order as $i => $id) $st->execute([$i, (int)$id]);
        echo json_encode(['ok' => true]);
    } catch (Throwable $e) { echo json_encode(['ok' => false]); }
    exit;
}

if (isset($_GET['delete'])) {
    csrf_check_get();
    db()->prepare('DELETE FROM sponsors WHERE id = ?')->execute([(int)$_GET['delete']]);
    flash('Sponsor deleted.');
    header('Location: ' . ADMIN_BASE . '/sponsors.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    csrf_check();
    try {
        $logo = $_POST['existing_logo'] ?? '';
        $up = upload_image($_FILES['logo'] ?? [], 'sponsor');
        if ($up !== '') $logo = $up;
        $tier = (int)($_POST['tier_id'] ?? 0) ?: null;
        $data = [$tier, $_POST['name'], $logo, $_POST['website'], $_POST['status']];
        if (!empty($_POST['id'])) {
            $data[] = (int)$_POST['id'];
            db()->prepare('UPDATE sponsors SET tier_id=?, name=?, logo=?, website=?, status=? WHERE id=?')->execute($data);
            flash('Sponsor updated.');
        } else {
            $max = (int)qv('SELECT COALESCE(MAX(sort),-1) FROM sponsors') + 1;
            $data[] = $max;
            db()->prepare('INSERT INTO sponsors (tier_id, name, logo, website, status, sort) VALUES (?,?,?,?,?,?)')->execute($data);
            flash('Sponsor added.');
        }
    } catch (Throwable $e) { flash($e->getMessage(), 'err'); }
    header('Location: ' . ADMIN_BASE . '/sponsors.php'); exit;
}

$tiers = q('SELECT * FROM sponsor_tiers ORDER BY sort');
$edit = isset($_GET['edit']) ? q1('SELECT * FROM sponsors WHERE id=?', [(int)$_GET['edit']]) : null;
$list = q('SELECT s.*, t.name AS tier_name, t.color AS tier_color FROM sponsors s
           LEFT JOIN sponsor_tiers t ON t.id = s.tier_id ORDER BY s.sort, s.id');
layout_head('Sponsors');
?>
<div class="toolbar">
  <div class="left">
    <span style="color:var(--muted);font-size:13.5px">Drag logos to reorder — this exact order shows on the website.</span>
    <a class="btn btn-sm" href="<?= ADMIN_BASE ?>/sponsor_tiers.php">Manage Tiers</a>
  </div>
  <a class="btn btn-gold" href="<?= ADMIN_BASE ?>/sponsors.php?edit=new">+ Add Sponsor</a>
</div>

<?php if ($edit || isset($_GET['edit'])): $e = $edit ?: ['id'=>'','tier_id'=>'','name'=>'','logo'=>'','website'=>'','status'=>'published']; ?>
<div class="panel">
  <h2><?= $edit ? 'Edit Sponsor' : 'Add Sponsor' ?></h2>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <input type="hidden" name="id" value="<?= esc($e['id']) ?>">
    <input type="hidden" name="existing_logo" value="<?= esc($e['logo']) ?>">
    <div class="form-grid">
      <div class="field"><label>Sponsor name</label><input name="name" required value="<?= esc($e['name']) ?>"></div>
      <div class="field"><label>Tier / Category</label>
        <select name="tier_id">
          <option value="0">— No tier —</option>
          <?php foreach ($tiers as $t): ?>
          <option value="<?= $t['id'] ?>" <?= (string)$e['tier_id']===(string)$t['id']?'selected':'' ?>><?= esc($t['name']) ?></option>
          <?php endforeach; ?>
        </select></div>
      <div class="field"><label>Website</label><input name="website" placeholder="https://…" value="<?= esc($e['website']) ?>"></div>
      <div class="field"><label>Status</label>
        <select name="status">
          <option value="published" <?= $e['status']==='published'?'selected':'' ?>>Published</option>
          <option value="draft" <?= $e['status']==='draft'?'selected':'' ?>>Draft</option>
        </select></div>
      <div class="field full"><label>Logo</label>
        <input type="file" name="logo" accept="image/*" data-preview="logoPrev">
        <?php if ($e['logo']): ?><img id="logoPrev" class="preview-img" style="background:#fff;padding:8px" src="../<?= esc(upload_url($e['logo'])) ?>"><?php else: ?><img id="logoPrev" class="preview-img" style="display:none;background:#fff;padding:8px"><?php endif; ?>
        <div class="hint">PNG with transparent background looks best. Max 5 MB.</div>
      </div>
    </div>
    <div style="margin-top:20px;display:flex;gap:10px">
      <button class="btn btn-gold" type="submit">Save Sponsor</button>
      <a class="btn" href="<?= ADMIN_BASE ?>/sponsors.php">Cancel</a>
    </div>
  </form>
</div>
<?php endif; ?>

<div class="panel">
  <h2>Logo wall order (<?= count($list) ?>)</h2>
  <?php if (!$list): ?><div class="empty">No sponsors yet.</div>
  <?php else: ?>
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <div class="drag-list" data-sortable data-save-url="<?= ADMIN_BASE ?>/sponsors.php">
    <?php foreach ($list as $s): ?>
    <div class="drag-item" data-id="<?= $s['id'] ?>">
      <span class="grip">⋮⋮</span>
      <?php if ($s['logo']): ?><img src="../<?= esc(upload_url($s['logo'])) ?>" alt=""><?php endif; ?>
      <div class="grow"><b><?= esc($s['name']) ?></b>
        <small><?= $s['tier_name'] ? '<span style="color:'.esc($s['tier_color']).'">⬤</span> '.esc($s['tier_name']) : 'No tier' ?></small></div>
      <span class="badge <?= $s['status']==='published'?'b-green':'b-grey' ?>"><?= esc($s['status']) ?></span>
      <a class="btn btn-sm" href="<?= ADMIN_BASE ?>/sponsors.php?edit=<?= $s['id'] ?>">Edit</a>
      <a class="btn btn-sm btn-danger" data-confirm="Delete this sponsor?" href="<?= ADMIN_BASE ?>/sponsors.php?delete=<?= $s['id'] ?>&csrf=<?= csrf_token() ?>">Delete</a>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
<?php layout_foot(); ?>
