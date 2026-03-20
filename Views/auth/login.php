<h1 class="mb-4"><?= htmlspecialchars($pageTitle ?? 'Connexion') ?></h1>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<form method="post" action="/login">
    <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Core\Csrf::token('login')) ?>">
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
    <div class="mb-3">
        <label for="password" class="form-label">Mot de passe</label>
        <input
            type="password"
            id="password"
            name="password"
            class="form-control"
            required>
    </div>
    <button class="btn btn-primary">
        Se connecter
    </button>
</form>
<p class="mt-3">
    Pas encore de compte ?
    <a href="/register">Créer un compte</a>
</p>