<b><?= t('Create new client') ?></b>
<form method="post" action="<?= $this->url->href('PrestaClientController', 'create', array('plugin' => 'Presta', 'task_id' => $task_id)) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->label(t('Client name'), 'client_name') ?>
    <?= $this->form->text('client_name', $values, $errors) ?>
    <br>
    <?= $this->form->label(t('Client address'), 'client_address') ?>
    <?= $this->form->textarea('client_address', $values, $errors) ?>
    <?= $this->modal->submitButtons() ?>
</form>
