<?php

use App\Core\Router;

?>
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/">Créations</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= Router::isActiveRoute('/') ? 'active' : '' ?>" href="/">Accueil</a>
               
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= Router::isActiveRoute('/creations') ? 'active' : '' ?>" href="/creations">Créations</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= Router::isActiveRoute('/creations/new') ? 'active' : '' ?>" href="/creations/new">
                        <i class="bi bi-plus-circle"></i> Ajouter
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>