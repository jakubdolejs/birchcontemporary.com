<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_access_token extends CI_Migration {

    public function up() {
        $this->load->database();

        $this->dbforge->add_field(array(
            "id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            ),
            "password_checksum"=>array(
                "type"=>"VARCHAR",
                "constraint"=>256
            ),
            "superuser"=>array(
                "type"=>"TINYINT",
                "constraint"=>1,
                "default"=>0
            )
        ));
        $this->dbforge->add_key("id",true);
        $this->dbforge->create_table("user");

        $this->dbforge->add_field(array(
            "token"=>array(
                "type"=>"VARCHAR",
                "constraint"=>32
            ),
            "user_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            ),
            "user_agent"=>array(
                "type"=>"VARCHAR",
                "constraint"=>64
            ),
            "ip_address"=>array(
                "type"=>"VARCHAR",
                "constraint"=>32
            ),
            "issue_date"=>array(
                "type"=>"DATETIME"
            ),
            "last_access_date"=>array(
                "type"=>"DATETIME"
            )
        ));
        $this->dbforge->add_key("token",true);
        $this->dbforge->add_key("user_id",true);
        $this->dbforge->add_key("user_agent",true);
        $this->dbforge->add_key("ip_address",true);
        $this->dbforge->create_table("access_token");

        $this->db->query("ALTER TABLE access_token ADD FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE ON UPDATE CASCADE");
    }

    public function down() {
        $this->dbforge->drop_table("access_token");
        $this->dbforge->drop_table("user");
    }
}
