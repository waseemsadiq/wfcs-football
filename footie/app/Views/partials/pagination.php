<?php
/**
 * Reusable pagination component.
 *
 * Required variables:
 * - $pagination: Array with keys: currentPage, totalPages, hasNext, hasPrev, startRecord, endRecord, totalCount
 * - $containerClass: Optional CSS class for container (default: 'mt-6')
 */
$containerClass = $containerClass ?? 'mt-6';

// Don't show pagination if there's only one page or no results
if (!isset($pagination) || $pagination['totalPages'] <= 1) {
    return;
}
?>

<div class="<?= $containerClass ?> flex flex-col sm:flex-row items-center justify-between gap-4">
    <!-- Info text -->
    <div class="text-sm text-text-muted">
        Showing <?= $pagination['startRecord'] ?>-<?= $pagination['endRecord'] ?> of <?= $pagination['totalCount'] ?> records
    </div>

    <!-- Pagination controls -->
    <div class="flex items-center gap-2">
        <!-- Previous button -->
        <button
            type="button"
            data-pagination-prev
            class="px-3 py-2 text-sm rounded border border-border bg-surface hover:bg-surface-hover transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            <?= !$pagination['hasPrev'] ? 'disabled' : '' ?>>
            ← Previous
        </button>

        <!-- Page numbers -->
        <div class="flex items-center gap-1" data-pagination-pages>
            <?php
            $currentPage = $pagination['currentPage'];
            $totalPages = $pagination['totalPages'];

            // Calculate which pages to show
            $startPage = max(1, $currentPage - 2);
            $endPage = min($totalPages, $currentPage + 2);

            // Show first page if not in range
            if ($startPage > 1): ?>
                <button type="button" data-page="1" class="px-3 py-2 text-sm rounded border border-border bg-surface hover:bg-surface-hover transition-colors">1</button>
                <?php if ($startPage > 2): ?>
                    <span class="px-2 text-text-muted">...</span>
                <?php endif;
            endif;

            // Show page range
            for ($i = $startPage; $i <= $endPage; $i++):
                $isActive = $i === $currentPage;
                ?>
                <button
                    type="button"
                    data-page="<?= $i ?>"
                    class="px-3 py-2 text-sm rounded border transition-colors <?= $isActive
                        ? 'bg-primary text-white border-primary font-semibold'
                        : 'border-border bg-surface hover:bg-surface-hover' ?>">
                    <?= $i ?>
                </button>
            <?php endfor;

            // Show last page if not in range
            if ($endPage < $totalPages): ?>
                <?php if ($endPage < $totalPages - 1): ?>
                    <span class="px-2 text-text-muted">...</span>
                <?php endif; ?>
                <button type="button" data-page="<?= $totalPages ?>" class="px-3 py-2 text-sm rounded border border-border bg-surface hover:bg-surface-hover transition-colors"><?= $totalPages ?></button>
            <?php endif; ?>
        </div>

        <!-- Next button -->
        <button
            type="button"
            data-pagination-next
            class="px-3 py-2 text-sm rounded border border-border bg-surface hover:bg-surface-hover transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            <?= !$pagination['hasNext'] ? 'disabled' : '' ?>>
            Next →
        </button>
    </div>
</div>
