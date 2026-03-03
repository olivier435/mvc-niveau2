<h1>404</h1>
    <p>La page demandée n'existe pas.</p>
    <?php if (defined('APP_DEBUG') && APP_DEBUG && isset($errorMessage)): ?>
        <p style="color:#a00"><small><?= htmlspecialchars($errorMessage)
                                        ?></small></p>
    <?php endif; ?>
    <p><a href="/">Retour à l'accueil</a></p>