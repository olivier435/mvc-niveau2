<?php
 
declare(strict_types=1);
$title = 'Page introuvable';
?>
<!doctype html>
<html lang="fr">
 
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
</head>
 
<body class="bg-light">
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-9 col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="fs-2 text-warning">
                                <i class="bi bi-search"></i>
                            </div>
                            <div>
                                <h1 class="h4 mb-1">Page introuvable (404)</h1>
                                <p class="text-muted mb-0">La ressource demandée n'existe pas ou n'est plus disponible.</p>
                            </div>
                        </div>
                        <div class="alert alert-warning mb-3" role="alert">
                            La page que vous recherchez est introuvable.
                        </div>
                        <?php if (defined('APP_DEBUG') && APP_DEBUG && isset($errorMessage)): ?>
                            <div class="alert alert-secondary mb-3" role="alert">
                                <strong>Détail :</strong>
                                <?= htmlspecialchars($errorMessage) ?>
                            </div>
                        <?php endif; ?>
                        <div class="d-flex gap-2">
                            <a class="btn btn-primary" href="/">Retour à l'accueil</a>
                            <a class="btn btn-outline-secondary" href="javascript:history.back()">Page précédente</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
 
</html>