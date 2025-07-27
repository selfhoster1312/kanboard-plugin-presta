<div class="sidebar sidebar-icons">
    <div class="sidebar-title">
        <h2><?= t('Task #%d', $task['id']) ?></h2>
    </div>
    <ul>
        <li <?= $this->app->checkMenuSelection('TaskViewController', 'show') ?>>
            <?= $this->url->icon('newspaper-o', t('Summary'), 'TaskViewController', 'show', array('task_id' => $task['id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ActivityController', 'task') ?>>
            <?= $this->url->icon('dashboard', t('Activity stream'), 'ActivityController', 'task', array('task_id' => $task['id'])) ?>
        </li>

        <?= $this->hook->render('template:task:sidebar:information', array('task' => $task)) ?>

    <?php if ($this->user->hasProjectAccess('TaskModificationController', 'edit', $task['project_id'])): ?>
        <?= $this->hook->render('template:task:sidebar:before-actions', array('task' => $task)) ?>

        <?php if ($this->projectRole->canUpdateTask($task)): ?>
        <li>
            <?= $this->modal->large('edit', t('Edit the task'), 'TaskModificationController', 'edit', array('task_id' => $task['id'])) ?>
        </li>
        <?php endif ?>
        <?= $this->hook->render('template:task:sidebar:after-basic-actions', array('task' => $task)) ?>

        <?= $this->hook->render('template:task:sidebar:after-add-links', array('task' => $task)) ?>

        <?= $this->hook->render('template:task:sidebar:after-add-comment', array('task' => $task)) ?>

        <?= $this->hook->render('template:task:sidebar:after-add-attachments', array('task' => $task)) ?>

        <li>
            <?= $this->modal->small('files-o', t('Duplicate'), 'TaskDuplicationController', 'duplicate', array('task_id' => $task['id'])) ?>
        </li>
        <li>
            <?= $this->modal->small('clipboard', t('Duplicate to project'), 'TaskDuplicationController', 'copy', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <?= $this->hook->render('template:task:sidebar:after-duplicate-task', array('task' => $task)) ?>

        <li>
            <?= $this->modal->small('clone', t('Move to project'), 'TaskDuplicationController', 'move', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <?= $this->hook->render('template:task:sidebar:after-send-mail', array('task' => $task)) ?>

        <?php if ($this->projectRole->canChangeTaskStatusInColumn($task['project_id'], $task['column_id'])): ?>
            <?php if ($task['is_active'] == 1): ?>
                <li>
                    <?= $this->modal->confirm('times', t('Close this task'), 'TaskStatusController', 'close', array('task_id' => $task['id'])) ?>
                </li>
            <?php else: ?>
                <li>
                    <?= $this->modal->confirm('check-square-o', t('Open this task'), 'TaskStatusController', 'open', array('task_id' => $task['id'])) ?>
                </li>
            <?php endif ?>
        <?php endif ?>
        <?php if ($this->projectRole->canRemoveTask($task)): ?>
            <li>
                <?= $this->modal->confirm('trash-o', t('Remove'), 'TaskSuppressionController', 'confirm', array('task_id' => $task['id'], 'redirect' => 'board')) ?>
            </li>
        <?php endif ?>

        <?= $this->hook->render('template:task:sidebar:actions', array('task' => $task)) ?>
    </ul>
    <?php endif ?>
    <div class="sidebar-title">
        <h2><?= t('Action', $task['id']) ?></h2>
    </div>
    <ul>
        <li>
            <?= $this->modal->small('user', t('Client'), 'PrestaClientController', 'select', array('plugin' => 'Presta', 'task_id' => $task['id'])) ?>
        </li>
        <li>
            <?= $this->modal->small('home', t('City'), 'PrestaCityController', 'select', array('plugin' => 'Presta', 'task_id' => $task['id'])) ?>
        </li>
        <li>
            <?= $this->modal->small('euro', t('Offer'), 'PrestaOfferController', 'select', array('plugin' => 'Presta', 'task_id' => $task['id'])) ?>
        </li>
    </ul>
</div>
