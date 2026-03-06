<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$currentPath = rtrim($currentPath, '/') ?: '/';

$isHome = ($currentPath === '/');
$bodyClass = $isHome ? 'is-home' : 'is-page';

$pageTitle = $pageTitle ?? 'Créations';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Roboto+Slab:wght@100;200;300;400;500;600;700;800;900&family=Source+Serif+Pro:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700;1,900&display=swap%22 rel=" stylesheet">

    <!-- CSS vendor local -->
    <link href="/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <!-- CSS app (si tu as un fichier) -->
    <!-- <link href="/assets/css/app.css" rel="stylesheet"> -->
</head>

<body class="<?= htmlspecialchars($bodyClass) ?>">
    <?php require VIEW_PATH . '/partials/_nav.php'; ?>

    <main class="container py-4">
        <?php require VIEW_PATH . '/layout/_flash.php'; ?>
        <?= $content ?>
    </main>

    <?php require VIEW_PATH . '/partials/_footer.php'; ?>
</body>

<!-- Vendor JS Files -->
<script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</html>