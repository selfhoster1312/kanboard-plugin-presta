<div class="page-header">
    <h2><?= t('Remove a client') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this client: "%s"?', $client['name']) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'PrestaClientController',
        'remove',
        array('plugin' => 'Presta', 'client_id' => $client_id)
    ) ?>
</div>
