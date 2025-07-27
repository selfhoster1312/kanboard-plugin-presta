<?php

namespace Kanboard\Plugin\Presta\Model;

use Kanboard\Core\Base;

define('PRESTA_TASKS_DIR', DATA_DIR.DIRECTORY_SEPARATOR.'presta'.DIRECTORY_SEPARATOR.'tasks');

class PrestaTaskModel extends Base {
    public function getTaskFilePath($task_id) {
        return PRESTA_TASKS_DIR.DIRECTORY_SEPARATOR.strval($task_id).'.json';
    }

    public function load($task_id) {
        $path = $this->getTaskFilePath($task_id);
        if (!file_exists($path)) {
            return [ "task" => $task_id ];
        }
        $file_content = file_get_contents($path);
        return json_decode($file_content, true);
    }

    public function save($task_id, $task) {
        $file_content = json_encode($task);

        // Make sure parent dir exists
        if (!is_dir(PRESTA_TASKS_DIR)) {
            mkdir(PRESTA_TASKS_DIR, 0777, true);
        }
        
        file_put_contents($this->getTaskFilePath($task_id), $file_content);
    }

    public function getClientId($task_id) {
        return $this->load($task_id)["client"] ?? null;
    }

    public function getClient($task_id) {
        $client_id = $this->load($task_id)["client"] ?? null;
        if ($client_id == null) {
            return null;
        }

        return $this->prestaClientModel->get($client_id);
    }

    public function setClientId_unchecked($task_id, $client_id) {
        $task = $this->load($task_id);
        $task["client"] = $client_id;
        $this->save($task_id, $task);
    }

    public function getCity($task_id) {
        return $this->load($task_id)["city"] ?? null;
    }

    public function setCity_unchecked($task_id, $city_name) {
        $task = $this->load($task_id);
        $task["city"] = $city_name;
        $this->save($task_id, $task);
    }

    public function getDistanceFee($task_id) {
        return $this->load($task_id)['distance_fee'] ?? 0;
    }

    public function setDistanceFee($task_id, int $fee) {
        $task = $this->load($task_id);
        $task['distance_fee'] = $fee;
        $this->save($task_id, $task);
    }

    public function getDiscount($task_id) {
        return $this->load($task_id)['discount'] ?? null;
    }

    public function setDiscount($task_id, $discount_amount, $discount_reason) {
        $task = $this->load($task_id);
        $task['discount'] = [ 'amount' => $discount_amount, 'reason' => $discoun_reason ];
        $this->save($task_id, $task);
    }
}
