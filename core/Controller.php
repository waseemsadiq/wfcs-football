<?php

declare(strict_types=1);

namespace Core;

use Core\Auth;

/**
 * Base controller with view rendering capabilities.
 * All application controllers extend this class.
 */
class Controller
{
    protected View $view;

    public function __construct()
    {
        $this->view = new View();
    }

    /**
     * Render a view with the main layout.
     */
    protected function render(string $template, array $data = [], string $layout = 'main'): void
    {
        $data['flash'] = $this->getFlash();
        $this->view->render($template, $data, $layout);
    }

    /**
     * Render a view without a layout.
     */
    protected function renderPartial(string $template, array $data = []): void
    {
        $this->view->renderPartial($template, $data);
    }

    /**
     * Redirect to another URL.
     */
    protected function redirect(string $url): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        // Clear buffer before redirecting to ensure only headers are sent
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        header("Location: {$url}");
        exit;
    }

    /**
     * Get POST data with optional default value.
     */
    protected function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Get GET data with optional default value.
     */
    protected function get(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Set a flash message for the next request.
     */
    protected function flash(string $type, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name('FOOTIE_SESSION');

            if (!session_start()) {
                throw new \RuntimeException('Failed to start session');
            }
        }
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    /**
     * Get and clear any flash message.
     */
    protected function getFlash(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name('FOOTIE_SESSION');

            if (!session_start()) {
                throw new \RuntimeException('Failed to start session');
            }
        }
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }

    /**
     * Return a JSON response.
     */
    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Check if request method is POST.
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Check if the current request is an AJAX request.
     */
    protected function isAjaxRequest(): bool
    {
        if ($this->post('ajax') === '1') {
            return true;
        }

        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * Validate required fields are present in POST data.
     * Returns array of missing field names.
     */
    protected function validateRequired(array $fields): array
    {
        $missing = [];
        foreach ($fields as $field) {
            $value = $this->post($field);
            if ($value === null || $value === '') {
                $missing[] = $field;
            }
        }
        return $missing;
    }

    /**
     * Validate CSRF token from POST data.
     * Returns true if valid, false otherwise.
     */
    protected function validateCsrf(): bool
    {
        $token = $this->post('csrf_token');
        return Auth::validateCsrfToken($token);
    }

    /**
     * Get CSRF token for forms.
     */
    protected function csrfToken(): string
    {
        return Auth::csrfToken();
    }

    /**
     * Render a CSRF hidden input field.
     */
    protected function csrfField(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($this->csrfToken()) . '">';
    }

    /**
     * Validate an email address.
     */
    protected function validateEmail(?string $email): bool
    {
        if ($email === null || $email === '') {
            return true; // Empty is valid (use validateRequired for required fields)
        }

        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate a hex colour code.
     */
    protected function validateHexColour(?string $colour): bool
    {
        if ($colour === null || $colour === '') {
            return false;
        }

        return preg_match('/^#[0-9A-Fa-f]{6}$/', $colour) === 1;
    }

    /**
     * Validate a date in YYYY-MM-DD format.
     */
    protected function validateDate(?string $date): bool
    {
        if ($date === null || $date === '') {
            return false;
        }

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1;
    }

    /**
     * Validate a time in HH:MM format.
     */
    protected function validateTime(?string $time): bool
    {
        if ($time === null || $time === '') {
            return false;
        }

        return preg_match('/^\d{2}:\d{2}$/', $time) === 1;
    }

    /**
     * Normalize frequency value to a valid option.
     * Returns the frequency if valid, or 'weekly' as default.
     */
    protected function normalizeFrequency(?string $frequency): string
    {
        $validFrequencies = ['daily', 'weekly', 'biweekly', 'fortnightly', 'monthly'];

        if ($frequency !== null && in_array($frequency, $validFrequencies, true)) {
            return $frequency;
        }

        return 'weekly';
    }

    /**
     * Validate string length.
     * Returns true if string is within min/max length bounds.
     */
    protected function validateLength(?string $value, int $min = 0, int $max = PHP_INT_MAX): bool
    {
        if ($value === null) {
            return $min === 0;
        }

        $length = mb_strlen($value);
        return $length >= $min && $length <= $max;
    }

    /**
     * Sanitize a string by trimming and limiting length.
     */
    protected function sanitizeString(?string $value, int $maxLength = 1000): string
    {
        if ($value === null) {
            return '';
        }

        $value = trim($value);
        if (mb_strlen($value) > $maxLength) {
            $value = mb_substr($value, 0, $maxLength);
        }

        return $value;
    }

    /**
     * Find an item in an array by its ID.
     */
    protected function findById(array $items, ?string $id): ?array
    {
        if ($id === null) {
            return null;
        }

        foreach ($items as $item) {
            if (isset($item['id']) && $item['id'] === $id) {
                return $item;
            }
        }

        return null;
    }
}
