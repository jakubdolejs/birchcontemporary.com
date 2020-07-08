<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends CI_Controller {
    
    public function index() {
        $this->load->library("migration");
        if ($this->migration->current()) {
            echo "Migrated to current version";
        } else {
            echo "Error migrating database: ".$this->migration->error_string();
        }
        echo PHP_EOL;
    }
}