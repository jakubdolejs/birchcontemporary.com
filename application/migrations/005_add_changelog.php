<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_changelog extends CI_Migration {

    public function up() {
        $this->dbforge->add_field(array(
            "id"=>array(
                "type"=>"BIGINT",
                "unsigned"=>true,
                "null"=>false,
                "auto_increment"=>true
            ),
            "time"=>array(
                "type"=>"INT"
            ),
            "user_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>"128"
            ),
            "query"=>array(
                "type"=>"TEXT"
            )
        ));
        $this->dbforge->add_key("id",true);
        $this->dbforge->create_table("admin_log");
    }

    public function down() {
        $this->dbforge->drop_table("admin_log");
    }
}