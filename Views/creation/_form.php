<?php

use App\Core\Csrf;

$old = $old ?? [
    'title' => '',
    'description' => '',
    'picture' => '',
];
$errors = $errors ?? [];
?>
<form method="post"
    action="<?= htmlspecialchars($action) ?>"
    enctype="multipart/form-data"
    class="card shadow-sm">
    <div class="card-body">
        <input type="hidden" name="_token" value="<?= htmlspecialchars(Csrf::token($csrfId)) ?>">
        <div class="mb-3">
            <label class="form-label" for="title">Titre</label>
            <input
                class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>"
                id="title"
                name="title"
                type="text"
                value="<?= $this->old($old, 'title') ?>">
            <?php if (isset($errors['title'])): ?>
                <div class="invalid-feedback">
                    <?= htmlspecialchars($errors['title']) ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label class="form-label" for="description">Description</label>
            <textarea
                class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>"
                id="description"
                name="description"
                rows="5"><?= $this->old($old, 'description') ?></textarea>
            <?php if (isset($errors['description'])): ?>
                <div class="invalid-feedback">
                    <?= htmlspecialchars($errors['description']) ?>
                </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($old['picture'])): ?>
            <div class="mb-3">
                <div class="text-muted mb-2">Image actuelle :</div>
                <img
                    class="img-fluid rounded border"
                    style="max-height: 240px;"
                    src="<?= htmlspecialchars(CREATIONS_PUBLIC_PREFIX . '/' . $old['picture']) ?>"
                    alt="Image actuelle">
            </div>
        <?php endif; ?>
        <div class="mb-3">
            <label class="form-label" for="picture">Image</label>
            <input
                class="form-control <?= isset($errors['picture']) ? 'is-invalid' : '' ?>"
                type="file"
                id="picture"
                name="picture"
                accept="image/jpeg,image/png,image/webp">
            <?php if (isset($errors['picture'])): ?>
                <div class="invalid-feedback">
                    <?= htmlspecialchars($errors['picture']) ?>
                </div>
            <?php endif; ?>
            <div class="form-text">
                Formats autorisés : JPG, PNG, WebP — 2 Mo maximum.
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">
                <?= htmlspecialchars($submitLabel) ?>
            </button>
            <a class="btn btn-outline-secondary" href="<?= htmlspecialchars($cancelUrl) ?>">
                Annuler
            </a>
        </div>
    </div>
</form>