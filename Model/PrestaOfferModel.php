<?php
namespace Kanboard\Plugin\Presta\Model;

use Kanboard\Core\Base;

define('PRESTA_OFFERS', PRESTA_DIR.DIRECTORY_SEPARATOR.'offers.json');

class PrestaOfferModel extends Base {
    public $values = null;
    
    function load() {
       if ($this->values == null) {
            if (!file_exists(PRESTA_OFFERS)) {
                $this->values = [ "counter" => 0, "offers" => [] ];
                return;
            }
            $file_content = file_get_contents(PRESTA_OFFERS);
            $this->values = json_decode($file_content, true);
        }
    }

    function save() {
        if ($this->values == null) {
            error_log("PRESTA: programming error! Attempting to save null array (offers)");
            return;
        }

        $file_content = json_encode($this->values, JSON_PRETTY_PRINT);

        // Make sure parent dir exists
        if (!is_dir(PRESTA_DIR)) {
            mkdir(PRESTA_DIR, 0777, true);
        }
        
        file_put_contents(PRESTA_OFFERS, $file_content);
    }
    
    public function get($offer_id) {
        $this->load();
        if (isset($this->values['offers'][$offer_id])) {
            return $this->values['offers'][$offer_id];
        } else {
            error_log("PRESTA: Attempting to get info about non-existing offer $offer_id. May be a race condition because an offer was just deleted. Only worry about this error if it appears *regularly* in the logs.");
            return null;
        }
    }

    public function has(string $offer_id) {
        $this->load();
        if (isset($this->values['offers'][$offer_id])) {
            return true;
        }

        return false;
    }

    public function remove(string $id) {
        $this->load();
        if (!isset($this->values["offers"][$id])) {
            return;
        }
        unset($this->values["offers"][$id]);
        $this->save();
    }

    public function create(string $short_name, string $description, int $price) {
        $this->load();
        // We never want to reattribute an ID (auto-increment) so we use the counter total value
        // and increment it for the next client ID.
        $this->values["counter"] += 1;
        
        $counter = $this->values["counter"];
        $this->values["offers"][strval($counter)] = [
            "offer_id" => strval($counter),
            "short_name" => $short_name,
            "description" => $description,
            "price" => $price,
            // Add a unique ID for further edition
            "uuid" => uniqid(),
        ];
        $this->save();

        return strval($counter);
    }

    // Update an existing offer
    public function update($offer_id, string $short_name, string $description, int $price) {
        $this->load();
        if (!isset($this->values["offers"][$offer_id])) {
            return;
        }
        $this->values["offers"][$offer_id]["short_name"] = $short_name;
        $this->values["offers"][$offer_id]["description"] = $description;
        $this->values["offers"][$offer_id]["price"] = $price;
        $this->save();
    }

    // Return an array of ID->NAME for use in forms
    public function options() {
        $this->load();
        return array_map(function($client_value) { return $client_value["short_name"]; }, $this->values["offers"]);
    }

    // Return the raw list of clients
    public function list() {
        $this->load();
        return $this->values["offers"];
    }
}
