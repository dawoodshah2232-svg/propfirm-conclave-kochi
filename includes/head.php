<?php
// Shared <head> for PHP pages. Set $page_title/$page_desc/$page_canonical before including.
require_once __DIR__ . '/site.php';
$site_name = site_setting('site_name', 'PropFirm Conclave Kochi');
$title = ($page_title ?? $site_name . ' 2026') . ' | ' . $site_name . ' 2026';
$desc  = $page_desc ?? 'PropFirm Conclave Kochi 2026 — 12–13 December 2026 at Adlux Convention Centre, Kochi. Meet prop firms, learn funded-trader strategies. Connect · Trade · Get Funded.';
$canon = $page_canonical ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title><?= site_esc($title) ?></title>
<meta name="description" content="<?= site_esc($desc) ?>">
<meta name="author" content="Finfluenze">
<meta name="robots" content="index, follow">
<?php if ($canon): ?><link rel="canonical" href="<?= site_esc($canon) ?>"><?php endif; ?>
<meta name="theme-color" content="#070709">
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= site_esc($site_name) ?> 2026">
<meta property="og:title" content="<?= site_esc($title) ?>">
<meta property="og:description" content="<?= site_esc($desc) ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= site_esc($title) ?>">
<meta name="twitter:description" content="<?= site_esc($desc) ?>">
<link rel="icon" type="image/png" href="assets/img/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css?v=20261007l">
</head>
<body>
