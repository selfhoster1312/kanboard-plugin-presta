<?php
if (!empty($comments)) {
    echo $this->renderDirect('task_comments/show', array(
        'task' => $task,
        'comments' => $comments,
        'project' => $project,
        'editable' => false,
    ));
}