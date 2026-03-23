<h1 class="mb-4"><?= htmlspecialchars($pageTitle ?? 'Réinitialiser le mot de passe') ?></h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="/reset-password">
    <input
        type="hidden"
        name="_token"
        value="<?= htmlspecialchars(\App\Core\Csrf::token('reset_password')) ?>">

    <input
        type="hidden"
        name="selector"
        value="<?= htmlspecialchars($selector ?? '') ?>">

    <input
        type="hidden"
        name="token"
        value="<?= htmlspecialchars($token ?? '') ?>">

    <div class="mb-3">
        <label for="password" class="form-label">Nouveau mot de passe</label>
        <input
            type="password"
            id="password"
            name="password"
            class="form-control"
            required>
    </div>

    <div class="mb-3">
        <label for="password_confirm" class="form-label">Confirmation du mot de passe</label>
        <input
            type="password"
            id="password_confirm"
            name="password_confirm"
            class="form-control"
            required>
    </div>

    <button class="btn btn-success">
        Réinitialiser le mot de passe
    </button>
</form>

<p class="mt-3">
    <a href="/login">Retour à la connexion</a>
</p>