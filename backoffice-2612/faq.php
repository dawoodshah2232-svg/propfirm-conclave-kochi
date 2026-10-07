<?php
// FAQs: add / edit / reorder / delete.
require_once __DIR__ . '/_layout.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order'])) {
    header('Content-Type: application/json');
    try {
        csrf_check();
        $order = json_decode($_POST['order'], true) ?: [];
        $st = db()->prepare('UPDATE faqs SET sort = ? WHERE id = ?');
        foreach ($order as $i => $id) $st->execute([$i, (int)$id]);
        echo json_encode(['ok' => true]);
    } catch (Throwable $e) { echo json_encode(['ok' => false]); }
    exit;
}
if (isset($_GET['delete'])) {
    csrf_check_get();
    db()->prepare('DELETE FROM faqs WHERE id = ?')->execute([(int)$_GET['delete']]);
    flash('FAQ deleted.');
    header('Location: ' . ADMIN_BASE . '/faq.php'); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['question'])) {
    csrf_check();
    if (!empty($_POST['id'])) {
        db()->prepare('UPDATE faqs SET question=?, answer=? WHERE id=?')
           ->execute([$_POST['question'], $_POST['answer'], (int)$_POST['id']]);
        flash('FAQ updated.');
    } else {
        $max = (int)qv('SELECT COALESCE(MAX(sort),-1) FROM faqs') + 1;
        db()->prepare('INSERT INTO faqs (question, answer, sort) VALUES (?,?,?)')
           ->execute([$_POST['question'], $_POST['answer'], $max]);
        flash('FAQ added.');
    }
    header('Location: ' . ADMIN_BASE . '/faq.php'); exit;
}

$edit = isset($_GET['edit']) ? q1('SELECT * FROM faqs WHERE id=?', [(int)$_GET['edit']]) : null;
$list = q('SELECT * FROM faqs ORDER BY sort');
layout_head('FAQs');
?>
<div class="toolbar"><div class="left"><span style="color:var(--muted);font-size:13.5px">Drag to reorder — this order shows on the FAQ page.</span></div>
  <a class="btn btn-gold" href="<?= ADMIN_BASE ?>/faq.php?edit=new">+ Add FAQ</a></div>

<?php if ($edit || isset($_GET['edit'])): $e = $edit ?: ['id'=>'','question'=>'','answer'=>'']; ?>
<div class="panel"><h2><?= $edit ? 'Edit' : 'Add' ?> FAQ</h2>
  <form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <input type="hidden" name="id" value="<?= esc($e['id']) ?>">
    <div class="field" style="margin-bottom:16px"><label>Question</label><input name="question" required value="<?= esc($e['question']) ?>"></div>
    <div class="field"><label>Answer</label><textarea name="answer" required><?= esc($e['answer']) ?></textarea></div>
    <div style="margin-top:20px;display:flex;gap:10px">
      <button class="btn btn-gold" type="submit">Save</button>
      <a class="btn" href="<?= ADMIN_BASE ?>/faq.php">Cancel</a>
    </div></form></div>
<?php endif; ?>

<div class="panel"><h2>FAQs (<?= count($list) ?>)</h2>
<?php if (!$list): ?><div class="empty">No FAQs yet.</div><?php else: ?>
<input type="hidden" name="csrf" value="<?= csrf_token() ?>">
<div class="drag-list" data-sortable data-save-url="<?= ADMIN_BASE ?>/faq.php">
  <?php foreach ($list as $f): ?>
  <div class="drag-item" data-id="<?= $f['id'] ?>">
    <span class="grip">⋮⋮</span>
    <div class="grow"><b><?= esc($f['question']) ?></b><small><?= esc(mb_substr($f['answer'], 0, 90)) ?>…</small></div>
    <a class="btn btn-sm" href="<?= ADMIN_BASE ?>/faq.php?edit=<?= $f['id'] ?>">Edit</a>
    <a class="btn btn-sm btn-danger" data-confirm="Delete this FAQ?" href="<?= ADMIN_BASE ?>/faq.php?delete=<?= $f['id'] ?>&csrf=<?= csrf_token() ?>">Delete</a>
  </div>
  <?php endforeach; ?>
</div><?php endif; ?></div>
<?php layout_foot(); ?>
