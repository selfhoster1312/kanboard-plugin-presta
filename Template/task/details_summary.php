    <div class="task-summary-container color-<?= $task['color_id'] ?>">
        <div class="task-summary-columns">
            <div class="task-summary-column">
                <ul class="no-bullet">
                    <li>
<?php if (isset($client)) { ?>
                        <strong><?= t('Client')?>:</strong> <?= $client["name"] ?>
                        <?= $this->modal->small('cog', null, 'PrestaClientController', 'select', array('plugin' => 'Presta', 'task_id' => $task["id"])) ?>
        <?php } else { ?>
                        <strong><?= t('No associated client') ?></strong>
                        <br><?= $this->modal->small('user', t('Choose an existing client'), 'PrestaClientController', 'select', array('plugin' => 'Presta', 'task_id' => $task['id'])) ?>
<?php } ?>
                    </li>
                    <li>
<?php if (isset($city)) { ?>
                        <strong><?= t('City') ?>:</strong> <?= $city ?>
                        <?= $this->modal->small('cog', null, 'PrestaCityController', 'select', array('plugin' => 'Presta', 'task_id' => $task["id"])) ?>
<?php } else { ?>
                        <strong><?= t('No associated city') ?></strong>
                        <br><?= $this->modal->small('home', t('Choose an existing city'), 'PrestaCityController', 'select', array('plugin' => 'Presta', 'task_id' => $task['id'])) ?>
<?php } ?>
                    </li>
                    <li>
                        <strong><?= t('Distance fee') ?>:</strong> <?= $distance_fee ?>€
                        <?= $this->modal->small('cog', null, 'PrestaTaskController', 'distance_fee', array('plugin' => 'Presta', 'task_id' => $task["id"])) ?>
                    </li>
                    <li>
                        <strong><?= t('Discount') ?>:</strong> <?= $discount['amount'] ?? 0 ?>€
                        <?= $this->modal->small('cog', null, 'PrestaTaskController', 'discount', array('plugin' => 'Presta', 'task_id' => $task["id"])) ?>
<?php if (isset($discount['reason'])) { ?>
                        <br>(<?= $discount['reason'] ?>)
<?php } ?>
                   <?= $this->hook->render('template:task:details:first-column', array('task' => $task)) ?>
                </ul>
            </div>
            <div class="task-summary-column">
                <ul class="no-bullet">
                    <li><strong><?= t('Total price') ?>: <?= $total_price ?></strong></li>
<?php if (!empty($offers)) { foreach ($offers as $short_name => $offers_list) { ?>
                    <li><hr></li>
                    <li>
                        <strong>Presta: <?= $short_name ?></strong>
                        <ul>
    <?php foreach($offers_list as $offer) { ?>
                            <li>
                                <?= $this->modal->confirm('trash', null, 'PrestaOfferController', 'task_confirm', array('plugin' => 'Presta', 'task_id' => $task["id"], 'offer_uuid' => $offer['uuid'])) ?>
                                <?= $this->modal->small('cog', null, 'PrestaOfferController', 'task_edit', array('plugin' => 'Presta', 'task_id' => $task["id"], 'offer_uuid' => $offer['uuid'])) ?>
                                <strong><?= $offer['date'] ?></strong> (<?= $offer["start"] ?>-<?= $offer["end"] ?>)
                            </li>
    <?php } ?>
                        </ul>
                    </li>
<?php } } else { ?>
                    <li>
                        <strong><?= t('No associated offer') ?></strong>
                        <br><?= $this->modal->small('euro', t('Add an offer'), 'PrestaOfferController', 'select', array('plugin' => 'Presta', 'task_id' => $task['id'])) ?>
                    </li>
<?php } ?>
                   <?= $this->hook->render('template:task:details:second-column', array('task' => $task)) ?>
                </ul>
            </div>
            <div class="task-summary-column">
                <ul class="no-bullet">
                    <li>
                        <strong><?= t('Created:') ?></strong>
                        <span><?= $this->dt->datetime($task['date_creation']) ?></span>
                    </li>
                    <li>
                        <strong><?= t('Modified:') ?></strong>
                        <span><?= $this->dt->datetime($task['date_modification']) ?></span>
                    </li>

                    <?= $this->hook->render('template:task:details:third-column', array('task' => $task)) ?>
                </ul>
            </div>
        </div>
        <?php if (! empty($tags)): ?>
            <div class="task-tags">
                <ul>
                    <?php foreach ($tags as $tag): ?>
                        <li class="task-tag <?= $tag['color_id'] ? "color-{$tag['color_id']}" : '' ?>"><?= $this->text->e($tag['name']) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif ?>
    </div>
