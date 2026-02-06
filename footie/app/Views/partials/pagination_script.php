/**
 * Reusable AJAX pagination script.
 *
 * Usage: Include this at the bottom of any view that uses pagination.
 *
 * Required variables:
 * - ajaxEndpoint: URL endpoint for AJAX requests (e.g., '/admin/players/ajax/list')
 * - containerSelector: CSS selector for content container (e.g., '#players-container')
 * - loaderSelector: CSS selector for loader element (e.g., '#players-loader')
 *
 * Optional variables:
 * - onPageLoad: Callback function after page loads (default: null)
 */

(function() {
    const AJAX_ENDPOINT = '<?= $ajaxEndpoint ?>';
    const CONTAINER_SELECTOR = '<?= $containerSelector ?>';
    const LOADER_SELECTOR = '<?= $loaderSelector ?>';
    const ON_PAGE_LOAD = <?= isset($onPageLoad) ? $onPageLoad : 'null' ?>;

    let currentParams = new URLSearchParams(window.location.search);

    // Handle pagination button clicks
    document.addEventListener('click', function(e) {
        const prevBtn = e.target.closest('[data-pagination-prev]');
        const nextBtn = e.target.closest('[data-pagination-next]');
        const pageBtn = e.target.closest('[data-page]');

        let newPage = null;

        if (prevBtn && !prevBtn.disabled) {
            const currentPage = parseInt(currentParams.get('page') || '1');
            newPage = currentPage - 1;
        } else if (nextBtn && !nextBtn.disabled) {
            const currentPage = parseInt(currentParams.get('page') || '1');
            newPage = currentPage + 1;
        } else if (pageBtn) {
            newPage = parseInt(pageBtn.dataset.page);
        }

        if (newPage) {
            loadPage(newPage);
        }
    });

    function loadPage(page) {
        const container = document.querySelector(CONTAINER_SELECTOR);
        const loader = document.querySelector(LOADER_SELECTOR);

        if (!container) return;

        // Update URL params
        currentParams.set('page', page);

        // Update browser URL without reload
        const newUrl = window.location.pathname + '?' + currentParams.toString();
        window.history.pushState({page}, '', newUrl);

        // Show loader
        if (loader) loader.classList.remove('hidden');
        container.style.opacity = '0.5';

        // Build AJAX URL with all current params
        const ajaxUrl = `${BASE_PATH}${AJAX_ENDPOINT}?${currentParams.toString()}`;

        fetch(ajaxUrl)
            .then(response => response.text())
            .then(html => {
                // Safe to use innerHTML - content is from our own trusted server endpoint
                container.innerHTML = html;

                // Call optional callback
                if (ON_PAGE_LOAD && typeof ON_PAGE_LOAD === 'function') {
                    ON_PAGE_LOAD();
                }
            })
            .catch(error => {
                console.error('Pagination error:', error);
                container.innerHTML = '<div class="text-error text-center py-8">Failed to load page.</div>';
            })
            .finally(() => {
                if (loader) loader.classList.add('hidden');
                container.style.opacity = '1';

                // Scroll to top of container
                container.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
    }

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function(e) {
        if (e.state && e.state.page) {
            currentParams = new URLSearchParams(window.location.search);
            loadPage(e.state.page);
        }
    });
})();
