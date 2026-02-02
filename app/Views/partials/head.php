<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) . ' - ' : '' ?>WFCS Football<?= $titleSuffix ?? '' ?></title>
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
    <meta property="og:url" content="<?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?>">
    <meta property="og:title" content="<?= isset($title) ? htmlspecialchars($title) . ' - ' : '' ?>WFCS Football">
    <meta property="og:description" content="Manage your football leagues, cups, and tournaments with ease.">
    <meta property="og:image" content="<?= $basePath ?>/images/og-image.png">

    <!-- Preload critical resources -->
    <link rel="preload" href="<?= $basePath ?>/css/output.css?v=20260202-1645" as="style">
    <link rel="preload" href="<?= $basePath ?>/css/fonts/Outfit-Regular.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= $basePath ?>/css/fonts/Outfit-SemiBold.woff2" as="font" type="font/woff2" crossorigin>

    <link rel="stylesheet" href="<?= $basePath ?>/css/output.css?v=20260202-1645">
</head>
