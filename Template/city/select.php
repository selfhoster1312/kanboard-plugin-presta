<?php
    // So we have the clients.json loaded as $clients
    // Now we want options that are ID->NAME
    // and values that are EMPTY (unless form was submitted)
?>
<b><?= t("Choose an existing city") ?></b>
<form method="post" action="<?= $this->url->href('PrestaCityController', 'select', array('plugin' => 'Presta', 'task_id' => $task_id)) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->select('city_name', $city_options, array(), $errors ? $errors : array()) ?>
    <?= $this->modal->submitButtons() ?>
</form>
<hr>
<b><?= $this->modal->small('plus', t('Create new city'), 'PrestaCityController', 'create', array('plugin' => 'Presta', 'task_id' => $task_id)) ?></b>

