<?php if (isset($external_links)){
    echo $this->renderDirect('task_external_link/show', array(
        'task' => $task,
        'links' => $links,
        'project' => $project,
    ));
}