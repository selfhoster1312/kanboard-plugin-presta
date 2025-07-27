<?php
    $clients = new Kanboard\Plugin\Presta\Model\PrestaClientModel();
    $presta_task = new Kanboard\Plugin\Presta\Model\PrestaTaskModel($task["id"]);
    $client = $clients->get($presta_task->getClient());
?>
<section id="task-summary">
    <h2><?= $this->text->e($task['title']) ?></h2>

    <?= $this->hook->render('template:task:details:top', array('task' => $task)) ?>

    <div class="task-summary-container color-<?= $task['color_id'] ?>">
        <div class="task-summary-columns">
            <div class="task-summary-column">
                <ul class="no-bullet">
<?php if (isset($client)) { ?>
                    <li>
                        <strong>Client:</strong> <?= $client["name"] ?>
                        <?= $this->modal->small('cog', null, 'PrestaClientController', 'select', array('plugin' => 'Presta', 'task_id' => $task["id"])) ?>
        <?php } else { ?>
                        <strong>Aucun client associé</strong>
                        <br><?= $this->modal->small('user', t('Choose an existing client'), 'PrestaClientController', 'select', array('plugin' => 'Presta', 'task_id' => $task['id'])) ?>
                    </li>
<?php } ?>

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
                    <?php if ($project['is_public']): ?>
                    <li>
                        <small>
                            <?= $this->url->icon('external-link', t('Public link'), 'TaskViewController', 'readonly', array('task_id' => $task['id'], 'token' => $project['token']), false, '', '', true) ?>
                        </small>
                    </li>
                    <?php endif ?>
                    <?php if ($project['is_public'] && !$editable): ?>
                    <li>
                        <small>
                            <?= $this->url->icon('th', t('Back to the board'), 'BoardViewController', 'readonly', array('token' => $project['token'])) ?>
                        </small>
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
                        <strong><?= t('Started:') ?></strong>
                        <?php if ($task['date_started']): ?>
                            <span><?= $this->dt->datetime($task['date_started']) ?></span>
                        <?php elseif ($editable): ?>
                            <span><?= $this->url->link(t('Start now'), 'TaskModificationController', 'start', ['task_id' => $task['id'], 'csrf_token' => $this->app->getToken()->getReusableCSRFToken()]) ?></span>
                        <?php endif ?>
                    </li>
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

    <?php if (! empty($task['external_uri']) && ! empty($task['external_provider'])): ?>
        <?= $this->app->component('external-task-view', array(
            'url' => $this->url->href('ExternalTaskViewController', 'show', array('task_id' => $task['id'])),
        )) ?>
    <?php endif ?>

    <?= $this->hook->render('template:task:details:bottom', array('task' => $task)) ?>
</section>
