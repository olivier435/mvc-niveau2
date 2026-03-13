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
<?php if (!empty($old['picture'])): ?>
    <div class="mb-3">
        <div class="text-muted mb-2">image actuelle :</div>
        <img class="img-fluid rounded border"
            style="max-height: 240px;"
            src="<?= htmlspecialchars(CREATIONS_PUBLIC_PREFIX . '/' . $old['picture']) ?>"
            alt="">
    </div>

<?php endif; ?>
<?php
require VIEW_PATH . '/creation/_form.php';
?>