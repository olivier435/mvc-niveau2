<?php

declare(strict_types=1);

use App\Controllers\AdminUserController;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\CreationController;
use App\Controllers\DashboardController;
use App\Controllers\PasswordResetController;

return [
    // Home  //path chemin
    ['GET',  '/',                          [HomeController::class, 'index']],

    // Dashboard admin
    ['GET', '/admin',                      [DashboardController::class, 'index'], 'ROLE_ADMIN'],

    // Auth
    ['GET',  '/register',                  [AuthController::class, 'register']],
    ['POST', '/register',                  [AuthController::class, 'register']],
    ['GET',  '/login',                     [AuthController::class, 'login']],
    ['POST', '/login',                     [AuthController::class, 'login']],
    ['POST', '/logout',                    [AuthController::class, 'logout'], '@AUTH'],

    // ResetPassword
    ['GET',  '/forgot-password',           [PasswordResetController::class, 'forgotPassword']],
    ['POST', '/forgot-password',           [PasswordResetController::class, 'forgotPassword']],
    ['GET',  '/reset-password',            [PasswordResetController::class, 'resetPassword']],
    ['POST', '/reset-password',            [PasswordResetController::class, 'resetPassword']],

    // Admin users
    ['GET', '/admin/users', [AdminUserController::class, 'index'], 'ROLE_ADMIN'],
    ['GET', '/admin/users/{id}/edit', [AdminUserController::class, 'edit'], 'ROLE_ADMIN'],
    ['POST', '/admin/users/{id}/edit', [AdminUserController::class, 'edit'], 'ROLE_ADMIN'],
    ['POST', '/admin/users/{id}/delete', [AdminUserController::class, 'delete'], 'ROLE_ADMIN'],
    ['POST', '/admin/users/{id}/reset-password', [AdminUserController::class, 'sendResetLink'], 'ROLE_ADMIN'],

    // Creations
    ['GET', '/creations', [CreationController::class, 'index']],
    // recherche JSON pour autocompletion
    ['GET', '/api/creations/search',  [CreationController::class, 'search']],

    ['GET', '/creations/new', [CreationController::class, 'create'], 'ROLE_ADMIN'],
    ['POST', '/creations/new', [CreationController::class, 'create'], 'ROLE_ADMIN'],
    ['GET', '/creations/{id}', [CreationController::class, 'showById']],
    ['GET', '/creations/{id}/edit', [CreationController::class, 'edit'], 'ROLE_ADMIN'],
    ['POST', '/creations/{id}/edit', [CreationController::class, 'edit'], 'ROLE_ADMIN'],
    ['POST', '/creations/{id}/delete', [CreationController::class, 'delete'], 'ROLE_ADMIN'],
    ['GET', '/c/{slug}', [CreationController::class, 'showBySlug']],
];
