<?php
// Agenda: days + sessions.
require_once __DIR__ . '/_layout.php';
require_login();

$day_id = (int)($_GET['day'] ?? 0);
if (!$day_id) $day_id = (int)qv('SELECT id FROM agenda_days ORDER BY sort LIMIT 1');

if (isset($_GET['del_session'])) {
    csrf_check_get();
    db()->prepare('DELETE FROM agenda_sessions WHERE id=?')->execute([(int)$_GET['del_session']]);
    flash('Session deleted.');
    header("Location: " . ADMIN_BASE . "/agenda.php?day=$day_id"); exit;
}
if (isset($_GET['del_day'])) {
    csrf_check_get();
    db()->prepare('DELETE FROM agenda_days WHERE id=?')->execute([(int)$_GET['del_day']]);
    flash('Day deleted with its sessions.');
    header('Location: ' . ADMIN_BASE . '/agenda.php'); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['day_label'])) {
    csrf_check();
    $max = (int)qv('SELECT COALESCE(MAX(sort),-1) FROM agenda_days') + 1;
    db()->prepare('INSERT INTO agenda_days (day_label, day_date, sort) VALUES (?,?,?)')
       ->execute([$_POST['day_label'], $_POST['day_date'], $max]);
    flash('Day added.');
    header('Location: ' . ADMIN_BASE . '/agenda.php'); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    csrf_check();
    $data = [$day_id, $_POST['start_time'], $_POST['end_time'], $_POST['title'], $_POST['speaker'], $_POST['description'], $_POST['tag']];
    if (!empty($_POST['id'])) {
        $data[] = (int)$_POST['id'];
        db()->prepare('UPDATE agenda_sessions SET day_id=?, start_time=?, end_time=?, title=?, speaker=?, description=?, tag=? WHERE id=?')->execute($data);
        flash('Session updated.');
    } else {
        $max = (int)qv('SELECT COALESCE(MAX(sort),-1) FROM agenda_sessions WHERE day_id=?', [$day_id]) + 1;
        $data[] = $max;
        db()->prepare('INSERT INTO agenda_sessions (day_id, start_time, end_time, title, speaker, description, tag, sort) VALUES (?,?,?,?,?,?,?,?)')->execute($data);
        flash('Session added.');
    }
    header("Location: " . ADMIN_BASE . "/agenda.php?day=$day_id"); exit;
}

$days = q('SELECT * FROM agenda_days ORDER BY sort');
$edit = isset($_GET['edit']) ? q1('SELECT * FROM agenda_sessions WHERE id=?', [(int)$_GET['edit']]) : null;
$sessions = $day_id ? q('SELECT * FROM agenda_sessions WHERE day_id=? ORDER BY sort', [$day_id]) : [];
layout_head('Agenda');
?>
<div class="toolbar">
  <div class="left"><div class="tabs" style="margin:0">
    <?php foreach ($days as $d): ?>
    <a class="tab <?= $d['id']==$day_id?'active':'' ?>" href="<?= ADMIN_BASE ?>/agenda.php?day=<?= $d['id'] ?>"><?= esc($d['day_label']) ?></a>
    <?php endforeach; ?>
  </div></div>
  <form method="post" style="display:flex;gap:8px">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <input name="day_label" placeholder="Day label" required style="background:rgba(255,255,255,.045);border:1px solid var(--line);border-radius:99px;padding:8px 16px;color:var(--text)">
    <input name="day_date" placeholder="Date" style="background:rgba(255,255,255,.045);border:1px solid var(--line);border-radius:99px;padding:8px 16px;color:var(--text)">
    <button class="btn btn-sm btn-gold" type="submit">+ Day</button>
  </form>
</div>

<?php if ($day_id && ($edit || isset($_GET['edit']))): $e = $edit ?: ['id'=>'','start_time'=>'','end_time'=>'','title'=>'','speaker'=>'','description'=>'','tag'=>'']; ?>
<div class="panel"><h2><?= $edit ? 'Edit' : 'Add' ?> Session</h2>
  <form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <input type="hidden" name="id" value="<?= esc($e['id']) ?>">
    <div class="form-grid">
      <div class="field"><label>Start</label><input name="start_time" placeholder="09:30" value="<?= esc($e['start_time']) ?>"></div>
      <div class="field"><label>End</label><input name="end_time" placeholder="10:15" value="<?= esc($e['end_time']) ?>"></div>
      <div class="field full"><label>Title</label><input name="title" required value="<?= esc($e['title']) ?>"></div>
      <div class="field"><label>Speaker</label><input name="speaker" value="<?= esc($e['speaker']) ?>"></div>
      <div class="field"><label>Tag</label><input name="tag" placeholder="e.g. Keynote, Panel" value="<?= esc($e['tag']) ?>"></div>
      <div class="field full"><label>Description</label><textarea name="description"><?= esc($e['description']) ?></textarea></div>
    </div>
    <div style="margin-top:20px;display:flex;gap:10px">
      <button class="btn btn-gold" type="submit">Save Session</button>
      <a class="btn" href="<?= ADMIN_BASE ?>/agenda.php?day=<?= $day_id ?>">Cancel</a>
    </div></form></div>
<?php endif; ?>

<?php if ($day_id): ?>
<div class="panel">
  <h2>Sessions <a class="btn btn-sm btn-gold" href="<?= ADMIN_BASE ?>/agenda.php?day=<?= $day_id ?>&edit=new">+ Add Session</a></h2>
  <?php if (!$sessions): ?><div class="empty">No sessions for this day yet.</div><?php else: ?>
  <table class="tbl"><tr><th>Time</th><th>Session</th><th>Tag</th><th></th></tr>
  <?php foreach ($sessions as $s): ?>
  <tr>
    <td style="white-space:nowrap"><b><?= esc($s['start_time']) ?></b> <span style="color:var(--muted)">– <?= esc($s['end_time']) ?></span></td>
    <td><b><?= esc($s['title']) ?></b><br><small style="color:var(--muted)"><?= esc($s['speaker']) ?></small></td>
    <td><?= $s['tag'] ? '<span class="badge b-gold">'.esc($s['tag']).'</span>' : '—' ?></td>
    <td style="white-space:nowrap">
      <a class="btn btn-sm" href="<?= ADMIN_BASE ?>/agenda.php?day=<?= $day_id ?>&edit=<?= $s['id'] ?>">Edit</a>
      <a class="btn btn-sm btn-danger" data-confirm="Delete this session?" href="<?= ADMIN_BASE ?>/agenda.php?day=<?= $day_id ?>&del_session=<?= $s['id'] ?>&csrf=<?= csrf_token() ?>">Delete</a>
    </td>
  </tr>
  <?php endforeach; ?></table><?php endif; ?>
  <div style="margin-top:16px">
    <a class="btn btn-sm btn-danger" data-confirm="Delete this day and all its sessions?" href="<?= ADMIN_BASE ?>/agenda.php?del_day=<?= $day_id ?>&csrf=<?= csrf_token() ?>">Delete this day</a>
  </div>
</div>
<?php endif; ?>
<?php layout_foot(); ?>
