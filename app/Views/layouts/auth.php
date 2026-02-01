<?php
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
?>
<!DOCTYPE html>
<html lang="en-GB" class="<?= !isset($_COOKIE['theme']) || $_COOKIE['theme'] === 'light' ? 'light' : '' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) . ' - ' : '' ?>WFCS Football</title>
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $basePath ?>/images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $basePath ?>/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $basePath ?>/images/favicon-16x16.png">
    <link rel="manifest" href="<?= $basePath ?>/site.webmanifest">
    <link rel="mask-icon" href="<?= $basePath ?>/images/favicon.svg" color="#45A2DA">
    <link rel="shortcut icon" href="<?= $basePath ?>/images/favicon.ico">
    <meta name="msapplication-TileColor" content="#ffc40d">
    <meta name="msapplication-config" content="<?= $basePath ?>/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url"
        content="<?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?>">
    <meta property="og:title" content="<?= isset($title) ? htmlspecialchars($title) . ' - ' : '' ?>WFCS Football">
    <meta property="og:description" content="Manage your football leagues, cups, and tournaments with ease.">
    <meta property="og:image" content="<?= $basePath ?>/images/og-image.png">

    <link rel="stylesheet" href="<?= $basePath ?>/css/output.css?v=20260201-2145">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<body
    class="flex items-center justify-center min-h-screen bg-[radial-gradient(circle_at_center,var(--tw-gradient-stops))] from-surface to-background text-text-main font-sans antialiased">
    <?= $content ?>
</body>

</html>