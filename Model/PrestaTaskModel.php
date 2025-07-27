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
        $task['discount'] = [ 'amount' => $discount_amount, 'reason' => $discount_reason ];
        $this->save($task_id, $task);
    }

    public function removeDiscount($task_id) {
        $task = $this->load($task_id);
        $task['discount'] = [];
        $this->save($task_id, $task);
    }

    public function addOffer_unchecked($task_id, $offer_id, $date, $start, $end, $uuid = null) {
        $task = $this->load($task_id);
        if (!isset($task["offers"])) {
            $task['offers'] = [];
        }

        array_push(
            $task['offers'],
            [
                'offer_id' => $offer_id,
                'date' => $date,
                'start' => $start,
                'end' => $end,
                'uuid' => $uuid ?? uniqid(),
            ]
        );
        $this->save($task_id, $task);
    }

    // Returns arrays of offers split by offer type so they can be grouped
    // eg. "Demi-journée" => [ OFFER_OBJECT, OFFER_OBJECT ]
    public function getOffers($task_id) {
        $task = $this->load($task_id);

        $final_offers = [];
        $available_offers = $this->prestaOfferModel->options();
        foreach($task['offers'] as $offer) {
            $key = $available_offers[$offer['offer_id']];
            if (!isset($final_offers[$key])) {
                $final_offers[$key] = [];
            }
            array_push($final_offers[$key], $offer);
        }

        return $final_offers;
    }

    // Returns calculated price with discount
    public function totalPrice($task_id) {
        $task = $this->load($task_id);
        $total = 0;

        foreach($task['offers'] as $offer) {
            $offer_price = $this->prestaOfferModel->get($offer['offer_id'])['price'];
            $total = $total + $offer_price;
        }

        if ($total == 0) {
            return 0;
        }

        $total = $total + ($task['distance_fee'] ?? 0);
        $total = $total - ($task['discount']['amount'] ?? 0);
        return $total;
    }

    public function hasOfferUuid($task_id, $offer_uuid) {
        $task = $this->load($task_id);
        foreach($task['offers'] ?? [] as $offer) {
            if ($offer['uuid'] == $offer_uuid) {
                return true;
            }
        }

        return false;
    }

    public function getOfferByUuid($task_id, $offer_uuid) {
        $task = $this->load($task_id);
        foreach($task['offers'] ?? [] as $offer) {
            if ($offer['uuid'] == $offer_uuid) {
                return $offer;
            }
        }
    }

    public function removeOfferUuid($task_id, $offer_uuid) {
        $task = $this->load($task_id);
        foreach($task['offers'] ?? [] as $counter => $offer) {
            if ($offer['uuid'] == $offer_uuid) {
                $key = $counter;
            }
        }
        if (!isset($key)) {
            return;
        }
        unset($task['offers'][$key]);
        $this->save($task_id, $task);
    }

    public function updateOfferByUuid($task_id, $offer_uuid, $offer_update)
    {
        $this->removeOfferUuid($task_id, $offer_uuid);
        $this->addOffer_unchecked($task_id, $offer_update['id'], $offer_update['date'], $offer_update['start'], $offer_update['end'], $offer_uuid);
        // $this->save($task_id, $task);
    }
}
