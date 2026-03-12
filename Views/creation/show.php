<?php

use App\Core\Csrf;

/** @var \App\Entities\Creation $creation */
$csrfId = 'delete_creation_' . $creation->getIdCreation();
?>
<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0"><?= htmlspecialchars($creation->getTitle()) ?></h1>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="/creations">Retour</a>
        <a class="btn btn-primary" href="/creations/<?= $creation->getIdCreation() ?>/edit">Modifier</a>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <p class="mb-0"><?= nl2br(htmlspecialchars($creation->getDescription())) ?></p>
    </div>
</div>
<form class="mt-3"
    method="post"
    action="/creations/<?= $creation->getIdCreation() ?>/delete"
    onsubmit="return confirm('Supprimer cette création ?');">
    <input type="hidden" name="_token" value="<?= htmlspecialchars(Csrf::token($csrfId)) ?>">
    <button class="btn btn-danger">
        <i class="bi bi-trash"></i> Supprimer
    </button>
</form>