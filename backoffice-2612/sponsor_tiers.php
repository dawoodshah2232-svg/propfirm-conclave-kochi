<?php
// Sponsor tiers / categories: add, rename, recolour, reorder, delete.
require_once __DIR__ . '/_layout.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order'])) {
    header('Content-Type: application/json');
    try {
        csrf_check();
        $order = json_decode($_POST['order'], true) ?: [];
        $st = db()->prepare('UPDATE sponsor_tiers SET sort = ? WHERE id = ?');
        foreach ($order as $i => $id) $st->execute([$i, (int)$id]);
        echo json_encode(['ok' => true]);
    } catch (Throwable $e) { echo json_encode(['ok' => false]); }
    exit;
}
if (isset($_GET['delete'])) {
    csrf_check_get();
    db()->prepare('DELETE FROM sponsor_tiers WHERE id = ?')->execute([(int)$_GET['delete']]);
    flash('Tier deleted — its sponsors moved to “No tier”.');
    header('Location: ' . ADMIN_BASE . '/sponsor_tiers.php'); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    csrf_check();
    $data = [$_POST['name'], $_POST['slug'] ?: strtolower(preg_replace('/[^a-z0-9]+/i', '-', $_POST['name'])), $_POST['color']];
    if (!empty($_POST['id'])) {
        $data[] = (int)$_POST['id'];
        db()->prepare('UPDATE sponsor_tiers SET name=?, slug=?, color=? WHERE id=?')->execute($data);
        flash('Tier updated.');
    } else {
        $max = (int)qv('SELECT COALESCE(MAX(sort),-1) FROM sponsor_tiers') + 1;
        $data[] = $max;
        db()->prepare('INSERT INTO sponsor_tiers (name, slug, color, sort) VALUES (?,?,?,?)')->execute($data);
        flash('Tier added.');
    }
    header('Location: ' . ADMIN_BASE . '/sponsor_tiers.php'); exit;
}

$tiers = q('SELECT t.*, (SELECT COUNT(*) FROM sponsors s WHERE s.tier_id=t.id) AS n FROM sponsor_tiers t ORDER BY sort');
$edit = isset($_GET['edit']) ? q1('SELECT * FROM sponsor_tiers WHERE id=?', [(int)$_GET['edit']]) : null;
layout_head('Sponsor Tiers');
?>
<div class="toolbar">
  <div class="left"><span style="color:var(--muted);font-size:13.5px">Tiers group the logo wall — top tier shows first. Drag to reorder.</span></div>
  <a class="btn btn-gold" href="<?= ADMIN_BASE ?>/sponsor_tiers.php?edit=new">+ Add Tier</a>
</div>

<?php if ($edit || isset($_GET['edit'])): $e = $edit ?: ['id'=>'','name'=>'','slug'=>'','color'=>'#c9a24b']; ?>
<div class="panel">
  <h2><?= $edit ? 'Edit Tier' : 'Add Tier' ?></h2>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <input type="hidden" name="id" value="<?= esc($e['id']) ?>">
    <div class="form-grid">
      <div class="field"><label>Tier name</label><input name="name" required placeholder="e.g. Diamond" value="<?= esc($e['name']) ?>"></div>
      <div class="field"><label>Slug</label><input name="slug" placeholder="auto from name" value="<?= esc($e['slug']) ?>"></div>
      <div class="field"><label>Accent colour</label><input type="color" name="color" value="<?= esc($e['color']) ?>" style="height:46px;padding:6px"></div>
    </div>
    <div style="margin-top:20px;display:flex;gap:10px">
      <button class="btn btn-gold" type="submit">Save Tier</button>
      <a class="btn" href="<?= ADMIN_BASE ?>/sponsor_tiers.php">Cancel</a>
    </div>
  </form>
</div>
<?php endif; ?>

<div class="panel">
  <h2>Tiers (<?= count($tiers) ?>)</h2>
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <div class="drag-list" data-sortable data-save-url="<?= ADMIN_BASE ?>/sponsor_tiers.php">
    <?php foreach ($tiers as $t): ?>
    <div class="drag-item" data-id="<?= $t['id'] ?>">
      <span class="grip">⋮⋮</span>
      <span style="width:16px;height:16px;border-radius:50%;background:<?= esc($t['color']) ?>;flex:none"></span>
      <div class="grow"><b><?= esc($t['name']) ?></b><small><?= (int)$t['n'] ?> sponsors</small></div>
      <a class="btn btn-sm" href="<?= ADMIN_BASE ?>/sponsor_tiers.php?edit=<?= $t['id'] ?>">Edit</a>
      <a class="btn btn-sm btn-danger" data-confirm="Delete this tier?" href="<?= ADMIN_BASE ?>/sponsor_tiers.php?delete=<?= $t['id'] ?>&csrf=<?= csrf_token() ?>">Delete</a>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php layout_foot(); ?>
