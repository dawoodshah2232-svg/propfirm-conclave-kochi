<?php
// Admin users: super admin only.
require_once __DIR__ . '/_layout.php';
require_role('super');

if (isset($_GET['delete'])) {
    csrf_check_get();
    if ((int)$_GET['delete'] === (int)current_admin()['id']) flash('You cannot delete your own account.', 'err');
    else { db()->prepare('DELETE FROM admin_users WHERE id=?')->execute([(int)$_GET['delete']]); flash('Admin deleted.'); }
    header('Location: ' . ADMIN_BASE . '/users.php'); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (!empty($_POST['id'])) {
        $data = [$_POST['name'], $_POST['email'], $_POST['role'], (int)$_POST['id']];
        db()->prepare('UPDATE admin_users SET name=?, email=?, role=? WHERE id=?')->execute($data);
        if (!empty($_POST['password'])) {
            db()->prepare('UPDATE admin_users SET pass_hash=? WHERE id=?')
               ->execute([password_hash($_POST['password'], PASSWORD_DEFAULT), (int)$_POST['id']]);
        }
        flash('Admin updated.');
    } else {
        if (empty($_POST['password'])) flash('Password is required for a new admin.', 'err');
        else {
            db()->prepare('INSERT INTO admin_users (name, email, pass_hash, role) VALUES (?,?,?,?)')
               ->execute([$_POST['name'], $_POST['email'], password_hash($_POST['password'], PASSWORD_DEFAULT), $_POST['role']]);
            flash('Admin added.');
        }
    }
    header('Location: ' . ADMIN_BASE . '/users.php'); exit;
}

$edit = isset($_GET['edit']) ? q1('SELECT * FROM admin_users WHERE id=?', [(int)$_GET['edit']]) : null;
$list = q('SELECT id, name, email, role, last_login, created_at FROM admin_users ORDER BY created_at');
layout_head('Admin Users');
?>
<div class="toolbar"><div class="left"></div>
  <a class="btn btn-gold" href="<?= ADMIN_BASE ?>/users.php?edit=new">+ Add Admin</a></div>

<?php if ($edit || isset($_GET['edit'])): $e = $edit ?: ['id'=>'','name'=>'','email'=>'','role'=>'editor']; ?>
<div class="panel"><h2><?= $edit ? 'Edit Admin' : 'Add Admin' ?></h2>
  <form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <input type="hidden" name="id" value="<?= esc($e['id']) ?>">
    <div class="form-grid">
      <div class="field"><label>Name</label><input name="name" required value="<?= esc($e['name']) ?>"></div>
      <div class="field"><label>Email (login id)</label><input name="email" type="email" required value="<?= esc($e['email']) ?>"></div>
      <div class="field"><label>Password <?= $edit ? '(leave blank to keep)' : '' ?></label><input name="password" type="password" <?= $edit ? '' : 'required' ?>></div>
      <div class="field"><label>Role</label><select name="role">
        <option value="editor" <?= $e['role']==='editor'?'selected':'' ?>>Editor — everything except users</option>
        <option value="super" <?= $e['role']==='super'?'selected':'' ?>>Super admin — full access</option></select></div>
    </div>
    <div style="margin-top:20px;display:flex;gap:10px">
      <button class="btn btn-gold" type="submit">Save</button>
      <a class="btn" href="<?= ADMIN_BASE ?>/users.php">Cancel</a>
    </div></form></div>
<?php endif; ?>

<div class="panel"><h2>Admins (<?= count($list) ?>)</h2>
<table class="tbl"><tr><th>Name</th><th>Email</th><th>Role</th><th>Last login</th><th></th></tr>
<?php foreach ($list as $u): ?>
<tr>
  <td><b><?= esc($u['name']) ?></b></td><td><?= esc($u['email']) ?></td>
  <td><span class="badge <?= $u['role']==='super'?'b-gold':'b-grey' ?>"><?= esc($u['role']) ?></span></td>
  <td><small style="color:var(--muted)"><?= $u['last_login'] ? date('d M Y, H:i', strtotime($u['last_login'])) : 'never' ?></small></td>
  <td style="white-space:nowrap">
    <a class="btn btn-sm" href="<?= ADMIN_BASE ?>/users.php?edit=<?= $u['id'] ?>">Edit</a>
    <?php if ((int)$u['id'] !== (int)current_admin()['id']): ?>
    <a class="btn btn-sm btn-danger" data-confirm="Delete this admin?" href="<?= ADMIN_BASE ?>/users.php?delete=<?= $u['id'] ?>&csrf=<?= csrf_token() ?>">Delete</a>
    <?php endif; ?>
  </td>
</tr>
<?php endforeach; ?></table></div>
<?php layout_foot(); ?>
