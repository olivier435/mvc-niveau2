<?php

/** @var \App\Entities\Creation[] $creations */
?>
<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0">Liste des créations</h1>
    <a class="btn btn-primary" href="/creations/new">
        <i class="bi bi-plus-lg"></i> Nouvelle création
    </a>
</div>
<?php if (empty($creations)): ?>
    <div class="alert alert-info">Aucune création pour le moment.</div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($creations as $c): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="h5"><?= htmlspecialchars($c->getTitle()) ?></h2>
                        <p class="text-muted mb-0">
                            <?= nl2br(htmlspecialchars(mb_strimwidth($c->getDescription(), 0, 120, '…'))) ?>
                        </p>
                    </div>
                    <div class="card-footer bg-white border-0 d-flex gap-2">
                        <a class="btn btn-outline-primary btn-sm" href="/creations/<?= $c->getIdCreation() ?>">
                            Voir
                        </a>
                        <a class="btn btn-outline-secondary btn-sm" href="/creations/<?= $c->getIdCreation() ?>/edit">
                            Modifier
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>