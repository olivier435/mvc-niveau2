<h1 class="mb-4"><?= htmlspecialchars($pageTitle ?? 'Mot de passe oublié') ?></h1>

<p class="text-muted">
    Saisissez votre adresse email. Si un compte correspond, un lien de réinitialisation sera généré.
</p>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="/forgot-password">
    <input
        type="hidden"
        name="_token"
        value="<?= htmlspecialchars(\App\Core\Csrf::token('forgot_password')) ?>">

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            class="form-control"
            value="<?= htmlspecialchars($this->old($old, 'email')) ?>"
            required>
    </div>

    <button class="btn btn-primary">
        Envoyer le lien de réinitialisation
    </button>
</form>

<p class="mt-3">
    <a href="/login">Retour à la connexion</a>
</p>