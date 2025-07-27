<b><?= t('Edit client n° %s: %s', $client_id, $client_name) ?></b>
<form method="post" action="<?= $this->url->href('PrestaClientController', 'edit', array('plugin' => 'Presta', 'client_id' => $client_id)) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->label(t('Client name'), 'client_name') ?>
    <?= $this->form->text('client_name', [ 'client_name' => $client_name ]) ?>
    <br>
    <?= $this->form->label(t('Client address'), 'client_address') ?>
    <?= $this->form->textarea('client_address', [ 'client_address' => $client_address ]) ?>
    <?= $this->modal->submitButtons() ?>
</form>
