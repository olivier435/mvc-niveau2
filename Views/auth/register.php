<h1 class="mb-4"><?= htmlspecialchars($pageTitle ?? 'Inscription') ?></h1>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<form method="post" action="/register">
    <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Core\Csrf::token('register')) ?>">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Prénom</label>
            <input
                type="text"
                name="firstname"
                class="form-control"
                value="<?= htmlspecialchars($this->old($old, 'firstname')) ?>"
                required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Nom</label>
            <input
                type="text"
                name="lastname"
                class="form-control"
                value="<?= htmlspecialchars($this->old($old, 'lastname')) ?>"
                required>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input
            type="email"
            name="email"
            class="form-control"
            value="<?= htmlspecialchars($this->old($old, 'email')) ?>"
            required>
    </div>
    <div class="mb-3">
        <label class="form-label">Mot de passe</label>
        <input
            type="password"
            name="password"
            class="form-control"
            required>
    </div>
    <div class="mb-3">
        <label class="form-label">Confirmation du mot de passe</label>
        <input
            type="password"
            name="password_confirm"
            class="form-control"
            required>
    </div>
    <hr>
    <div class="mb-3">
        <label class="form-label">Adresse</label>
        <input
            type="text"
            name="address"
            class="form-control"
            value="<?= htmlspecialchars($this->old($old, 'address')) ?>">
    </div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Code postal</label>
            <input
                type="text"
                name="postal_code"
                class="form-control"
                value="<?= htmlspecialchars($this->old($old, 'postal_code')) ?>">
        </div>
        <div class="col-md-8 mb-3">
            <label class="form-label">Ville</label>
            <input
                type="text"
                name="city"
                class="form-control"
                value="<?= htmlspecialchars($this->old($old, 'city')) ?>">
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Téléphone</label>
        <input
            type="text"
            name="phone"
            class="form-control"
            value="<?= htmlspecialchars($this->old($old, 'phone')) ?>">
    </div>
    <button class="btn btn-success">
        Créer mon compte
    </button>
</form>
<p class="mt-3">
    Déjà un compte ?
    <a href="/login">Se connecter</a>
</p>