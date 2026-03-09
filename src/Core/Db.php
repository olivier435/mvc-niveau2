<?php

namespace App\Core;

use PDO;
use PDOException;

final class DB
{
    protected static ?PDO $connection = null;

    /**
     * Retour l'instance PDO (singleton par requête)
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
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            return self::$connection;
        } catch (PDOException $e) {
            http_response_code(500);

            $errorMEssage = (defined('APP_DEBUG') && APP_DEBUG)
                ? $e->getMessage()
                : 'Erreur serveur. Merci de réessayer plus tard.';

            require VIEW_PATH . '/errors/600.php';
            exit;
        }
    }
}
