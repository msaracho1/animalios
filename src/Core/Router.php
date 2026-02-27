<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<string, Route[]> */
    private array $routes = []; // [METHOD][] = Route

    /** @var array<string, string> */
    private static array $named = []; // name => pattern

    public function get(string $pattern, mixed $handler): Route
    {
        return $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, mixed $handler): Route
    {
        return $this->add('POST', $pattern, $handler);
    }

    private function add(string $method, string $pattern, mixed $handler): Route
    {
        $route = new Route($method, $pattern, $handler);
        $this->routes[$method][] = $route;
        return $route;
    }

    public function dispatch(Request $req): void
    {
        $methodRoutes = $this->routes[$req->method] ?? [];

        foreach ($methodRoutes as $route) {
            $params = $route->match($req->path);
            if ($params === null) continue;

            // CSRF on POST
            if ($req->method === 'POST') {
                $token = (string)($req->post['_token'] ?? '');
                if (!Session::verifyCsrf($token)) {
                    http_response_code(419);
                    echo '419 - CSRF token mismatch';
                    return;
                }
            }

            // Middlewares
            foreach ($route->middlewares as $mw) {
                $res = $mw($req);
                if ($res !== null) return; // middleware already handled
            }

            $this->invoke($route->handler, $req, $params);
            return;
        }

        http_response_code(404);
        echo '404 - Not Found';
    }

    private function invoke(mixed $handler, Request $req, array $params): void
    {
        if (is_callable($handler)) {
            echo $handler($req, ...array_values($params));
            return;
        }

        if (is_array($handler) && count($handler) === 2) {
            [$class, $method] = $handler;
            $controller = new $class();
            echo $controller->$method($req, ...array_values($params));
            return;
        }

        throw new \RuntimeException('Invalid route handler');
    }

    public static function registerNamed(string $name, string $pattern): void
    {
        self::$named[$name] = $pattern;
    }

    public static function urlFor(string $name, array $params = []): string
    {
        $pattern = self::$named[$name] ?? null;
        if ($pattern === null) {
            throw new \RuntimeException("Named route not found: $name");
        }

        $url = $pattern;
        foreach ($params as $k => $v) {
            $url = str_replace('{' . $k . '}', rawurlencode((string)$v), $url);
        }
        return $url;
    }
}

final class Route
{
    /** @var callable[] */
    public array $middlewares = [];

    private ?string $name = null;
    private string $regex;

    /** @var string[] */
    private array $paramNames;

    public function __construct(
        public readonly string $method,
        public readonly string $pattern,
        public readonly mixed $handler, // ✅ FIX: readonly MUST be typed in PHP 8.2
    ) {
        [$this->regex, $this->paramNames] = $this->compile($pattern);
    }

    public function name(string $name): self
    {
        $this->name = $name;
        Router::registerNamed($name, $this->pattern);
        return $this;
    }

    public function middleware(callable $mw): self
    {
        $this->middlewares[] = $mw;
        return $this;
    }

    public function match(string $path): ?array
    {
        if (!preg_match($this->regex, $path, $m)) return null;

        $params = [];
        foreach ($this->paramNames as $p) {
            $params[$p] = $m[$p] ?? null;
        }
        return $params;
    }

    /** @return array{0:string,1:array<int,string>} */
    private function compile(string $pattern): array
    {
        $paramNames = [];
        $regex = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            function ($matches) use (&$paramNames) {
                $paramNames[] = $matches[1];
                return '(?P<' . $matches[1] . '>[^/]+)';
            },
            $pattern
        );

        return ['#^' . $regex . '$#', $paramNames];
    }
}