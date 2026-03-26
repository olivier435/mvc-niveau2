<?php

/** @var \App\Entities\Creation $creation */
$id = $creation->getIdCreation();
?>

<h1 class="h3 mb-3">Modifier la création</h1>

<?php
$action = '/creations/' . $id . '/edit';
$csrfId = 'edit_creation_' . $id;
$submitLabel = 'Enregistrer';
$cancelUrl = '/creations/' . $id;
?>

<?php
require VIEW_PATH . '/creation/_form.php';
?>