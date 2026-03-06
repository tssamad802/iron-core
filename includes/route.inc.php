<?php

class Router
{
    private string $basePath;
    private array $routes = [];
    private string $request;

    public function __construct(string $basePath = '')
    {
        $this->basePath = $basePath;
        $this->request = $this->resolveRequest();
    }

    private function resolveRequest(): string
    {
        $request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $request = str_replace($this->basePath, '', $request);
        $request = rtrim($request, '/');

        return $request === '' ? '/' : $request;
    }

    public function add(string|array $path, string $file): static
    {
        foreach ((array) $path as $p) {
            $this->routes[$p] = $file;
        }

        return $this;
    }

    public function dispatch(): void
    {
        if (array_key_exists($this->request, $this->routes)) {
            require __DIR__ . '/../' . $this->routes[$this->request];
        } else {
            http_response_code(404);
        }
    }
}