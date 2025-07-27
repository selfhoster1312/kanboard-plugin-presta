<?php
if (!empty($links)) {
    echo $this->renderDirect('task_internal_link/show', array(
        'links' => $links,
        'project' => $project,
        'link_label_list' => $link_label_list,
        'editable' => $editable,
        'is_public' => false,
    ));
}