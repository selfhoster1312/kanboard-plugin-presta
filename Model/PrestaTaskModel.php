<?php

namespace Kanboard\Plugin\Presta\Model;
// namespace Kanboard\Plugin\Presta\PrestaTaskModel;

define('PRESTA_TASKS_DIR', DATA_DIR.DIRECTORY_SEPARATOR.'presta'.DIRECTORY_SEPARATOR.'tasks');

class PrestaTaskModel {
    public $task_id;
    public $values = null;
    
    function __construct(int $task_id) {
        $this->task_id = $task_id;
        $this->file_path = $this->getTaskFilePath();
        $this->values = null;
    }
    
    public function getTaskFilePath() {
        return PRESTA_TASKS_DIR.DIRECTORY_SEPARATOR.strval($this->task_id).'.json';
    }

    public function load() {
        if ($this->values == null) {
            if (!file_exists($this->file_path)) {
                $this->values = [ "task" => $this->task_id ];
                return;
            }
            $file_content = file_get_contents($this->file_path);
            $this->values = json_decode($file_content, true);
        }
    }

    public function save() {
        if ($this->values == null) {
            error_log("PRESTA: programming error! Attempting to save null array (task $this->task_id)");
            return;
        }

        $file_content = json_encode($this->values);

        // Make sure parent dir exists
        if (!is_dir(PRESTA_TASKS_DIR)) {
            mkdir(PRESTA_TASKS_DIR, 0777, true);
        }
        
        file_put_contents($this->file_path, $file_content);
    }

    public function getClient() {
        $this->load();
        return isset($this->values["client"]) ? $this->values["client"] : null;
    }

    public function setClient_unchecked($client_id) {
        $this->load();
        $this->values["client"] = $client_id;
        $this->save();
    }
}
