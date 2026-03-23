<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/bootstrap.php';

use App\Core\Router;
use App\Models\UserModel;
use App\Service\AuthService;

$authService = new AuthService();
$userModel = new UserModel();
$authService->loginFromRememberCookie($userModel);

// 1) On charge la liste des routes depuis config/routes.php
$router = new Router(require dirname(__DIR__) . '/config/routes.php');

// 2) On donne au router la méthode HTTP + l'URI demandée
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);