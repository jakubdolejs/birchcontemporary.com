<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(rtrim(APPPATH,"/")."/models/Bc_Model.php");

class Artist_model extends Bc_Model {

    public function get_artists($with_image_counts=false,$only_artists_with_image=false) {
        $this->db->select("artist.id, artist.name ,artist.surname, represented, artist.image_id",false)
                ->from("artist");
        if ($with_image_counts) {
            $this->db->select("count(distinct image_artist.image_id) as 'image_count'")
                ->join("image_artist","image_artist.artist_id = artist.id","left")
                ->group_by("artist.id");
        }
        if ($only_artists_with_image) {
            $this->db->where("artist.image_id IS NOT NULL",null,false);
        }
        $this->db->order_by("represented","desc")
                ->order_by("surname")
                ->order_by('name');
        $query = $this->db->get();
        if ($query->num_rows()) {
            $artists = array();
            foreach ($query->result_array() as $row) {
                if (!array_key_exists($row["id"],$artists)) {
                    $artists[$row["id"]] = array(
                        "id"=>$row["id"],
                        "name"=>$row["name"],
                        "surname"=>$row["surname"],
                        "represented"=>intval($row["represented"]),
                        "image_id"=>$row["image_id"]
                    );
                    if ($with_image_counts) {
                        $artists[$row["id"]]["image_count"] = $row["image_count"];
                    }
                }
            }
            return array_values($artists);
        }
        return null;
    }

    public function get_name($artist_id) {
        $this->db->select("trim(concat(artist.name,' ',artist.surname)) as `full_name`",false)
            ->from("artist")
            ->where("id",$artist_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row["full_name"];
        }
        return null;
    }

    public function get_artist($artist_id,$with_image_count=false) {
        $this->db->select("artist.id, artist.name, artist.surname, represented is not null and represented > 0 as 'represented', artist.image_id")
            ->from("artist")
            ->where("artist.id",$artist_id);
        if ($with_image_count) {
            $this->db->select("count(distinct image_artist.image_id) as 'image_count'")
                ->join("image_artist","image_artist.artist_id = artist.id","left");
        }
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row_array();
            $artist = array(
                "id"=>$row["id"],
                "name"=>$row["name"],
                "surname"=>$row["surname"],
                "image_id"=>$row["image_id"],
                "represented"=>(bool)$row["represented"]
            );
            if ($with_image_count) {
                $artist["image_count"] = $row["image_count"];
            }
            return $artist;
        }
        return null;
    }

    public function update($user_id,$artist_id,$name,$surname,$represented,$image_id) {
        if (!$image_id) {
            $image_id = null;
        }
        $this->db->set("name",$name)
            ->set("surname",$surname)
            ->set("represented",intval($represented))
            ->set("image_id",$image_id)
            ->where("id",$artist_id);
        if ($this->db->update("artist") !== false) {
            $this->log($user_id);
            return true;
        }
        return false;
    }

    public function get_artist_sections($artist_id) {
        $this->db->from("image_artist")
            ->where("artist_id",$artist_id);
        $has_images = $this->db->count_all_results() > 0;
        $this->db->from("artist_exhibition")
            ->where("artist_id",$artist_id);
        $has_exhibitions = $this->db->count_all_results() > 0;
        $has_cv = file_exists(rtrim(FCPATH,"/")."/cv_pdf/".$artist_id.".pdf");
        $this->db->from("news_artist")
            ->where("artist_id",$artist_id);
        $has_news = $this->db->count_all_results() > 0;
        return array("images"=>$has_images,"exhibitions"=>$has_exhibitions,"cv"=>$has_cv,"news"=>$has_news);
    }

    private function artist_id_exists($id) {
        $this->db->where("id",$id);
        $this->db->from("artist");
        return $this->db->count_all_results() > 0;
    }

    public function add($user_id,$name,$surname) {
        $this->load->helper("text");
        $base_id = preg_replace("/[^a-z0-9]+/i","-",strtolower(convert_accented_characters(trim($name.' '.$surname))));
        $id = $base_id;
        $i = 1;
        while ($this->artist_id_exists($id)) {
            $id = $base_id."-".$i;
            $i++;
        }
        $this->db->set("id",$id);
        $this->db->set("name",$name);
        $this->db->set("surname",$surname);
        if ($this->db->insert("artist")) {
            $this->log($user_id);
            return $id;
        }
        return false;
    }

    public function is_deletable_by_user($artist_id,$user) {
        $this->db->from("artist_exhibition")
            ->where("artist_id",$artist_id);
        if ($this->db->count_all_results() > 0) {
            return false;
        }
        $this->db->from("image_artist")
            ->where("artist_id",$artist_id);
        if ($this->db->count_all_results() > 0) {
            return false;
        }
        return true;
    }

    public function delete($user_id,$artist_id) {
        $this->db->trans_start();
        $this->db->where("id",$artist_id);
        $this->db->delete("artist");
        $this->log($user_id);
        $this->db->trans_complete();
        if ($this->db->trans_status() !== false) {
            return true;
        }
        return false;
    }
}