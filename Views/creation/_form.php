<?php

use App\Core\Csrf;

$old = $old ?? ['title' => '', 'description' => '', 'picture' => ''];
$error = $error ?? null;
?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<form method="post" action="<?= htmlspecialchars($action) ?>" class="card shadow-sm">
    <div class="card-body">
        <input type="hidden" name="_token" value="<?= htmlspecialchars(Csrf::token($csrfId)) ?>">
        <div class="mb-3">
            <label class="form-label" for="title">Titre</label>
            <input class="form-control"
                id="title"
                name="title"
                required
                value="<?= htmlspecialchars($old['title'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label" for="description">Description</label>
            <textarea class="form-control"
                id="description"
                name="description"
                rows="5"
                required><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label" for="picture">Image (texte pour l'instant)</label>
            <input class="form-control"
                id="picture"
                name="picture"
                value="<?= htmlspecialchars($old['picture'] ?? '') ?>">
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary"><?= htmlspecialchars($submitLabel) ?></button>
            <a class="btn btn-outline-secondary" href="<?= htmlspecialchars($cancelUrl) ?>">Annuler</a>
        </div>
    </div>
</form>