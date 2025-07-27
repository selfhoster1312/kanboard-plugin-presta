<?php
namespace Kanboard\Plugin\Presta\Model;

use Kanboard\Core\Base;

define('PRESTA_CITIES', PRESTA_DIR.DIRECTORY_SEPARATOR.'cities.json');

class PrestaCityModel extends Base {
    public $values = null;
    
    function load() {
       if ($this->values == null) {
            if (!file_exists(PRESTA_CITIES)) {
                $this->values = [ 'cities' => [] ];
                return;
            }
            $file_content = file_get_contents(PRESTA_CITIES);
            $this->values = json_decode($file_content, true);
            return;
        }
    }

    function save() {
        if ($this->values == null) {
            error_log("PRESTA: programming error! Attempting to save null array (cities)");
            return;
        }

        $file_content = json_encode($this->values, JSON_PRETTY_PRINT);

        // Make sure parent dir exists
        if (!is_dir(PRESTA_DIR)) {
            mkdir(PRESTA_DIR, 0777, true);
        }
        
        file_put_contents(PRESTA_CITIES, $file_content);
    }

    public function has(string $city_name) {
        $this->load();
        return in_array($city_name, $this->values['cities']);
    }

    public function remove(string $city_name) {
        $this->load();
        if (!$this->has($city_name)) {
            return;
        }

        $key = array_search($city_name, $this->values['cities']);
        unset($this->values['cities'][$key]);
        $this->save();
    }

    public function create(string $name) {
        $this->load();

        if ($this->has($name)) {
            return;
        }

        array_push($this->values['cities'], $name);        
        $this->save();
    }

    // Return an array of NAME->NAME for use in forms
    public function options() {
        $this->load();
        $options = [];

        foreach($this->list() as $city_name) {
            $options[$city_name] = $city_name;
        }

        return $options;
    }

    // Return the raw list of cities
    public function list() {
        $this->load();
        return $this->values['cities'];
    }
}
