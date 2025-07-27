<b><?= t('Set the discount (€)') ?></b>
<form method="post" action="<?= $this->url->href('PrestaTaskController', 'discount', array('plugin' => 'Presta', 'task_id' => $task_id)) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->label(t('Discount amount'), 'discount_amount') ?>
    <?= $this->form->number('discount_amount', $values, $errors) ?>

    <?= $this->form->label(t('Discount reason'), 'discount_reason') ?>
    <?= $this->form->text('discount_reason', $values, $errors) ?>
    <?= $this->modal->submitButtons() ?>
</form>
