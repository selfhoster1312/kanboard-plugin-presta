<b><?= t("Add an offer") ?></b>
<form method="post" action="<?= $this->url->href('PrestaOfferController', 'select', array('plugin' => 'Presta', 'task_id' => $task_id)) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->select('offer_id', $offer_options, array(), $errors) ?>
    <br>
    <?= $this->form->date(t('Date of the service'), 'date', $values) ?>
    <br>
    <?= $this->form->label(t('Time to start'), 'start') ?>
    <?= $this->form->input('time', 'start', $values) ?>
    <br>
    <?= $this->form->label(t('Time to end'), 'end') ?>
    <?= $this->form->input('time', 'end', $values) ?>

    <?= $this->modal->submitButtons() ?>
</form>


