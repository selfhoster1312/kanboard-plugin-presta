<b><?= $this->modal->small('plus', t('Create new offer'), 'PrestaOfferController', 'create', array('plugin' => 'Presta', 'task_id' => isset($task_id) ? $task_id : null)) ?></b>
<h2><?= t('Offers list') ?></h2>
<ul>
<?php foreach($offers as $offer_id => $offer) { ?>
<li>
    <?= $this->modal->confirm('cog', null, 'PrestaOfferController', 'edit', array('plugin' => 'Presta', 'offer_id' => $offer_id)) ?>
    <?= $this->modal->confirm('trash-o', null, 'PrestaOfferController', 'confirm', array('plugin' => 'Presta', 'offer_id' => $offer_id)) ?>
    <?= $offer['short_name'] ?> (<?= $offer['price'] ?>€)
</li>
<?php } ?>
</ul>