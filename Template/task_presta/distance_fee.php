<b><?= t('Set the distance fee (€)') ?></b>
<form method="post" action="<?= $this->url->href('PrestaTaskController', 'distance_fee', array('plugin' => 'Presta', 'task_id' => $task_id)) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->label(t('Distance fee'), 'distance_fee') ?>
    <?= $this->form->number('distance_fee', $values, $errors) ?>
    <?= $this->modal->submitButtons() ?>
</form>
