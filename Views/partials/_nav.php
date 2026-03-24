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
            <!-- Menu principal -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= Router::isActiveRoute('/') ? 'active' : '' ?>" href="/">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= Router::isActiveRoute('/creations') ? 'active' : '' ?>" href="/creations">
                        Créations
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= Router::isActiveRoute('/creations/new') ? 'active' : '' ?>" href="/creations/new">
                        <i class="bi bi-plus-circle"></i> Ajouter
                    </a>
                </li>
                <?php if ($this->getUser() && $this->getUser()['role'] === 'ROLE_ADMIN'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= Router::isActiveRoute('/admin') ? 'active' : '' ?>" href="/admin">
                            <i class="bi bi-speedometer2"></i> Admin
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            <!-- Zone authentification -->
            <ul class="navbar-nav ms-auto">
                <?php if ($this->getUser()): ?>
                    <li class="nav-item d-flex align-items-center me-3">
                        <span class="navbar-text">
                            Bonjour <?= htmlspecialchars($this->getUser()['firstname']) ?>
                        </span>
                    </li>
                    <li class="nav-item d-flex align-items-center">
                        <form method="post" action="/logout">
                            <input type="hidden" name="_token" value="<?= \App\Core\Csrf::token('logout') ?>">
                            <button class="btn btn-sm btn-outline-danger">
                                Déconnexion
                            </button>
                        </form>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?= Router::isActiveRoute('/login') ? 'active' : '' ?>" href="/login">
                            Connexion
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= Router::isActiveRoute('/register') ? 'active' : '' ?>" href="/register">
                            Inscription
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>