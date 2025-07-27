<?php
namespace Kanboard\Plugin\Presta\Model;

// define('PRESTA_CLIENTS', DATA_DIR.DIRECTORY_SEPARATOR.'presta'.DIRECTORY_SEPARATOR.'clients.json');
define('PRESTA_CLIENTS', PRESTA_DIR.DIRECTORY_SEPARATOR.'clients.json');

class PrestaClientModel {
    public $values = null;
    
    function load() {
       if ($this->values == null) {
            if (!file_exists(PRESTA_CLIENTS)) {
                $this->values = [ "counter" => 0, "clients" => [] ];
                return;
            }
            $file_content = file_get_contents(PRESTA_CLIENTS);
            $this->values = json_decode($file_content, true);
        }
    }

    function save() {
        if ($this->values == null) {
            error_log("PRESTA: programming error! Attempting to save null array (clients)");
            return;
        }

        $file_content = json_encode($this->values, JSON_PRETTY_PRINT);

        // Make sure parent dir exists
        if (!is_dir(PRESTA_DIR)) {
            mkdir(PRESTA_DIR, 0777, true);
        }
        
        file_put_contents(PRESTA_CLIENTS, $file_content);
    }
    
    public function get($client_id) {
        $this->load();
        if (isset($this->values['clients'][$client_id])) {
            return $this->values['clients'][$client_id];
        } else {
            error_log("PRESTA: Attempting to get info about non-existing client $client_id. May be a race condition because a client was just deleted. Only worry about this error if it appears *regularly* in the logs.");
            return null;
        }
    }

    public function has(string $client_id) {
        $this->load();
        if (isset($this->values['clients'][$client_id])) {
            return true;
        }

        return false;
    }

    public function remove(string $id) {
        $this->load();
        if (!isset($this->values["clients"][$id])) {
            return;
        }
        unset($this->values["clients"][$id]);
        $this->save();
    }

    public function create(string $name, string $address) {
        $this->load();
        // We never want to reattribute an ID (auto-increment) so we use the counter total value
        // and increment it for the next client ID.
        $this->values["counter"] += 1;
        
        $counter = $this->values["counter"];
        $this->values["clients"][strval($counter)] = [ "id" => strval($counter), "name" => $name, "address" => $address];
        $this->save();

        return strval($counter);
    }

    // Update an existing client
    public function update($client_id, $client_name, $client_address) {
        $this->load();
        if (!isset($this->values["clients"][$client_id])) {
            return;
        }
        $this->values["clients"][$client_id]["name"] = $client_name;
        $this->values["clients"][$client_id]["address"] = $client_address;
        $this->save();
    }

    // Return an array of ID->NAME for use in forms
    public function options() {
        $this->load();
        return array_map(function($client_value) { return $client_value["name"]; }, $this->values["clients"]);
    }

    // Return the raw list of clients
    public function list() {
        $this->load();
        return $this->values["clients"];
    }
}
