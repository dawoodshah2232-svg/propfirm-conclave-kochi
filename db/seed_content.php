<?php
// One-time content migration: moves the current static site content into the DB.
// Run:  php db/seed_content.php
// Safe to re-run — it skips anything already seeded.
$host = $argv[1] ?? 'localhost';
$user = $argv[2] ?? 'root';
$pass = $argv[3] ?? '';
$dbname = $argv[4] ?? 'propfirm_conclave';

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$root = dirname(__DIR__);
$ins = function ($table, $cols, $rows) use ($pdo) {
    $ph = '(' . implode(',', array_fill(0, count($cols), '?')) . ')';
    $st = $pdo->prepare("INSERT IGNORE INTO $table (" . implode(',', $cols) . ") VALUES $ph");
    $n = 0;
    foreach ($rows as $r) { try { $st->execute($r); $n += $st->rowCount(); } catch (Throwable $e) {} }
    return $n;
};
$count = fn($t) => (int)$pdo->query("SELECT COUNT(*) FROM $t")->fetchColumn();

// ------------------------------------------------------------ speakers
if (!$count('speakers')) {
    $rows = [
        ['Arjun Mehta', 'Funded Trader', 'Apex Funding', 'Cleared three evaluations before going full-time — now teaches the risk framework that got him funded.', 'assets/img/speaker-1.jpg', 'published', 0],
        ['Sarah Williams', 'Head of Trader Development', 'Apex Funding', 'Coached 3,000+ traders through prop challenges. Her session: the consistency rules firms actually score.', 'assets/img/speaker-2.jpg', 'published', 1],
        ['Mohammed Al-Rashid', 'CEO', 'Titan Prop', 'Building trader-first funding models across MENA & Asia. Keynote on where prop trading goes next.', 'assets/img/speaker-3.jpg', 'published', 2],
        ['Priya Nair', 'Trading Psychologist', 'MindOverMarkets', 'The mindset edge: drawdown psychology and daily routines that keep funded traders funded.', 'assets/img/speaker-4.jpg', 'published', 3],
        ['David Chen', 'Algorithmic Strategist', 'QuantDesk', 'Systematic strategies engineered to survive prop firm drawdown constraints.', 'assets/img/speaker-5.jpg', 'published', 4],
        ['Rahul Verma', 'Funded Trader', 'Independent', 'Scaled from a $10K evaluation to $800K in funded capital in 18 months.', 'assets/img/speaker-6.jpg', 'published', 5],
    ];
    echo 'speakers: ' . $ins('speakers', ['name','role','company','bio','photo','status','sort'], $rows) . PHP_EOL;
}

// ------------------------------------------------------------ sponsors
if (!$count('sponsors')) {
    $tiers = [];
    foreach ($pdo->query('SELECT id, slug FROM sponsor_tiers')->fetchAll(PDO::FETCH_ASSOC) as $t) $tiers[$t['slug']] = $t['id'];
    $rows = [
        [$tiers['platinum'] ?? null, 'Exness', 'assets/img/sponsors/exness.jpg', '', 'published', 0],
        [$tiers['gold'] ?? null, 'FundingPips', 'assets/img/sponsors/fundingpips.jpg', '', 'published', 1],
        [$tiers['gold'] ?? null, 'MultiBank Group', 'assets/img/sponsors/multibank.jpg', '', 'published', 2],
        [$tiers['silver'] ?? null, 'Vantage', 'assets/img/sponsors/vantage.jpg', '', 'published', 3],
        [$tiers['silver'] ?? null, 'Valetax', 'assets/img/sponsors/valetax.jpg', '', 'published', 4],
        [$tiers['exhibitor'] ?? null, 'GTC', 'assets/img/sponsors/gtc.jpg', '', 'published', 5],
    ];
    echo 'sponsors: ' . $ins('sponsors', ['tier_id','name','logo','website','status','sort'], $rows) . PHP_EOL;
}

// ------------------------------------------------------------ gallery
if (!$count('gallery')) {
    $imgs = [
        ['Keynote stage', 'assets/img/keynote.jpg', 0],
        ['Panel discussion', 'assets/img/panel.jpg', 1],
        ['Expo floor', 'assets/img/expo.jpg', 2],
        ['Networking evening', 'assets/img/networking.jpg', 3],
        ['Adlux Convention Centre', 'assets/img/venue-adlux-1.jpg', 4],
        ['Adlux at dusk', 'assets/img/venue-adlux-2.jpg', 5],
        ['Conference hall', 'assets/img/why-attend-conference.jpg', 6],
        ['Gala night', 'assets/img/gala.jpg', 7],
    ];
    echo 'gallery: ' . $ins('gallery', ['title','image','sort'], $imgs) . PHP_EOL;
}

// ------------------------------------------------------------ faqs
if (!$count('faqs')) {
    $html = file_get_contents($root . '/faq.html');
    preg_match_all('#<button class="faq-q"[^>]*>(.*?)<span class="pl">.*?</button>\s*<div class="faq-a">(.*?)</div>#s', $html, $m, PREG_SET_ORDER);
    $rows = [];
    foreach ($m as $i => $x) {
        $q = trim(html_entity_decode(strip_tags($x[1])));
        $a = trim(html_entity_decode(strip_tags($x[2], '<a>')));
        // make internal links php-friendly
        $a = str_replace(['sponsors.html', 'contact.html', 'about.html'], ['sponsors.php', 'contact.php', 'about.php'], $a);
        $rows[] = [$q, strip_tags($a), $i];
    }
    echo 'faqs: ' . $ins('faqs', ['question','answer','sort'], $rows) . PHP_EOL;
}

// ------------------------------------------------------------ agenda
if (!$count('agenda_sessions')) {
    $html = file_get_contents($root . '/agenda.html');
    $days = [
        1 => ['Day 1', 'Saturday, 12 December 2026'],
        2 => ['Day 2', 'Sunday, 13 December 2026'],
    ];
    $dayIds = [];
    foreach ($days as $n => [$label, $date]) {
        $id = $pdo->query("SELECT id FROM agenda_days WHERE day_label=" . $pdo->quote($label))->fetchColumn();
        if (!$id) {
            $pdo->prepare('INSERT INTO agenda_days (day_label, day_date, sort) VALUES (?,?,?)')->execute([$label, $date, $n]);
            $id = $pdo->lastInsertId();
        }
        $dayIds[$n] = $id;
    }
    foreach ([1, 2] as $n) {
        if (!preg_match('#<div class="timeline" data-day="' . $n . '"[^>]*>(.*?)</div>\s*</div>#s', $html, $m)) continue;
        preg_match_all('#<div class="t-item[^"]*">\s*<div class="time">(.*?)</div>\s*<h3>(.*?)</h3>\s*<p>(.*?)</p>(?:\s*<span class="tag">(.*?)</span>)?#s', $m[1], $ss, PREG_SET_ORDER);
        $i = 0;
        foreach ($ss as $x) {
            $time = trim(html_entity_decode(strip_tags($x[1])));
            [$start, $end] = array_pad(preg_split('/\s*[—–-]\s*/', $time), 2, '');
            $pdo->prepare('INSERT INTO agenda_sessions (day_id, start_time, end_time, title, speaker, description, tag, sort)
                           VALUES (?,?,?,?,?,?,?,?)')
                ->execute([$dayIds[$n], trim($start), trim($end),
                    trim(html_entity_decode(strip_tags($x[2]))), '',
                    trim(html_entity_decode(strip_tags($x[3]))),
                    trim(html_entity_decode(strip_tags($x[4] ?? ''))), $i++]);
        }
    }
    echo 'agenda sessions: ' . $pdo->query('SELECT COUNT(*) FROM agenda_sessions')->fetchColumn() . PHP_EOL;
}

// ------------------------------------------------------------ blog posts
if (!$count('posts')) {
    $files = [
        'what-is-a-prop-firm' => 'blog-desk-real.jpg',
        'how-prop-firm-challenges-work' => 'blog-seminar-real.jpg',
        'risk-management-funded-trader' => 'blog-study-real.jpg',
        'questions-before-choosing-prop-firm' => 'blog-desk-real.jpg',
        'why-kochi-trading' => 'blog-seminar-real.jpg',
    ];
    $n = 0;
    foreach ($files as $slug => $cover) {
        $f = $root . "/blog/$slug.html";
        if (!is_file($f)) continue;
        $html = file_get_contents($f);
        preg_match('#<title>(.*?)\s*\|\s*PropFirm Conclave Blog</title>#s', $html, $t);
        preg_match('#<meta name="description" content="(.*?)">#s', $html, $d);
        // body: inside <div class="wrap article"> minus the hero-img div
        $body = '';
        if (preg_match('#<div class="wrap article">(.*?)</section>#s', $html, $b)) {
            $body = preg_replace('#<div class="hero-img">.*?</div>#s', '', $b[1]);
            $body = preg_replace('#<div class="cta-band.*?</section>#s', '', $body);
            $body = trim($body);
        }
        $title = trim(html_entity_decode(strip_tags($t[1] ?? $slug)));
        $excerpt = trim(html_entity_decode($d[1] ?? ''));
        try {
            $pdo->prepare('INSERT IGNORE INTO posts (slug, title, excerpt, cover, body, status, published_at)
                           VALUES (?,?,?,?,?, "published", NOW())')
                ->execute([$slug, $title, $excerpt, 'assets/img/' . $cover, $body]);
            $n += $pdo->query("SELECT ROW_COUNT()")->fetchColumn();
        } catch (Throwable $e) {}
    }
    echo "posts: $n" . PHP_EOL;
}

echo "Seed complete." . PHP_EOL;
