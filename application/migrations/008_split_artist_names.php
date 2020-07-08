<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Split_artist_names extends CI_Migration {
    
    public function up() {
        $this->dbforge->add_column("artist",array(
            "surname"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            )
        ));
        $this->load->database();
        $this->db->select("id, name")->from("artist");
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $name = preg_split('/ /',$row["name"],2);
                $this->db->set("name",$name[0]);
                if (count($name) > 1) {
                    $this->db->set("surname",$name[1]);
                } else {
                    $this->db->set("surname",null);
                }
                $this->db->where("id",$row["id"]);
                $this->db->update("artist");
            }
        }
    }
    
    public function down() {
        $this->load->database();
        $this->db->select("id, name, surname")->from("artist");
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $this->db->set('name',trim($row["name"].' '.$row["surname"]))->where("id",$row["id"]);
                $this->db->update("artist");
            }
        }
        $this->dbforge->drop_column("artist","surname");
    }
}