<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/bootstrap.php';

use App\Core\Router;
// 1) on charge la liste des routes depuis config/ routes.php
$router = new Router(require dirname(__DIR__) . '/config/routes.php');
//renvoyer le tableau de route, injecter dans le constructeur du router

//2 on donne au router la methode HTTP + l URI url  demandée
//request method cherche dans la requete get ou post
//request url tout l'url creation/ request uri
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
//REQUEST URI => url /creations/2?foo=bar
// /creations/2 trouver la route correspondant  dans la route 
