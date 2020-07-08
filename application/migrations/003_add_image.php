<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_image extends CI_Migration {
    
    public function up() {
        $this->load->database();
        
        // Work
        $this->dbforge->add_field(array(
            "id"=>array(
                "type"=>"BIGINT",
                "unsigned"=>TRUE,
                "auto_increment"=>TRUE
            ),
            "work_width"=>array(
                "type"=>"DOUBLE",
                "constraint"=>"8,2"
            ),
            "work_height"=>array(
                "type"=>"DOUBLE",
                "constraint"=>"8,2"
            ),
            "work_depth"=>array(
                "type"=>"DOUBLE",
                "constraint"=>"8,2"
            ),
            "work_creation_year"=>array(
                "type"=>"VARCHAR",
                "constraint"=>32
            ),
            "image_width"=>array(
                "type"=>"INT",
                "unsigned"=>TRUE,
                "constraint"=>8
            ),
            "image_height"=>array(
                "type"=>"INT",
                "unsigned"=>TRUE,
                "constraint"=>8
            ),
            "title"=>array(
                "type"=>"VARCHAR",
                "constraint"=>256
            ),
            "description"=>array(
                "type"=>"VARCHAR",
                "constraint"=>256
            ),
            "version"=>array(
                "type"=>"INT",
                "unsigned"=>true,
                "null"=>false,
                "default"=>0
            )
        ));
        $this->dbforge->add_key("id",TRUE);
        $this->dbforge->create_table("image");

        $this->db->query("ALTER TABLE artist ADD FOREIGN KEY image_id_fk (image_id) REFERENCES image (id) ON DELETE SET NULL ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE exhibition ADD FOREIGN KEY image_id_fk (image_id) REFERENCES image (id) ON DELETE SET NULL ON UPDATE CASCADE");
        
        // Work artist link
        $this->dbforge->add_field(array(
            "image_id"=>array(
                "type"=>"BIGINT",
                "unsigned"=>TRUE
            ),
            "artist_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            ),
            "priority"=>array(
                "type"=>"INT"
            ),
            "featured"=>array(
                "type"=>"TINYINT",
                "constraint"=>1,
                "default"=>1
            )
        ));
        $this->dbforge->add_key("image_id",TRUE);
        $this->dbforge->add_key("artist_id",TRUE);
        $this->dbforge->create_table("image_artist");
        $this->db->query("ALTER TABLE image_artist ADD FOREIGN KEY image_id_fk (image_id) REFERENCES image (id) ON DELETE CASCADE ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE image_artist ADD FOREIGN KEY artist_id_fk (artist_id) REFERENCES artist (id) ON DELETE CASCADE ON UPDATE CASCADE");
        
        // Exhibition image link
        $this->dbforge->add_field(array(
            "image_id"=>array(
                "type"=>"BIGINT",
                "unsigned"=>TRUE
            ),
            "exhibition_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            ),
            "priority"=>array(
                "type"=>"INT",
                "null"=>false,
                "default"=>0
            )
        ));
        $this->dbforge->add_key("exhibition_id",TRUE);
        $this->dbforge->add_key("image_id",TRUE);
        $this->dbforge->create_table("image_exhibition");
        $this->db->query("ALTER TABLE image_exhibition ADD FOREIGN KEY image_id_fk (image_id) REFERENCES image (id) ON DELETE CASCADE ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE image_exhibition ADD FOREIGN KEY exhibition_id_fk (exhibition_id) REFERENCES exhibition (id) ON DELETE CASCADE ON UPDATE CASCADE");
    }
    
    public function down() {
        $this->dbforge->drop_table("image_exhibition");
        $this->dbforge->drop_table("image_artist");
        $this->dbforge->drop_table("image");
    }
}