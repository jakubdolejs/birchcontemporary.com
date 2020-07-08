<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_news extends CI_Migration {

    public function up() {
        $this->dbforge->add_field(array(
            "id"=>array(
                "type"=>"INT",
                "auto_increment"=>true
            ),
            "html"=>array(
                "type"=>"text"
            ),
            "date"=>array(
                "type"=>"date"
            )
        ));
        $this->dbforge->add_key("id",true);
        $this->dbforge->create_table("news",true);

        $this->dbforge->add_field(array(
            "news_id"=>array(
                "type"=>"INT"
            ),
            "artist_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>"128"
            )
        ));
        $this->dbforge->add_key("news_id",true);
        $this->dbforge->add_key("artist_id",true);
        $this->dbforge->create_table("news_artist",true);

        $this->load->database();
        $this->db->query("ALTER TABLE news_artist ADD FOREIGN KEY news_fk (news_id) REFERENCES news (id) ON DELETE CASCADE");
        $this->db->query("ALTER TABLE news_artist ADD FOREIGN KEY artist_fk (artist_id) REFERENCES artist (id) ON DELETE CASCADE");

        $this->dbforge->add_field(array(
            "news_id"=>array(
                "type"=>"INT"
            ),
            "exhibition_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>"128"
            )
        ));
        $this->dbforge->add_key("news_id",true);
        $this->dbforge->add_key("exhibition_id",true);
        $this->dbforge->create_table("news_exhibition",true);

        $this->db->query("ALTER TABLE news_exhibition ADD FOREIGN KEY news_fk (news_id) REFERENCES news (id) ON DELETE CASCADE");
        $this->db->query("ALTER TABLE news_exhibition ADD FOREIGN KEY exhibition_fk (exhibition_id) REFERENCES exhibition (id) ON DELETE CASCADE");
    }

    public function down() {
        $this->dbforge->drop_table("news_exhibition");
        $this->dbforge->drop_table("news_artist");
        $this->dbforge->drop_table("news");
    }
}