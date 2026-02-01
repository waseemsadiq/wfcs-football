<?php
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
?>
<!DOCTYPE html>
<html lang="en-GB" class="<?= !isset($_COOKIE['theme']) || $_COOKIE['theme'] === 'light' ? 'light' : '' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) . ' - ' : '' ?>WFCS Football Admin</title>
    <link rel="stylesheet" href="<?= $basePath ?>/css/output.css?v=20260201-blue">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-background text-text-main font-sans antialiased min-h-screen flex flex-col">
    <header class="bg-surface/80 backdrop-blur-md border-b border-border py-4 mb-8 sticky top-0 z-50">
        <div class="max-w-[1200px] mx-auto px-4 md:px-6 w-full flex justify-between items-center">
            <a href="<?= $basePath ?>/admin" class="flex items-center no-underline">
                <img src="<?= $basePath ?>/images/logo-white.svg" alt="WFCS Football" class="h-16 w-16 hide-light">
                <img src="<?= $basePath ?>/images/logo-blue.svg" alt="WFCS Football" class="h-16 w-16 hide-dark">
            </a>

            <!-- Desktop Navigation (hidden on mobile) -->
            <nav class="hidden md:flex items-center space-x-6">
                <a href="<?= $basePath ?>/admin"
                    class="<?= ($currentPage ?? '') === 'dashboard' ? 'text-primary' : 'text-text-muted' ?> font-semibold text-sm hover:text-primary transition-colors uppercase tracking-wide">Dashboard</a>
                <a href="<?= $basePath ?>/admin/teams"
                    class="<?= ($currentPage ?? '') === 'teams' ? 'text-primary' : 'text-text-muted' ?> font-semibold text-sm hover:text-primary transition-colors uppercase tracking-wide">Teams</a>
                <a href="<?= $basePath ?>/admin/seasons"
                    class="<?= ($currentPage ?? '') === 'seasons' ? 'text-primary' : 'text-text-muted' ?> font-semibold text-sm hover:text-primary transition-colors uppercase tracking-wide">Seasons</a>
                <a href="<?= $basePath ?>/admin/leagues"
                    class="<?= ($currentPage ?? '') === 'leagues' ? 'text-primary' : 'text-text-muted' ?> font-semibold text-sm hover:text-primary transition-colors uppercase tracking-wide">Leagues</a>
                <a href="<?= $basePath ?>/admin/cups"
                    class="<?= ($currentPage ?? '') === 'cups' ? 'text-primary' : 'text-text-muted' ?> font-semibold text-sm hover:text-primary transition-colors uppercase tracking-wide">Cups</a>

                <!-- Theme Toggle -->
                <button id="theme-toggle" class="theme-toggle" aria-label="Toggle theme">
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

                <form method="POST" action="<?= $basePath ?>/logout" class="ml-4">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\Core\Auth::csrfToken()) ?>">
                    <button type="submit"
                        class="btn btn-sm bg-transparent border border-danger text-danger hover:bg-danger/10">Log
                        out</button>
                </form>
            </nav>

            <!-- Mobile Hamburger Menu (visible only on mobile) -->
            <button id="mobile-menu-btn" class="md:hidden p-2 text-text-muted hover:text-primary transition-colors"
                aria-label="Open menu">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </header>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden" onclick="closeSidebar()">
    </div>

    <!-- Mobile Sidebar -->
    <aside id="mobile-sidebar"
        class="fixed top-0 right-0 h-full w-64 bg-surface border-l border-border z-50 transform translate-x-full transition-transform duration-300 ease-in-out">
        <div class="flex flex-col h-full">
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between p-6 border-b border-border">
                <span class="text-lg font-bold text-primary">Menu</span>
                <button onclick="closeSidebar()" class="p-2 text-text-muted hover:text-primary transition-colors"
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
                <a href="<?= $basePath ?>/admin"
                    class="<?= ($currentPage ?? '') === 'dashboard' ? 'text-primary' : 'text-text-muted' ?> font-semibold text-base hover:text-primary transition-colors uppercase tracking-wide">Dashboard</a>
                <a href="<?= $basePath ?>/admin/teams"
                    class="<?= ($currentPage ?? '') === 'teams' ? 'text-primary' : 'text-text-muted' ?> font-semibold text-base hover:text-primary transition-colors uppercase tracking-wide">Teams</a>
                <a href="<?= $basePath ?>/admin/seasons"
                    class="<?= ($currentPage ?? '') === 'seasons' ? 'text-primary' : 'text-text-muted' ?> font-semibold text-base hover:text-primary transition-colors uppercase tracking-wide">Seasons</a>
                <a href="<?= $basePath ?>/admin/leagues"
                    class="<?= ($currentPage ?? '') === 'leagues' ? 'text-primary' : 'text-text-muted' ?> font-semibold text-base hover:text-primary transition-colors uppercase tracking-wide">Leagues</a>
                <a href="<?= $basePath ?>/admin/cups"
                    class="<?= ($currentPage ?? '') === 'cups' ? 'text-primary' : 'text-text-muted' ?> font-semibold text-base hover:text-primary transition-colors uppercase tracking-wide">Cups</a>

                <!-- Mobile Theme Toggle -->
                <button id="theme-toggle-mobile" class="theme-toggle flex items-center gap-2 mt-4"
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

            <!-- Sidebar Footer with Logout -->
            <div class="p-6 border-t border-border">
                <form method="POST" action="<?= $basePath ?>/logout" class="w-full">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\Core\Auth::csrfToken()) ?>">
                    <button type="submit"
                        class="btn btn-sm bg-transparent border border-danger text-danger hover:bg-danger/10 w-full">Log
                        out</button>
                </form>
            </div>
        </div>
    </aside>

    <script>
        // Sidebar functions
        function openSidebar() {
            document.getElementById('mobile-sidebar').classList.remove('translate-x-full');
            document.getElementById('sidebar-overlay').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            document.getElementById('mobile-sidebar').classList.add('translate-x-full');
            document.getElementById('sidebar-overlay').classList.add('hidden');
            document.body.style.overflow = '';
        }

        document.getElementById('mobile-menu-btn').addEventListener('click', openSidebar);

        // Theme toggle
        (function () {
            function toggleTheme() {
                const html = document.documentElement;
                html.classList.toggle('light');
                const isLight = html.classList.contains('light');
                document.cookie = 'theme=' + (isLight ? 'light' : 'dark') + ';path=/;max-age=31536000';
            }

            document.getElementById('theme-toggle')?.addEventListener('click', toggleTheme);
            document.getElementById('theme-toggle-mobile')?.addEventListener('click', toggleTheme);
        })();
    </script>

    <main class="flex-1">
        <div class="max-w-[1200px] mx-auto px-4 md:px-6 w-full">
            <?php if (isset($flash) && $flash): ?>
                <div
                    class="mb-6 p-4 rounded-sm border <?= $flash['type'] === 'error' ? 'bg-danger/10 text-red-300 border-danger/30' : 'bg-primary/10 text-primary border-primary/30' ?>">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </div>
    </main>

    <footer class="mt-20 py-12 text-center text-text-muted border-t border-border text-sm">
        <div class="max-w-[1200px] mx-auto px-4 md:px-6 w-full">
            <p>&copy; <?= date('Y') ?> WFCS Football | App by <a href="https://www.waseemsadiq.com"
                    target="_blank">Waseem Sadiq</a></p>
        </div>
    </footer>

    <script src="<?= $basePath ?>/js/app.js"></script>
</body>

</html>