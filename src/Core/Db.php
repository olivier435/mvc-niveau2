<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

final class DB
{
    protected static ?PDO $connection = null;
    /**
     * Retourne l'instance PDO (singleton par requête).
     */
    public static function pdo(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }
        try {
            self::$connection = new PDO(
                DB_DSN,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    // recommandé : évite certains comportements "surprenants"
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
            return self::$connection;
        } catch (PDOException $e) {
            // Gestion d'erreur propre (dev/prod)
            http_response_code(500);
            $errorMessage = (defined('APP_DEBUG') && APP_DEBUG)
                ? $e->getMessage()
                : 'Erreur serveur. Merci de réessayer plus tard.';
            // Vue d'erreur 500 (à créer)
            require VIEW_PATH . '/errors/500.php';
            exit;
        }
    }
}
