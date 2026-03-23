<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\CreationController;
use App\Controllers\HomeController;
use App\Controllers\PasswordResetController;

return [
    // Home
    ['GET', '/', [HomeController::class, 'index']],

    // Auth
    ['GET', '/register', [AuthController::class, 'register']],
    ['POST', '/register', [AuthController::class, 'register']],
    ['GET', '/login', [AuthController::class, 'login']],
    ['POST', '/login', [AuthController::class, 'login']],
    ['POST', '/logout', [AuthController::class, 'logout'], '@AUTH'],

    //resetPassword
    ['GET', '/forgot-password', [PasswordResetController::class, 'forgotPassword']],
    ['POST', '/forgot-password', [PasswordResetController::class, 'forgotPassword']],

    // Rset PassWord
    ['GET', '/reset-password',   [PasswordResetController::class, 'forgotPassword']],
    ['POST', '/reset-password', [PasswordResetController::class, 'forgotPassword']],




    // Créations (CRUD)
    ['GET', '/creations', [CreationController::class, 'index']],

    // Create
    ['GET', '/creations/new', [CreationController::class, 'create'], 'ROLE_ADMIN'], // affiche le formulaire
    ['POST', '/creations/new', [CreationController::class, 'create'], 'ROLE_ADMIN'], // traite le formulaire

    // Read (par ID)
    ['GET', '/creations/{id}', [CreationController::class, 'showById']],

    // Update
    ['GET', '/creations/{id}/edit', [CreationController::class, 'edit'], 'ROLE_ADMIN'],
    ['POST', '/creations/{id}/edit', [CreationController::class, 'edit'], 'ROLE_ADMIN'],

    // Delete
    ['POST', '/creations/{id}/delete', [CreationController::class, 'delete'], 'ROLE_ADMIN'],

    // Read (par SLUG) : route séparée pour éviter conflit
    ['GET', '/c/{slug}', [CreationController::class, 'showBySlug']],
];
