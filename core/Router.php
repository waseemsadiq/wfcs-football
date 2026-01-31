<?php

declare(strict_types=1);

namespace Core;

/**
 * Simple URL router for the application.
 * Maps URLs to controller actions.
 */
class Router
{
    private array $routes = [];
    private array $protectedRoutes = [];

    /**
     * Register a GET route.
     */
    public function get(string $path, string $controller, string $action, bool $protected = true): self
    {
        $this->addRoute('GET', $path, $controller, $action, $protected);
        return $this;
    }

    /**
     * Register a POST route.
     */
    public function post(string $path, string $controller, string $action, bool $protected = true): self
    {
        $this->addRoute('POST', $path, $controller, $action, $protected);
        return $this;
    }

    /**
     * Add a route to the routing table.
     */
    private function addRoute(string $method, string $path, string $controller, string $action, bool $protected): void
    {
        $pattern = $this->convertToRegex($path);
        $this->routes[$method][$pattern] = [
            'controller' => $controller,
            'action' => $action,
            'path' => $path,
        ];

        if ($protected) {
            $this->protectedRoutes[$pattern] = true;
        }
    }

    /**
     * Convert a route path to a regex pattern.
     * Supports {param} placeholders.
     */
    private function convertToRegex(string $path): string
    {
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    /**
     * Dispatch the current request to the appropriate controller.
     */
    public function dispatch(string $uri, string $method): void
    {
        $uri = $this->normaliseUri($uri);
        $method = strtoupper($method);

        if (!isset($this->routes[$method])) {
            $this->handleNotFound();
            return;
        }

        foreach ($this->routes[$method] as $pattern => $route) {
            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                if ($this->isProtected($pattern) && !Auth::check()) {
                    $this->redirectToLogin();
                    return;
                }

                $this->callAction($route['controller'], $route['action'], $params);
                return;
            }
        }

        $this->handleNotFound();
    }

    /**
     * Check if a route requires authentication.
     */
    private function isProtected(string $pattern): bool
    {
        return isset($this->protectedRoutes[$pattern]);
    }

    /**
     * Normalise the URI by removing query strings and trailing slashes.
     */
    private function normaliseUri(string $uri): string
    {
        $uri = parse_url($uri, PHP_URL_PATH) ?: '/';
        $uri = rtrim($uri, '/') ?: '/';
        return $uri;
    }

    /**
     * Call the controller action with parameters.
     */
    private function callAction(string $controller, string $action, array $params): void
    {
        $controllerClass = "App\\Controllers\\{$controller}";

        if (!class_exists($controllerClass)) {
            $this->handleNotFound();
            return;
        }

        $instance = new $controllerClass();

        if (!method_exists($instance, $action)) {
            $this->handleNotFound();
            return;
        }

        call_user_func_array([$instance, $action], $params);
    }

    /**
     * Redirect to the login page.
     */
    private function redirectToLogin(): void
    {
        header('Location: /login');
        exit;
    }

    /**
     * Handle a 404 not found response.
     */
    private function handleNotFound(): void
    {
        http_response_code(404);
        echo '<h1>Page not found</h1>';
        echo '<p>Sorry, the page you are looking for does not exist.</p>';
        echo '<p><a href="/">Back to home</a> | <a href="/admin">Admin dashboard</a></p>';
    }
}
