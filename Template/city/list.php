<b><?= $this->modal->small('plus', t('Create new city'), 'PrestaCityController', 'create', array('plugin' => 'Presta', 'task_id' => isset($task_id) ? $task_id : null)) ?></b>
<h2><?= t('Cities list') ?></h2>
<ul>
<?php foreach($cities as $city) { ?>
<li>
    <?= $this->modal->confirm('trash-o', null, 'PrestaCityController', 'confirm', array('plugin' => 'Presta', 'city_name' => $city)) ?>
    <?= $city ?>
</li>
<?php } ?>
</ul>