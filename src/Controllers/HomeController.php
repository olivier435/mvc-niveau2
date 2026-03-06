<?php

declare(strict_types=1);

namespace App\Controllers;

final class HomeController extends Controller
{
    public function index(): void
    {
        $this->setFlash('success', 'Bienvene');
        $this->render('home/index', [
            'title' => 'Accueil',
        ]);
    }
}
