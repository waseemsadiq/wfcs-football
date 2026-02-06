<?php

declare(strict_types=1);

namespace Core;

/**
 * Template rendering with layout support.
 * Renders views within a layout template.
 */
class View
{
    private string $viewsPath;
    private string $layoutsPath;

    public function __construct()
    {
        $this->viewsPath = dirname(__DIR__) . '/app/Views';
        $this->layoutsPath = $this->viewsPath . '/layouts';
    }

    /**
     * Get shared data available to all views.
     */
    private function getSharedData(): array
    {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath = rtrim(dirname($scriptName), '/\\');
        return [
            'basePath' => $basePath === '/' || $basePath === '\\' ? '' : $basePath
        ];
    }

    /**
     * Render a view with a layout.
     */
    public function render(string $template, array $data = [], string $layout = 'main'): void
    {
        $data = array_merge($this->getSharedData(), $data);

        $content = $this->renderTemplate($template, $data);
        $data['content'] = $content;

        $layoutFile = "{$this->layoutsPath}/{$layout}.php";

        if (!file_exists($layoutFile)) {
            throw new \RuntimeException("Layout not found: {$layout}");
        }

        extract($data);
        include $layoutFile;
    }

    /**
     * Render a view without a layout.
     */
    public function renderPartial(string $template, array $data = []): void
    {
        $data = array_merge($this->getSharedData(), $data);
        echo $this->renderTemplate($template, $data);
    }

    /**
     * Render a template and return the output.
     */
    private function renderTemplate(string $template, array $data): string
    {
        $templateFile = "{$this->viewsPath}/{$template}.php";

        if (!file_exists($templateFile)) {
            throw new \RuntimeException("View not found: {$template}");
        }

        extract($data);
        ob_start();
        include $templateFile;
        return ob_get_clean();
    }

    /**
     * Escape HTML entities for safe output.
     */
    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Shorthand for escaping output.
     */
    public static function e(string $value): string
    {
        return self::escape($value);
    }

    /**
     * Convert various video URLs to their embed format.
     */
    public static function formatVideoEmbedUrl($url)
    {
        if (empty($url) || !is_string($url)) {
            return $url ?? '';
        }

        // YouTube
        if (preg_match('#(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?\/ ]{11})#i', $url, $matches)) {
            $videoId = $matches[1];
            return "https://www.youtube.com/embed/{$videoId}";
        }

        // Vimeo: vimeo.com/ID
        if (preg_match('#vimeo\.com/(?:channels/(?:\w+/)|groups/(?:\w+/)|album/(?:\d+/)|video/|)(\d+)(?:$|/|\?)#i', $url, $matches)) {
            $videoId = $matches[1];
            return "https://player.vimeo.com/video/{$videoId}";
        }

        // Cloudflare Stream
        if (strpos($url, 'cloudflarestream.com') !== false || strpos($url, 'videodelivery.net') !== false) {
            // If it's already an iframe or watch URL that's embeddable, leave it
            if (strpos($url, '/iframe') !== false || strpos($url, '/embed/') !== false) {
                return $url;
            }
            // Extract 32-char hex ID
            if (preg_match('#/([a-f0-9]{32})(?:$|/|\?)#i', $url, $matches)) {
                $videoId = $matches[1];
                $domain = strpos($url, 'videodelivery.net') !== false ? 'iframe.videodelivery.net' : 'customer-id.cloudflarestream.com';
                $parsedUrl = parse_url($url);
                $host = isset($parsedUrl['host']) ? $parsedUrl['host'] : $domain;
                return "https://{$host}/{$videoId}/iframe";
            }
        }

        return $url;
    }
}

/**
 * Global helper function for escaping output.
 */
if (!function_exists('e')) {
    function e($value)
    {
        return View::escape((string) $value);
    }
}
