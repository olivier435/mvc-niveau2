<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$flashes = $_SESSION['_flash'] ?? [];
unset($_SESSION['_flash']);
$map = [
    'error' => 'danger',
    'success' => 'success',
    'warning' => 'warning',
    'info' => 'info',
];
?>
<?php if (!empty($flashes)): ?>
    <div class="mb-3">
        <?php foreach ($flashes as $flash): ?>
            <?php
            $type = (string)($flash['type'] ?? 'info');
            $msg = (string)($flash['msg'] ?? '');
            if ($msg === '') continue;
            $bsType = $map[$type] ?? 'info';
            ?>
            <div class="alert alert-<?= htmlspecialchars($bsType) ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($msg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>