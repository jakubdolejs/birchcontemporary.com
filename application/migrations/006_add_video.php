<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_video extends CI_Migration {

    public function up() {
        $this->dbforge->add_column("image",array(
            "vimeo_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>"128",
                "null"=>true
            )
        ));
    }

    public function down() {
        $this->dbforge->drop_column("image","vimeo_id");
    }
}