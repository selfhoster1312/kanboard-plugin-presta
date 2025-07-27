<b><?= t('Create new offer') ?></b>
<form method="post" action="<?= $this->url->href('PrestaOfferController', 'create', array('plugin' => 'Presta', 'task_id' => $task_id ?? null)) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->label(t('Short name'), 'short_name') ?>
    <?= $this->form->text('short_name', $values, $errors) ?>
    <br>
    <?= $this->form->label(t('Description'), 'description') ?>
    <?= $this->form->textarea('description', $values, $errors) ?>
    <br>
    <?= $this->form->label(t('Price'), 'price') ?>
    <?= $this->form->number('price', $values, $errors) ?>
    
    <?= $this->modal->submitButtons() ?>
</form>
