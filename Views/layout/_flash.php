<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
 
$flashes = $_SESSION['_flashes'] ?? [];
unset($_SESSION['_flashes']);
 
$map = [
    'error'   => 'danger',
    'success' => 'success',
    'warning' => 'warning',
    'info'    => 'info',
];
?>
 
<?php if (!empty($flashes)): ?>
    <div class="mb-3">
        <?php foreach ($flashes as $type => $messages): ?>
            <?php
            $bsType = $map[$type] ?? 'info';
            $messages = is_array($messages) ? $messages : [];
            ?>
            <?php foreach ($messages as $msg): ?>
                <?php if ((string) $msg === '') continue; ?>
                <div class="alert alert-<?= htmlspecialchars($bsType) ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars((string) $msg) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>