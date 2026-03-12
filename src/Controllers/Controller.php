<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;

abstract class Controller
{
    protected function render(string $view, array $params = [], string $layout = 'layout/base'): void
    {
        extract($params, EXTR_OVERWRITE);

        ob_start();
        require VIEW_PATH . "/{$view}.php";
        $content = ob_get_clean();

        require VIEW_PATH . "/{$layout}.php";
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url, true, 302);
        exit;
    }

    protected function redirectBack(string $fallback = '/'): void
    {
        $url = $_SERVER['HTTP_REFERER'] ?? $fallback;
        $this->redirect($url);
    }

    protected function setFlash(string $type, string $message): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['_flashes'][$type][] = $message;
    }

    protected function getFlashes(): array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $flashes = $_SESSION['_flashes'] ?? [];
        unset($_SESSION['_flashes']);
        return $flashes;
    }

    protected function abort(int $statusCode = 404, string $message = ''): void
    {
        http_response_code($statusCode);
        $errorMessage = $message;
        if ($statusCode === 404) {
            require VIEW_PATH . '/errors/404.php';
            exit;
        }
        if ($statusCode === 403) {
            require VIEW_PATH . '/errors/403.php';
            exit;
        }
        if ($statusCode === 405) {
            require VIEW_PATH . '/errors/405.php';
            exit;
        }
        echo $message !== '' ? $message : 'Erreur';
        exit;
    }

    protected function requirePost(): void
    {
        if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->abort(405);
        }
    }

    protected function requireCsrf(string $tokenId, string $fieldName = '_token'): void
    {
        $token = $_POST[$fieldName] ?? null;
        if (!Csrf::isValid($tokenId, is_string($token) ? $token : null)) {
            $this->abort(403, 'Token CSRF invalide.');
        }
    }

    protected function isGranted(string $role): bool
    {
        return isset($_SESSION['user']) && ($_SESSION['user']['role'] ?? null) === $role;
    }

    protected function getUser(): ?array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return $_SESSION['user'] ?? null;
    }
    
    protected function requireRole(string $role, string $redirectTo = '/login'): void
    {
        if (!$this->isGranted($role)) {
            $this->redirect($redirectTo);
        }
    }
}
