<?php
if (!empty($files) || !empty($images)) {
    echo $this->renderDirect('task_file/show', array(
        'task' => $task,
        'files' => $files,
        'images' => $images
    ));
}

