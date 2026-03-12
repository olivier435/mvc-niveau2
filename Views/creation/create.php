<h1 class="h3 mb-3">Créer une création</h1>

<?php
$action = '/creations/new';
$csrfId = 'create_creation';
$submitLabel = 'Créer';
$cancelUrl = '/creations';

require VIEW_PATH . '/creation/_form.php';
?>