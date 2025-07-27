<b><?= t('Create new city') ?></b>
<form method="post" action="<?= $this->url->href('PrestaCityController', 'create', array('plugin' => 'Presta', 'task_id' => $task_id)) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->label(t('City name'), 'city_name') ?>
    <?= $this->form->text('city_name', $values, $errors) ?>
    <?= $this->modal->submitButtons() ?>
</form>
