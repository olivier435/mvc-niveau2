<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Service\AuthService;

abstract class Controller
{
    protected ?AuthService $auth = null;

    private function auth(): AuthService
    {
        if ($this->auth === null) {
            $this->auth = new AuthService();
        }
        return $this->auth;
    }

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

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-type: application/json; charset=UTF-8');

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
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

    protected function getUser(): ?array
    {
        return $this->auth()->user();
    }

    protected function isAuthenticated(): bool
    {
        return $this->auth()->check();
    }

    protected function requireGuest(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect('/');
        }
    }

    protected function redirectIfAuthenticated(string $to = '/'): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect($to);
        }
    }

    protected function denyAccess(string $message = 'Accès interdit'): void
    {
        $this->abort(403, $message);
    }

    protected function requireRole(string $role): void
    {
        if (!$this->auth()->check()) {
            $this->redirect('/login');
        }

        if (!$this->auth()->isGranted($role)) {
            http_response_code(403);
            echo 'Accès interdit';
            exit;
        }
    }

    protected function old(array $old, string $key, string $default = ''): string
    {
        return htmlspecialchars((string)($old[$key] ?? $default), ENT_QUOTES, 'UTF-8');
    }
}
