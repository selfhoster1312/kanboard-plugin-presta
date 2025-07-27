<b><?= t("Modify an offer") ?></b>
<form method="post" action="<?= $this->url->href('PrestaOfferController', 'task_update', array('plugin' => 'Presta', 'task_id' => $task_id, 'offer_uuid' => $offer_uuid)) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('offer_id', [ 'offer_id' => $offer_id ]); ?>
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


