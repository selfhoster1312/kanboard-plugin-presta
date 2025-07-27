<b><?= t('Edit offer n° %s: %s', $offer_id, $offer['short_name']) ?></b>
<form method="post" action="<?= $this->url->href('PrestaOfferController', 'edit', array('plugin' => 'Presta', 'offer_id' => $offer_id)) ?>" autocomplete="off">
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
