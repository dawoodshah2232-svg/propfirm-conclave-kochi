<?php
// Public-site data layer. Reads the admin database; if the DB is unreachable,
// every helper falls back to the baked-in static defaults so pages never break.
define('SITE_DB_HOST', 'localhost');
define('SITE_DB_NAME', 'propfirm_conclave');
define('SITE_DB_USER', 'root');
define('SITE_DB_PASS', '');

function site_db(): ?PDO {
    static $pdo = null; static $tried = false;
    if (!$tried) {
        $tried = true;
        try {
            $pdo = new PDO('mysql:host='.SITE_DB_HOST.';dbname='.SITE_DB_NAME.';charset=utf8mb4',
                SITE_DB_USER, SITE_DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
        } catch (Throwable $e) { $pdo = null; }
    }
    return $pdo;
}

function site_q(string $sql, array $p = []): array {
    $db = site_db(); if (!$db) return [];
    try { $st = $db->prepare($sql); $st->execute($p); return $st->fetchAll(); }
    catch (Throwable $e) { return []; }
}

function site_setting(string $key, string $default = ''): string {
    $r = site_q('SELECT svalue FROM settings WHERE skey=? LIMIT 1', [$key]);
    return $r ? (string)$r[0]['svalue'] : $default;
}

/** Editable content block with static fallback. */
function block(string $page, string $section, string $field, string $default = ''): string {
    $r = site_q('SELECT fvalue FROM content_blocks WHERE page_slug=? AND section=? AND field=? LIMIT 1',
                [$page, $section, $field]);
    return $r ? (string)$r[0]['fvalue'] : $default;
}

function site_esc(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function speakers_list(int $limit = 0): array {
    $sql = "SELECT * FROM speakers WHERE status='published' ORDER BY sort, id" . ($limit ? " LIMIT $limit" : '');
    return site_q($sql);
}

function sponsor_tiers_list(): array {
    return site_q('SELECT * FROM sponsor_tiers ORDER BY sort');
}

function sponsors_by_tier(): array {
    $tiers = sponsor_tiers_list();
    $out = [];
    foreach ($tiers as $t) {
        $sponsors = site_q("SELECT * FROM sponsors WHERE status='published' AND tier_id=? ORDER BY sort, id", [$t['id']]);
        if ($sponsors) $out[] = ['tier' => $t, 'sponsors' => $sponsors];
    }
    $loose = site_q("SELECT * FROM sponsors WHERE status='published' AND tier_id IS NULL ORDER BY sort, id");
    if ($loose) $out[] = ['tier' => ['name' => 'Our Sponsors', 'color' => '#c9a24b'], 'sponsors' => $loose];
    return $out;
}

function sponsors_flat(int $limit = 0): array {
    $sql = "SELECT s.*, t.name AS tier_name FROM sponsors s LEFT JOIN sponsor_tiers t ON t.id=s.tier_id
            WHERE s.status='published' ORDER BY s.sort, s.id" . ($limit ? " LIMIT $limit" : '');
    return site_q($sql);
}

function gallery_list(int $limit = 0): array {
    $sql = 'SELECT * FROM gallery ORDER BY sort, id' . ($limit ? " LIMIT $limit" : '');
    return site_q($sql);
}

function agenda_list(): array {
    $days = site_q('SELECT * FROM agenda_days ORDER BY sort');
    foreach ($days as &$d) $d['sessions'] = site_q('SELECT * FROM agenda_sessions WHERE day_id=? ORDER BY sort', [$d['id']]);
    return $days;
}

function faqs_list(): array { return site_q('SELECT * FROM faqs ORDER BY sort'); }

function posts_list(int $limit = 3): array {
    return site_q("SELECT * FROM posts WHERE status='published' ORDER BY published_at DESC LIMIT $limit");
}

function ticket_types_list(): array {
    return site_q("SELECT * FROM ticket_types WHERE status='active' ORDER BY sort");
}

function upload_public(string $name): string {
    if ($name === '') return '';
    if (str_starts_with($name, 'assets/') || str_starts_with($name, 'http')) return $name;
    return 'assets/uploads/' . $name;
}
