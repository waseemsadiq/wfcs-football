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

<body class="bg-background text-text-main font-sans antialiased min-h-screen flex flex-col">
    <header class="bg-surface/80 backdrop-blur-md border-b border-border py-5 mb-12 sticky top-0 z-50">
        <div class="max-w-[1200px] mx-auto px-4 md:px-6 w-full flex justify-between items-center">
            <a href="<?= $basePath ?>/" class="flex items-center no-underline">
                <img src="<?= $basePath ?>/images/logo-white.svg" alt="WFCS Football" class="h-20 w-20 hide-light">
                <img src="<?= $basePath ?>/images/logo-blue.svg" alt="WFCS Football" class="h-20 w-20 hide-dark">
            </a>

            <!-- Desktop Navigation (hidden on mobile) -->
            <nav class="hidden md:flex items-center space-x-8">
                <a href="<?= $basePath ?>/"
                    class="text-text-muted font-semibold text-base transition-colors uppercase tracking-wider hover:text-primary">Home</a>
                <a href="<?= $basePath ?>/leagues"
                    class="text-text-muted font-semibold text-base transition-colors uppercase tracking-wider hover:text-primary">Leagues</a>
                <a href="<?= $basePath ?>/cups"
                    class="text-text-muted font-semibold text-base transition-colors uppercase tracking-wider hover:text-primary">Cups</a>
                <a href="<?= $basePath ?>/teams"
                    class="text-text-muted font-semibold text-base transition-colors uppercase tracking-wider hover:text-primary">Teams</a>
                <?php if (isset($_SESSION['admin_id'])): ?>
                    <a href="<?= $basePath ?>/admin"
                        class="text-text-muted font-semibold text-base transition-colors uppercase tracking-wider hover:text-primary">Admin
                        Stuff</a>
                <?php endif; ?>
                <!-- Theme Toggle -->
                <button id="theme-toggle-public" class="theme-toggle" aria-label="Toggle theme">
                    <svg class="h-5 w-5 hide-light" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg class="h-5 w-5 hide-dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
                <a href="<?= $basePath ?>/login" aria-label="Admin Login"
                    class="text-text-muted hover:text-primary hover:scale-110 hover:opacity-70 transition-transform duration-200 inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </a>
            </nav>

            <!-- Mobile Hamburger Menu (visible only on mobile) -->
            <button id="mobile-menu-btn-public"
                class="md:hidden p-2 text-text-muted hover:text-primary transition-colors" aria-label="Open menu">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </header>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay-public" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden"
        onclick="closeSidebarPublic()"></div>

    <!-- Mobile Sidebar -->
    <aside id="mobile-sidebar-public"
        class="fixed top-0 right-0 h-full w-64 bg-surface border-l border-border z-50 transform translate-x-full transition-transform duration-300 ease-in-out">
        <div class="flex flex-col h-full">
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between p-6 border-b border-border">
                <span class="text-lg font-bold text-primary">Menu</span>
                <button onclick="closeSidebarPublic()" class="p-2 text-text-muted hover:text-primary transition-colors"
                    aria-label="Close menu">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="flex-1 flex flex-col p-6 space-y-4">
                <a href="<?= $basePath ?>/"
                    class="text-text-muted font-semibold text-base transition-colors uppercase tracking-wider hover:text-primary">Home</a>
                <a href="<?= $basePath ?>/leagues"
                    class="text-text-muted font-semibold text-base transition-colors uppercase tracking-wider hover:text-primary">Leagues</a>
                <a href="<?= $basePath ?>/cups"
                    class="text-text-muted font-semibold text-base transition-colors uppercase tracking-wider hover:text-primary">Cups</a>
                <a href="<?= $basePath ?>/teams"
                    class="text-text-muted font-semibold text-base transition-colors uppercase tracking-wider hover:text-primary">Teams</a>
                <?php if (isset($_SESSION['admin_id'])): ?>
                    <a href="<?= $basePath ?>/admin"
                        class="text-text-muted font-semibold text-base transition-colors uppercase tracking-wider hover:text-primary">Admin
                        Stuff</a>
                <?php endif; ?>
                <a href="<?= $basePath ?>/login"
                    class="text-text-muted font-semibold text-base transition-colors uppercase tracking-wider hover:text-primary flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    Admin Login
                </a>

                <!-- Mobile Theme Toggle -->
                <button id="theme-toggle-mobile-public" class="theme-toggle flex items-center gap-2 mt-4"
                    aria-label="Toggle theme">
                    <svg class="h-5 w-5 hide-light" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg class="h-5 w-5 hide-dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <span class="hide-light">Light Mode</span>
                    <span class="hide-dark">Dark Mode</span>
                </button>
            </nav>
        </div>
    </aside>

    <script>
        // Sidebar functions
        function openSidebarPublic() {
            document.getElementById('mobile-sidebar-public').classList.remove('translate-x-full');
            document.getElementById('sidebar-overlay-public').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebarPublic() {
            document.getElementById('mobile-sidebar-public').classList.add('translate-x-full');
            document.getElementById('sidebar-overlay-public').classList.add('hidden');
            document.body.style.overflow = '';
        }

        document.getElementById('mobile-menu-btn-public').addEventListener('click', openSidebarPublic);

        // Theme toggle
        (function () {
            function toggleTheme() {
                const html = document.documentElement;
                html.classList.toggle('light');
                const isLight = html.classList.contains('light');
                document.cookie = 'theme=' + (isLight ? 'light' : 'dark') + ';path=/;max-age=31536000';
            }

            document.getElementById('theme-toggle-public')?.addEventListener('click', toggleTheme);
            document.getElementById('theme-toggle-mobile-public')?.addEventListener('click', toggleTheme);
        })();
    </script>

    <main class="flex-1">
        <div class="max-w-[1200px] mx-auto px-4 md:px-6 w-full">
            <?= $content ?>
        </div>
    </main>

    <footer class="mt-20 py-12 text-center text-text-muted border-t border-border text-sm">
        <div class="max-w-[1200px] mx-auto px-4 md:px-6 w-full">
            <p>&copy; <?= date('Y') ?> WFCS Football | App by <a href="https://www.waseemsadiq.com"
                    target="_blank">Waseem Sadiq</a></p>
        </div>
    </footer>
</body>

</html>