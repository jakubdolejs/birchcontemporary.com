<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_exhibition extends CI_Migration {
    
    public function up() {
        // Exhibitions
        $this->dbforge->add_field(array(
            "id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128                
            ),            
            "start_date"=>array(
                "type"=>"DATE",
                "null"=>true
            ),
            "end_date"=>array(
                "type"=>"DATE",
                "null"=>true
            ),
            "reception_start"=>array(
                "type"=>"DATETIME",
                "null"=>true
            ),
            "reception_end"=>array(
                "type"=>"DATETIME",
                "null"=>true
            ),
            "title"=>array(
                "type"=>"VARCHAR",
                "constraint"=>256
            ),
            "text"=>array(
                "type"=>"TEXT",
                "null"=>true
            ),
            "image_id"=>array(
                "type"=>"BIGINT",
                "unsigned"=>true,
                "null"=>true
            )
        ));
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->create_table("exhibition");
    }
    
    public function down() {
        $this->dbforge->drop_table("exhibition");
    }
}
