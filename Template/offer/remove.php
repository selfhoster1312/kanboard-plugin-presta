<div class="page-header">
    <h2><?= t('Remove an offer') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this offer: "%s"?', $offer['short_name']) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'PrestaOfferController',
        'remove',
        array('plugin' => 'Presta', 'offer_id' => $offer_id)
    ) ?>
</div>
