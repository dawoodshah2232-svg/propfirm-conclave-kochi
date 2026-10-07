<?php
// Blog: posts CRUD with cover upload, draft/published.
require_once __DIR__ . '/_layout.php';
require_login();

if (isset($_GET['delete'])) {
    csrf_check_get();
    db()->prepare('DELETE FROM posts WHERE id = ?')->execute([(int)$_GET['delete']]);
    flash('Post deleted.');
    header('Location: ' . ADMIN_BASE . '/blog.php'); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    csrf_check();
    try {
        $cover = $_POST['existing_cover'] ?? '';
        $up = upload_image($_FILES['cover'] ?? [], 'post');
        if ($up !== '') $cover = $up;
        $slug = trim($_POST['slug']) ?: strtolower(preg_replace('/[^a-z0-9]+/i', '-', $_POST['title']));
        $data = [$slug, $_POST['title'], $_POST['excerpt'], $cover, $_POST['body'], $_POST['status'],
                 $_POST['status'] === 'published' ? date('Y-m-d H:i:s') : null];
        if (!empty($_POST['id'])) {
            $old = q1('SELECT status FROM posts WHERE id=?', [(int)$_POST['id']]);
            if ($old && $old['status'] === 'published') $data[6] = qv('SELECT published_at FROM posts WHERE id=?', [(int)$_POST['id']]);
            $data[] = (int)$_POST['id'];
            db()->prepare('UPDATE posts SET slug=?, title=?, excerpt=?, cover=?, body=?, status=?, published_at=? WHERE id=?')->execute($data);
            flash('Post updated.');
        } else {
            db()->prepare('INSERT INTO posts (slug, title, excerpt, cover, body, status, published_at) VALUES (?,?,?,?,?,?,?)')->execute($data);
            flash('Post created.');
        }
    } catch (Throwable $e) { flash($e->getMessage(), 'err'); }
    header('Location: ' . ADMIN_BASE . '/blog.php'); exit;
}

$edit = isset($_GET['edit']) ? q1('SELECT * FROM posts WHERE id=?', [(int)$_GET['edit']]) : null;
$list = q('SELECT * FROM posts ORDER BY created_at DESC');
layout_head('Blog');
?>
<div class="toolbar"><div class="left"></div>
  <a class="btn btn-gold" href="<?= ADMIN_BASE ?>/blog.php?edit=new">+ New Post</a></div>

<?php if ($edit || isset($_GET['edit'])): $e = $edit ?: ['id'=>'','slug'=>'','title'=>'','excerpt'=>'','cover'=>'','body'=>'','status'=>'draft']; ?>
<div class="panel"><h2><?= $edit ? 'Edit Post' : 'New Post' ?></h2>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <input type="hidden" name="id" value="<?= esc($e['id']) ?>">
    <input type="hidden" name="existing_cover" value="<?= esc($e['cover']) ?>">
    <div class="form-grid">
      <div class="field full"><label>Title</label><input name="title" required value="<?= esc($e['title']) ?>"></div>
      <div class="field"><label>Slug</label><input name="slug" placeholder="auto from title" value="<?= esc($e['slug']) ?>"></div>
      <div class="field"><label>Status</label><select name="status">
        <option value="draft" <?= $e['status']==='draft'?'selected':'' ?>>Draft</option>
        <option value="published" <?= $e['status']==='published'?'selected':'' ?>>Published</option></select></div>
      <div class="field full"><label>Excerpt</label><input name="excerpt" maxlength="300" value="<?= esc($e['excerpt']) ?>"></div>
      <div class="field full"><label>Cover image</label>
        <input type="file" name="cover" accept="image/*" data-preview="coverPrev">
        <?php if ($e['cover']): ?><img id="coverPrev" class="preview-img" src="../<?= esc(upload_url($e['cover'])) ?>"><?php else: ?><img id="coverPrev" class="preview-img" style="display:none"><?php endif; ?>
      </div>
      <div class="field full"><label>Body (HTML allowed)</label><textarea name="body" style="min-height:280px;font-family:monospace;font-size:13px"><?= esc($e['body']) ?></textarea>
        <div class="hint">Write with &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;img&gt; tags — it renders as the article page.</div></div>
    </div>
    <div style="margin-top:20px;display:flex;gap:10px">
      <button class="btn btn-gold" type="submit">Save Post</button>
      <a class="btn" href="<?= ADMIN_BASE ?>/blog.php">Cancel</a>
    </div></form></div>
<?php endif; ?>

<div class="panel"><h2>Posts (<?= count($list) ?>)</h2>
<?php if (!$list): ?><div class="empty">No posts yet.</div><?php else: ?>
<table class="tbl"><tr><th></th><th>Title</th><th>Status</th><th>Published</th><th></th></tr>
<?php foreach ($list as $p): ?>
<tr>
  <td><?php if ($p['cover']): ?><img class="thumb" src="../<?= esc(upload_url($p['cover'])) ?>"><?php endif; ?></td>
  <td><b><?= esc($p['title']) ?></b><br><small style="color:var(--muted)">/blog/<?= esc($p['slug']) ?>.html</small></td>
  <td><span class="badge <?= $p['status']==='published'?'b-green':'b-grey' ?>"><?= esc($p['status']) ?></span></td>
  <td><small style="color:var(--muted)"><?= $p['published_at'] ? date('d M Y', strtotime($p['published_at'])) : '—' ?></small></td>
  <td style="white-space:nowrap">
    <a class="btn btn-sm" href="<?= ADMIN_BASE ?>/blog.php?edit=<?= $p['id'] ?>">Edit</a>
    <a class="btn btn-sm btn-danger" data-confirm="Delete this post?" href="<?= ADMIN_BASE ?>/blog.php?delete=<?= $p['id'] ?>&csrf=<?= csrf_token() ?>">Delete</a>
  </td>
</tr>
<?php endforeach; ?></table><?php endif; ?></div>
<?php layout_foot(); ?>
