<div class="page-header">
    <h2><?= t('Remove a city') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this city: "%s"?', $city_name) ?>
    </p>

    <?= $this->modal->confirmButtons(
        'PrestaCityController',
        'remove',
        array('plugin' => 'Presta', 'city_name' => $city_name)
    ) ?>
</div>
