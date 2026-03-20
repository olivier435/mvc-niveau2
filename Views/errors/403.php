<?php
 
declare(strict_types=1);
$title = 'Accès interdit';
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
                            <div class="fs-2 text-danger">
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>
                            <div>
                                <h1 class="h4 mb-1">Accès interdit (403)</h1>
                                <p class="text-muted mb-0">Vous n'avez pas les droits nécessaires pour accéder à cette ressource.</p>
                            </div>
                        </div>
                        <div class="alert alert-danger mb-3" role="alert">
                            Accès refusé. Vous n'êtes pas autorisé à consulter cette page.
                        </div>
                        <?php if (defined('APP_DEBUG') && APP_DEBUG && isset($errorMessage) && $errorMessage !== ''): ?>
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
 