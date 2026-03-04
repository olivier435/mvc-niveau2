<?php

declare(strict_types=1);

namespace App\Controllers;



abstract class Controller
{
    protected function render(string $view, array $params = [], string $layout = 'layout/base'): void
    {
        extract($params, EXTR_OVERWRITE);

        ob_start();
        require dirname(__DIR__, 2) . "/Views/{$view}.php";
        $content = ob_get_clean();
        //vide tampon et met dans content

        require dirname(__DIR__, 2) . "/Views/{$layout}.php";
    }

    protected function redirect(string $url): void
    {
        //renvoie un entete http dit au navigateur va ailleur
        header('Location: ' . $url, true, 302);
        exit;
        //true remplace 302 evite alourdie //exit evit de continue ton code
        //$this_>redirect('/creations')url creation rediriger
    }

    protected function isGranted(string $role): bool
    {
        //role verifie si un utilisateur est presente en session iss $user on lit le role stocke en session on le compare au role demande , role user false
        //isgranted role admin methode controleur false si a un role utilisateur 
        //if gerer plusieur role $this->is granted 
        return isset($_SESSION['user']) && ($_SESSION['user']['role'] ?? null) === $role;
    }
    //get user si la session n'est pas active on la demarre si ^ssion utilisateur dans la session on renvoie l'user sinon null permet de connaitre le nom

    protected function getUser(): ?array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return $_SESSION['user'] ?? null;
    }
    //require role si t'es pas admin je te redirige 
    protected function requireRole(string $role, string $redirectTo = '/login'): void
    {
        if (!$this->isGranted($role)) {
            $this->redirect($redirectTo);
        }
    }
    //message flach affiche message quand tu redifrection post formulaire redirige a la page

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
    //si la page es tmanquant gere les message erreur  abord gere la redirection vers les erreurs
    protected function abort(int $statusCode = 404, string $message = ''): void
    {
        http_response_code($statusCode);
        if ($statusCode === 404) {
            require APP_ROOT . '/Views/errors/404.php';
            exit;
        }
        if ($statusCode === 403) {
            require APP_ROOT . '/Views/errors/403.php';
            exit;
        }
        echo $message !== '' ? $message : 'Erreur';
        exit;
    }
    //permet de rediriger en arriere  page precedente , page que j'ai vu precedemnt
    protected function redirectBack(string $fallback = '/'): void
    {
        $url = $_SERVER['HTTP_REFERER'] ?? $fallback;
        $this->redirect($url);
    }
    //http derniere page utilsie
}
