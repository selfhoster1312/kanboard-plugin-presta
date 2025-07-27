<b><?= $this->modal->small('plus', 'Créer un nouveau client', 'PrestaClientController', 'create', array('plugin' => 'Presta', 'task_id' => isset($task_id) ? $task_id : null)) ?></b>
<?php
// TODO: filter by city
?>
<h2><?= t('Client list') ?></h2>
<ul>
<?php foreach($clients as $client_id => $client) { ?>
<li>
    <?= $this->modal->small('cog', null, 'PrestaClientController', 'edit', array('plugin' => 'Presta', 'client_id' => $client_id)) ?>
    <?= $this->modal->confirm('trash-o', null, 'PrestaClientController', 'confirm', array('plugin' => 'Presta', 'client_id' => $client_id)) ?>
    <?= $client["name"] ?>
</li>
<?php } ?>
</ul>