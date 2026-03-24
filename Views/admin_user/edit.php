<?php

declare(strict_types=1);

use App\Core\Csrf;

/** @var string $pageTitle */
/** @var \App\Entities\User $user */
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1"><?= htmlspecialchars($pageTitle ?? 'Modifier un utilisateur') ?></h1>
        <p class="text-muted mb-0">
            Modification des données non sensibles de l'utilisateur.
        </p>
    </div>

    <div>
        <a href="/admin/users" class="btn btn-outline-secondary">Retour à la liste</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form method="post" action="/admin/users/<?= (int) $user->getId() ?>/edit">
            <input
                type="hidden"
                name="_token"
                value="<?= htmlspecialchars(Csrf::token('edit_user_' . $user->getId())) ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="firstname" class="form-label">Prénom</label>
                    <input
                        type="text"
                        id="firstname"
                        name="firstname"
                        class="form-control"
                        required
                        value="<?= htmlspecialchars($user->getFirstname()) ?>">
                </div>

                <div class="col-md-6">
                    <label for="lastname" class="form-label">Nom</label>
                    <input
                        type="text"
                        id="lastname"
                        name="lastname"
                        class="form-control"
                        required
                        value="<?= htmlspecialchars($user->getLastname()) ?>">
                </div>

                <div class="col-md-8">
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        required
                        value="<?= htmlspecialchars($user->getEmail()) ?>">
                </div>

                <div class="col-md-4">
                    <label for="role" class="form-label">Rôle</label>
                    <select id="role" name="role" class="form-select">
                        <option value="ROLE_USER" <?= $user->getRole() === 'ROLE_USER' ? 'selected' : '' ?>>
                            Utilisateur
                        </option>
                        <option value="ROLE_ADMIN" <?= $user->getRole() === 'ROLE_ADMIN' ? 'selected' : '' ?>>
                            Administrateur
                        </option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="/admin/users" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>