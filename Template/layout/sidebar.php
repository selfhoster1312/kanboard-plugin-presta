<div class="sidebar">
    <h2>Presta</h2>
    <ul>
        <li <?= $this->app->checkMenuSelection('PrestaClientController', 'list', 'Presta') ?>>
            <?= $this->url->link(t('Clients'), 'PrestaClientController', 'list', [ 'plugin' => 'Presta' ]) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('PrestaCityController', 'list', 'Presta') ?>>
            <?= $this->url->link(t('Cities'), 'PrestaCityController', 'list', [ 'plugin' => 'Presta' ]) ?>
        </li>
    </ul>
</div>
