<?php

declare(strict_types=1);
//active type scalaire int string  protege chaine caractere

use Dotenv\Dotenv; 
//importe dote env depuis la librairie dossier vendor
//dotenv quelque chose

/**
 * chemin racine 
 */
define('APP_ROOT', dirname(__DIR__));
//on cree une constante app root dire le fichier courant  config
//dirname dossier parent 
define('PUBLIC_PATH', APP_ROOT . '/public');
//chemin absolue public autoload set des assert font image lie dans le dossier public
//definie le chemin
/**
 * 1) Autoload Composer (PSR-4 + dependance)
 */
require_once APP_ROOT . '/vendor/autoload.php';

//charger variable.env
/**
 *  2) Charger .env (versionner)
 */
Dotenv::createMutable(APP_ROOT, '.env')->safeLoad();
//charge .env je veux lire situe dans la racine de l'application 
//safeload si le fichier n'existe pas ne plante pas

/**
 * 3) Charger .env .local n (non versionner)  et forcer la surcharge
 *  -> pas de parsing manuel : c'est Dotenv qui parse
 *  -> on applique juste l'override avec le tableau retourné
 */
$envLocalFile = APP_ROOT . '/.env.local';
//on prepare le chemin .env.local secret 
if (is_file($envLocalFile)) {
    $loaded = Dotenv::createMutable(APP_ROOT, '.env.local')->safeLoad();
    //verifie .envlocal existe avant de le charger sinon
    //tu charge dotenv safe load retourne un tableau associatvie 
    //sageload cree tableau associatif
    //[DB_NAME= nom base]
    //DB_HOST =>127
    foreach ($loaded as $key => $value) {
        $_ENV[$key] = $_SERVER[$key] = $value;
    }
}
//.env.local doit gagner sur tout le reste , ecraser .env $server
//
//helper  env() eviter les erreurs 
if (!function_exists('env')) {
    function env(string $key, ?string $default = null): ?string
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
    //retourne la variable .env  si n existe pas sinon valeur par defaut
}
/**
 *  Constantes globales
 */
define('APP_ENV', env('APP_ENV', 'prod'));
define('APP_SECRET', env('APP_SECRET', 'change_me'));
//session, ou token 
define('APP_DEBUG', (int) env('APP_DEBUG', '0') === 1);
//convertie en vrai booleen converti en numerique compare 1
define('APP_URL', env('APP_URL', 'http://localhost'));
//utilise des emails , lien absolues

/**
 *  DB data base
 */
define('DB_HOST', env('DB_HOST', '127.0.0.1'));
define('DB_PORT', (string) env('DB_PORT', '3306'));
define('DB_NAME', env('DB_NAME', ''));
define('DB_USER', env('DB_USER', 'root'));

$pass = env('DB_PASSWORD', '');
if ($pass === 'null' || $pass === 'NULL') {
    $pass = null;
}
//gestion null on veut un vrai nul pas entre guillemet
define('DB_PASS', $pass);
define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));
define('DB_DSN', sprintf(
    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
    DB_HOST,
    DB_PORT,
    DB_NAME,
    DB_CHARSET,
));

define('VIEW_PATH', APP_ROOT . '/Views');
//si je change dossir template je chane views par template
//definie constante
/**
 * contaxte applicative 
 */
mb_internal_encoding('UTF-8');
date_default_timezone_set('Europe/Paris');

/**
 * securite sessions
 */
ini_set('session.cookie_httponly', '1');
//protectction xss pas lire en session
ini_set('session.cookie_secure', (!empty($_SERVER['HTTPS']) ? '1' : '0'));
ini_set('session.use_strict_monde', '1');
//empeche de fixation session

//demarrage securite session
if (session_status() !== PHP_SESSION_ACTIVE) {
    //parametre securite cookie (avant session_start)
    session_set_cookie_params([
        'lifetime' => 0,
        //session cookie
        'path' => '/',
        'domain' => '',
        'secure' => (!empty($_SERVER['HTTPS'])),
        //true en https
        'httponly' => true,
        //js ne peut lire le cookie
        'samesite' => 'Lax',
        //protege contre csrf basique
    ]);
    session_start();
}

/**
 * protection contre la session fixation
 */
if (!isset($_SESSION['_initiated'])) {
    session_regenerate_id(true);
    $_SESSION['_initiated'] = true;
}
