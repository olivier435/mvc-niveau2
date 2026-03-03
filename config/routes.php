<?php

declare(strict_types=1);

use App\Controllers\CreationController;
use App\Controllers\HomeController;

return [
    //HOme
    ['GET', '/', [HomeController::class, 'index']],
    //creation au niveau du crud
    ['GET', '/creations', [CreationController::class, 'index']],

    //creation create
    ['GET', '/creations/new', [CreationController::class, 'create']],
    //affiche le formulaire
    ['POST', '/creations/new', [CreationController::class, 'create']],
    //traite le formulaire

    //read par l'id
    ['GET', '/creations/{id}', [CreationController::class, 'showByid']],
    //montre par le id

    // update
    ['GET', '/creations/{id}/edit', [CreationController::class, 'edit']],
    ['POST', '/creations/{id}/edit', [CreationController::class, 'edit']],

    //delete
    ['POST', '/creations/{id}/delete', [CreationController::class, 'delete']],

    //read lecture slug : route separer pour eviter les conflits route
    ['GET', '/c/{slug}', [CreationController::class, 'showByslug']],
    //peut confondre le slug par id


];
