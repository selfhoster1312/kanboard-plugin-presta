<?php
if (!empty($subtasks)) {
    echo $this->renderDirect('subtask/show', array(
        'task' => $task,
        'subtasks' => $subtasks,
        'project' => $project,
        'editable' => $editable,
    ));
}
