<div class="page-header">
    <h2><?= t('Remove an offer') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this offer?') ?>
    </p>

    <?= $this->modal->confirmButtons(
        'PrestaOfferController',
        'task_delete',
        array('plugin' => 'Presta', 'task_id' => $task_id, 'offer_uuid' => $offer_uuid)
    ) ?>
</div>
