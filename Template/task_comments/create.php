<?php use Kanboard\Core\Security\Role; ?>
<h3 style="font-weight: bold;"><?= t('Add a comment') ?></h2>
<form method="post" action="<?= $this->url->href('CommentController', 'save', array('task_id' => $task['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('task_id', $values) ?>
    <?= $this->form->hidden('user_id', $values) ?>
    <?= $this->form->hidden('visibility', [ 'visibility' => 'app-user' ]) ?>

    <?= $this->form->textEditor('comment', $values, $errors, array('required' => true, 'aria-label' => t('New comment'))) ?>

    <?= $this->modal->submitButtons() ?>
</form>
