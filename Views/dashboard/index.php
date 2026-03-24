<?php

declare(strict_types=1);

use App\Core\Csrf;

/** @var string $pageTitle */
/** @var \App\Entities\DashboardStats $stats */
/** @var \App\Entities\Creation[] $latestCreations */
/** @var array{total:int,admins:int,usersOnly:int} $userStats */
/** @var \App\Entities\User[] $latestUsers */
?>

<h1 class="mb-4"><?= htmlspecialchars($pageTitle ?? 'Dashboard admin') ?></h1>
<div class="container">
    <!-- ========================= -->
    <!-- STATISTIQUES CREATIONS -->
    <!-- ========================= -->
    <div class="row mb-4">

        <div class="col-md-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Créations</h5>
                    <p class="display-6"><?= $stats->getTotalCreations() ?></p>
                    <small class="text-muted">Total</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Avec image</h5>
                    <p class="display-6"><?= $stats->getCreationsWithPicture() ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Sans image</h5>
                    <p class="display-6"><?= $stats->getCreationsWithoutPicture() ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================= -->
    <!-- STATISTIQUES UTILISATEURS -->
    <!-- ========================= -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Utilisateurs</h5>
                    <p class="display-6"><?= $userStats['total'] ?></p>
                    <small class="text-muted">Total</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Administrateurs</h5>
                    <p class="display-6"><?= $userStats['admins'] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Utilisateurs</h5>
                    <p class="display-6"><?= $userStats['usersOnly'] ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================= -->
    <!-- ACTIONS RAPIDES -->
    <!-- ========================= -->
    <div class="mb-4">
        <h2 class="h4">Actions rapides</h2>
        <div class="d-flex flex-wrap gap-2 mt-3">
            <a href="/creations/new" class="btn btn-success">
                Ajouter une création
            </a>
            <a href="/creations" class="btn btn-outline-primary">
                Voir les créations
            </a>
            <a href="/admin/users" class="btn btn-outline-dark">
                Voir les utilisateurs
            </a>
            <a href="/" class="btn btn-outline-secondary">
                Retour au site
            </a>
        </div>
    </div>

    <!-- ========================= -->
    <!-- DERNIERES CREATIONS -->
    <!-- ========================= -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h4 mb-3">Dernières créations</h2>
            <?php if (empty($latestCreations)): ?>
                <p class="text-muted">Aucune création pour le moment.</p>
            <?php else: ?>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Titre</th>
                            <th>Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($latestCreations as $creation): ?>
                            <tr>
                                <td><?= $creation->getIdCreation() ?></td>
                                <td><?= htmlspecialchars($creation->getTitle()) ?></td>
                                <td>
                                    <?= $creation->getCreatedAt()?->format('d/m/Y H:i') ?>
                                </td>
                                <td class="text-end">
                                    <a href="/creations/<?= $creation->getIdCreation() ?>" class="btn btn-sm btn-outline-primary">
                                        Voir
                                    </a>
                                    <a href="/creations/<?= $creation->getIdCreation() ?>/edit" class="btn btn-sm btn-outline-warning">
                                        Modifier
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- ========================= -->
    <!-- DERNIERS UTILISATEURS -->
    <!-- ========================= -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h4 mb-3">Derniers utilisateurs</h2>
            <?php if (empty($latestUsers)): ?>
                <p class="text-muted">Aucun utilisateur enregistré.</p>
            <?php else: ?>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Prénom</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($latestUsers as $user): ?>
                            <tr>
                                <td><?= $user->getId() ?></td>
                                <td><?= htmlspecialchars($user->getFirstname()) ?></td>
                                <td><?= htmlspecialchars($user->getLastname()) ?></td>
                                <td><?= htmlspecialchars($user->getEmail()) ?></td>
                                <td>
                                    <?php if ($user->getRole() === 'ROLE_ADMIN'): ?>
                                        <span class="badge bg-dark">Admin</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Utilisateur</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="/admin/users/<?= $user->getId() ?>/edit"
                                        class="btn btn-sm btn-outline-warning">
                                        Modifier
                                    </a>
                                    <form method="post"
                                        action="/admin/users/<?= $user->getId() ?>/reset-password"
                                        style="display:inline">
                                        <input type="hidden"
                                            name="_token"
                                            value="<?= Csrf::token('reset_password_user_' . $user->getId()) ?>">
                                        <button class="btn btn-sm btn-outline-primary">
                                            Reset mdp
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>