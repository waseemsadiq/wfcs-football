<?php
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
?>
<!DOCTYPE html>
<html lang="en-GB" class="<?= !isset($_COOKIE['theme']) || $_COOKIE['theme'] === 'light' ? 'light' : '' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) . ' - ' : '' ?>WFCS Football</title>
    <link rel="stylesheet" href="<?= $basePath ?>/css/output.css?v=20260201-blue">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<body
    class="flex items-center justify-center min-h-screen bg-[radial-gradient(circle_at_center,var(--tw-gradient-stops))] from-surface to-background text-text-main font-sans antialiased">
    <?= $content ?>
</body>

</html>