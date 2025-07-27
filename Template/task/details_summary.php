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
                    </li>
                    <li>
                        <strong><?= t('Discount') ?>:</strong> <?= $discount['amount'] ?? 0 ?>€
<?php if (isset($discount['reason'])) { ?>
                        <br>(<?= $discount['reason'] ?>)
<?php } ?>
                </ul>
            </div>
            <div class="task-summary-column">
                <ul class="no-bullet">
                    <li>
                        <strong><?= t('Status:') ?></strong>
                        <span>
                        <?php if ($task['is_active'] == 1): ?>
                            <?= t('open') ?>
                        <?php else: ?>
                            <?= t('closed') ?>
                        <?php endif ?>
                        </span>
                    </li>
                    <li>
                        <strong><?= t('Priority:') ?></strong> <span><?= $task['priority'] ?></span>
                    </li>
                    <?php if (! empty($task['reference'])): ?>
                        <li>
                            <strong><?= t('Reference:') ?></strong> <span><?= $this->task->renderReference($task) ?></span>
                        </li>
                    <?php endif ?>
                    <?php if (! empty($task['score'])): ?>
                        <li>
                            <strong><?= t('Complexity:') ?></strong> <span><?= $this->text->e($task['score']) ?></span>
                        </li>
                    <?php endif ?>

                    <?= $this->hook->render('template:task:details:first-column', array('task' => $task)) ?>
                </ul>
            </div>            <div class="task-summary-column">
                <ul class="no-bullet">
                    <?php if ($task['date_due']): ?>
                        <li>
                            <strong><?= t('Due date:') ?></strong>
                            <span><?= $this->dt->datetime($task['date_due']) ?></span>
                        </li>
                    <?php endif ?>
                    <li>
                        <strong><?= t('Created:') ?></strong>
                        <span><?= $this->dt->datetime($task['date_creation']) ?></span>
                    </li>
                    <li>
                        <strong><?= t('Modified:') ?></strong>
                        <span><?= $this->dt->datetime($task['date_modification']) ?></span>
                    </li>
                    <?php if ($task['date_completed']): ?>
                    <li>
                        <strong><?= t('Completed:') ?></strong>
                        <span><?= $this->dt->datetime($task['date_completed']) ?></span>
                    </li>
                    <?php endif ?>
                    <?php if ($task['date_moved']): ?>
                    <li>
                        <strong><?= t('Moved:') ?></strong>
                        <span><?= $this->dt->datetime($task['date_moved']) ?></span>
                    </li>
                    <?php endif ?>

                    <?= $this->hook->render('template:task:details:fourth-column', array('task' => $task)) ?>
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
