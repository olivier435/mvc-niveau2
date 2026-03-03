<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes;
    //stock toute tes routes 

    public function __construct(array $routes)
    //injecte route changer route sans changer router 
    {
        $this->routes = $routes;
    }

    public function dispatch(string $method, string $uri): void
    //normalise la method revoit comme method on met en majuscule
    {
        $method = strtoupper($method === 'HEAD' ? 'GET' : $method);

        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        //parase analyse l url les composent a utilsie
        $path = rtrim($path, '/') ?: '/';
        //extrait de chemin on enleve $query  uniformiser creations/2/ =>/creations/2 on en eleve /derriere

        $allowedMethods = [];
        //method authorise

        foreach ($this->routes as [$routeMethod, $routePattern, $handler]) {
            $routeMethod = strtoupper($routeMethod);
            $routePattern = rtrim($routePattern, '/') ?: '/';
            //parcourir les route , destructure chaque  route 
            //mettre ordre parametre
            //route patter  faire / creations/{id}/edit
            [$regex, $paramOrder] = $this->compilePattern($routePattern);

            if (!preg_match($regex, $path, $matches)) {
                continue;
            }
            //si il ne math pas il continie
            $allowedMethods[] = $routeMethod;

            if ($routeMethod !== $method) {
                continue;
            }
            //si c'est id on casse si on valide  slud minuscule
            //on construit l'argument 

            $args = $this->buildArgs($matches, $paramOrder);
            if ($args === null) {
                $this->notFound();
                return;
            }

            [$class, $action] = $handler;

            if (!class_exists($class)) {
                $this->notFound("Controller introuvable : $class");
                return;
            }

            $controller = new $class();

            if (!method_exists($controller, $action)) {
                $this->notFound("Action introuvable : $action");
                return;
            }
            //...arg prend l'action show build id lui passe id dans l'url en intenger dans show 
            $controller->$action(...$args);
            return;
        }
        if (!empty($allowedMethods)) {
            $this->methodNotAllowed($allowedMethods);
            return;
        }
        //si la method n'est pas vide on envoie 405
        $this->notFound();
    }

    private function compilePattern(string $pattern): array
    {
        $paramOrder = [];
        $regex = preg_replace_callback(
            '#\{(\w+)\}#',
            function (array $m) use (&$paramOrder): string {
                $name = $m[1];
                $paramOrder[] = $name;
                return '(?P<' . $name . '>[^/]+)';
            },
            $pattern
        );
        return ['#^' . $regex . '$#', $paramOrder];
    }

    private function buildArgs(array $matches, array $paramOrder): ?array
    {
        $args = [];
        foreach ($paramOrder as $name) {
            $value = $matches[$name] ?? null;
            if ($value === null) {
                return null;
            }
            if ($name === 'id') {
                if (!ctype_digit($value)) {
                    return null;
                }
                $args[] = (int) $value;
                continue;
            }
            if ($name === 'slug') {
                $value = strtolower($value);
                if (!$this->isValidSlug($value)) {
                    return null;
                }
                $args[] = $value;
                continue;
            }
            $args[] = $value;
        }
        return $args;
    }
    //validation sluf
    private function isValidSlug(string $slug): bool
    {
        if ($slug === '') {
            return false;
        }
        if (!preg_match('#^[a-z0-9-]+$#', $slug)) {
            //autorise des slug en chiffres 
            //une chains een bois => une -chaise -en -bois 
            //titre libraire game og throne  parie 2 slug game-of-throne-partie-2
            return false;
        }
        if ($slug[0] === '-' || $slug[strlen($slug) - 1] === '-') {
            return false;
        }
        if (str_contains($slug, '--')) {
            return false;
        }
        return true;
    }

    private function notFound(string $message = '404 Not Found'): void
    {
        http_response_code(404);
        $errorMessage = $message;
        require APP_ROOT . '/Views/errors/404.php';
        exit();
    }

    private function methodNotAllowed(array $allowed): void
    {
        http_response_code(405);
        header('Allow: ' . implode(', ', array_unique($allowed)));
        require APP_ROOT . '/Views/errors/405.php';
        exit();
    }

    public static function isActiveRoute(string $path): bool
    {
        $current = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
        $current = rtrim($current, '/') ?: '/';
        $path = rtrim($path, '/') ?: '/';
        return $current === $path;
    }
}
