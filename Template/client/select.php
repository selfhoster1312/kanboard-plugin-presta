<?php
    // So we have the clients.json loaded as $clients
    // Now we want options that are ID->NAME
    // and values that are EMPTY (unless form was submitted)
?>
<b><?= t("Choose an existing client") ?></b>
<form method="post" action="<?= $this->url->href('PrestaClientController', 'select', array('plugin' => 'Presta', 'task_id' => $task_id)) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->select('client_id', $client_options, array(), $errors ? $errors : array()) ?>
    <?= $this->modal->submitButtons() ?>
</form>
<hr>
<b><?= $this->modal->small('plus', t('Create new client'), 'PrestaClientController', 'create', array('plugin' => 'Presta', 'task_id' => $task_id)) ?></b>

