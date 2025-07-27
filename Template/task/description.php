<?php
if (!empty($task['description'])) {
    echo $this->renderDirect('task/description', array('task' => $task));
}
