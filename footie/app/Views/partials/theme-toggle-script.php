<script>
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
        document.getElementById('theme-toggle-public')?.addEventListener('click', toggleTheme);
        document.getElementById('theme-toggle-mobile-public')?.addEventListener('click', toggleTheme);
    })();
</script>
