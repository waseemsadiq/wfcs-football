<?php
/**
 * Danger Zone Delete Section
 *
 * Displays a warning card with a delete button for destructive actions.
 *
 * @var string $entityType - Entity type (e.g., 'league', 'cup', 'team', 'season')
 * @var string $deleteUrl - Form action URL for deletion
 * @var string $csrfToken - CSRF token for form submission
 * @var string|null $customMessage - Optional custom warning message
 */

$message = $customMessage ?? "Permanently delete this {$entityType} and all its data.";
$confirmMessage = "Are you sure you want to delete this {$entityType}? This cannot be undone.";
?>

<div class="card border-danger/30 mt-8">
    <h2 class="text-xl font-bold text-danger mb-4">Danger Zone</h2>
    <p class="text-text-muted mb-6"><?= htmlspecialchars($message) ?></p>
    <form method="POST" action="<?= htmlspecialchars($deleteUrl) ?>"
          onsubmit="return confirm('<?= htmlspecialchars($confirmMessage, ENT_QUOTES) ?>');">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <button type="submit" class="btn bg-transparent border border-danger text-danger hover:bg-danger hover:text-white">
            Delete <?= ucfirst(htmlspecialchars($entityType)) ?>
        </button>
    </form>
</div>
