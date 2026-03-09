<?php

declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    private const KEY = '_csrf_tokens';
    public static function token(string $id): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!empty($_SESSION[self::KEY][$id])) {
            return (string) $_SESSION[self::KEY][$id];
        }
        $token = bin2hex(random_bytes(32));
        $_SESSION[self::KEY][$id] = $token;
        return $token;
    }
    public static function isValid(string $id, ?string $token): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if ($token === null) {
            return false;
        }
        $known = $_SESSION[self::KEY][$id] ?? null;
        $ok = is_string($known) && hash_equals($known, $token);
        // Invalidation après succès (anti re-play)
        if ($ok) {
            unset($_SESSION[self::KEY][$id]);
        }
        return $ok;
    }
}
