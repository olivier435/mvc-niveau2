<?php

/** @var \App\Entities\Creation[] $creations */
?>
<div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0">Liste des créations</h1>
    <a class="btn btn-primary" href="/creations/new">
        <i class="bi bi-plus-lg"></i> Nouvelle création
    </a>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <label for="creation-search-input" class="form-label fw-semibold">
            Rechercher une création
        </label>

        <div class="position-relative">
            <input
                type="search"
                id="creation-search-input"
                class="form-control"
                name="q"
                placeholder="Tapez au moins 2 caractères..."
                autocomplete="off"
                aria-autocomplete="list"
                aria-controls="creation-search-results"
                aria-expanded="false">

            <div
                id="creation-search-results"
                class="list-group position-absolute w-100 shadow-sm d-none"
                role="listbox"
                aria-label="Suggestions de créations"
                style="z-index: 1000;"></div>
        </div>

        <div class="form-text">
            Commencez à saisir le titre d'une création pour afficher des suggestions.
        </div>
    </div>
</div>

<?php if (empty($creations)): ?>
    <div class="alert alert-info">Aucune création pour le moment.</div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($creations as $c): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($c->getPicture())): ?>
                        <img class="img-fluid rounded border"
                            style="max-height: 240px;"
                            src="<?= htmlspecialchars(CREATIONS_PUBLIC_PREFIX . '/' . $c->getPicture()) ?>"
                            alt="">
                    <?php endif; ?>
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
    <?php if ($pages > 1): ?>
        <?php
        // Fenêtre de pagination (nombre de liens affichés)
        $window = 5; // 5 pages max affichées
        $half = intdiv($window, 2);

        // Début / fin de fenêtre autour de la page courante
        $start = max(1, $page - $half);
        $end = min($pages, $page + $half);

        // Ajustements pour toujours afficher $window pages si possible
        $currentCount = $end - $start + 1;

        if ($currentCount < $window) {
            $missing = $window - $currentCount;
            // On essaie d'étendre à gauche

            $start = max(1, $start - $missing);
            $currentCount = $end - $start + 1;

            // Si on n'a pas réussi assez, on étend à droite
            if ($currentCount < $window) {
                $end = min($pages, $end + ($window - $currentCount));
            }
        }
        ?>
        <nav aria-label="Pagination des créations">
            <ul class="pagination justify-content-center mt-4">
                <!-- Previous -->
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link"
                        href="<?= $page > 1 ? '/creations?page=' . ($page - 1) : '#' ?>"
                        aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <!-- Always show page 1 -->
                <li class="page-item <?= $page === 1 ? 'active' : '' ?>">
                    <a class="page-link" href="/creations?page=1">1</a>
                </li>
                <!-- Dots if start is far from 2 -->
                <?php if ($start > 2): ?>
                    <li class="page-item disabled">
                        <span class="page-link">…</span>
                    </li>
                <?php endif; ?>
                <!-- Window pages (avoid duplicates of 1 and last) -->
                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <?php if ($i === 1 || $i === $pages) continue; ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="/creations?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <!-- Dots if end is far from last-1 -->
                <?php if ($end < $pages - 1): ?>
                    <li class="page-item disabled">
                        <span class="page-link">…</span>
                    </li>
                <?php endif; ?>
                <!-- Always show last page (if > 1) -->
                <?php if ($pages > 1): ?>
                    <li class="page-item <?= $page === $pages ? 'active' : '' ?>">
                        <a class="page-link" href="/creations?page=<?= $pages ?>"><?= $pages ?></a>
                    </li>
                <?php endif; ?>
                <!-- Next -->
                <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>">
                    <a class="page-link"
                        href="<?= $page < $pages ? '/creations?page=' . ($page + 1) : '#' ?>"
                        aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
<?php endif; ?>