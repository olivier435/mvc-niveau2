<?php

declare(strict_types=1);

use App\Core\Csrf;

/** @var string $pageTitle */
/** @var \App\Entities\User[] $users */
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1"><?= htmlspecialchars($pageTitle ?? 'Gestion des utilisateurs') ?></h1>
        <p class="text-muted mb-0">
            Liste des utilisateurs de l'application et actions d'administration.
        </p>
    </div>

    <div class="d-flex gap-2">
        <a href="/admin" class="btn btn-outline-secondary">Retour au dashboard</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <?php if (empty($users)): ?>
            <div class="alert alert-info mb-0">
                Aucun utilisateur enregistré pour le moment.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Prénom</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Date d'inscription</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= (int) $user->getId() ?></td>
                                <td><?= htmlspecialchars($user->getFirstname()) ?></td>
                                <td><?= htmlspecialchars($user->getLastname()) ?></td>
                                <td><?= htmlspecialchars($user->getEmail()) ?></td>
                                <td>
                                    <?php if ($user->getRole() === 'ROLE_ADMIN'): ?>
                                        <span class="badge text-bg-dark">Admin</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-secondary">Utilisateur</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user->getCreatedAt()): ?>
                                        <?= htmlspecialchars($user->getCreatedAt()->format('d/m/Y H:i')) ?>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2 flex-wrap justify-content-end">
                                        <a
                                            href="/admin/users/<?= (int) $user->getId() ?>/edit"
                                            class="btn btn-sm btn-outline-warning">
                                            Modifier
                                        </a>

                                        <form
                                            action="/admin/users/<?= (int) $user->getId() ?>/reset-password"
                                            method="post"
                                            class="d-inline">
                                            <input
                                                type="hidden"
                                                name="_token"
                                                value="<?= htmlspecialchars(Csrf::token('reset_password_user_' . $user->getId())) ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                Réinitialiser le mot de passe
                                            </button>
                                        </form>

                                        <form
                                            action="/admin/users/<?= (int) $user->getId() ?>/delete"
                                            method="post"
                                            class="d-inline"
                                            onsubmit="return confirm('Confirmer la suppression de cet utilisateur ?');">
                                            <input
                                                type="hidden"
                                                name="_token"
                                                value="<?= htmlspecialchars(Csrf::token('delete_user_' . $user->getId())) ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>