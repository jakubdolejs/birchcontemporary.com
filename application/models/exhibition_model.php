<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(rtrim(APPPATH,"/")."/models/Bc_Model.php");

class Exhibition_model extends Bc_Model {

    public function get_exhibitions($type) {
        $this->db->select("exhibition.id, title, text, exhibition.start_date, exhibition.end_date, exhibition.reception_start, exhibition.reception_end, exhibition.image_id, artist.id as 'artist_id', trim(concat(artist.name,' ',artist.surname)) as 'artist_name'",false)
                ->from("exhibition")
                ->join("artist_exhibition JOIN artist ON artist_exhibition.artist_id = artist.id","artist_exhibition.exhibition_id = exhibition.id","left");
        if ($type == "current") {
            $this->db->where("end_date >= NOW()",NULL,FALSE)
                    ->where("start_date <= NOW()",NULL,FALSE)
                    ->order_by("start_date");
        } else if ($type == "upcoming") {
            $this->db->where("start_date > NOW()",NULL,FALSE)
                    ->order_by("start_date");
        } else if ($type == "past") {
            $this->db->where("end_date < NOW()",NULL,FALSE)
                    ->order_by("end_date","DESC");
        }
        $this->db->order_by("artist.surname")->order_by("artist.name");
        $query = $this->db->get();
        $exhibitions = array();
        foreach ($query->result_array() as $exhibition) {
            if (!array_key_exists($exhibition["id"], $exhibitions)) {
                $exhibitions[$exhibition["id"]] = $exhibition;
                if (isset($exhibitions[$exhibition["id"]]["artist_name"])) {
                    unset($exhibitions[$exhibition["id"]]["artist_name"]);
                }
                $exhibitions[$exhibition["id"]]["artists"] = array();
            }
            if (!empty($exhibition["artist_id"])) {
                $exhibitions[$exhibition["id"]]["artists"][$exhibition["artist_id"]] = $exhibition["artist_name"];
            }
        }
        return $exhibitions;
    }

    public function get_all_exhibitions($year=null) {
        $this->db->select("exhibition.id, title, text, exhibition.start_date, exhibition.end_date, exhibition.reception_start, exhibition.reception_end, exhibition.image_id, artist.id as 'artist_id', trim(concat(artist.name,' ',artist.surname)) as 'artist_name'",false)
            ->from("exhibition")
            ->join("artist_exhibition JOIN artist ON artist_exhibition.artist_id = artist.id","artist_exhibition.exhibition_id = exhibition.id","left")
            ->order_by("exhibition.start_date","DESC");
        if (!$year) {
            $this->db->where("exhibition.start_date > now() - interval 1 year",null,false);
        } else {
            $this->db->where("date_format(exhibition.start_date,'%Y') = ".$this->db->escape($year),null,false)
                ->or_where("date_format(exhibition.end_date,'%Y') = ".$this->db->escape($year));
        }
        $this->db->order_by("artist.surname")->order_by("artist.name");
        $query = $this->db->get();
        $exhibitions = array();
        if ($query->num_rows()) {
            foreach ($query->result_array() as $exhibition) {
                if (!array_key_exists($exhibition["id"], $exhibitions)) {
                    $exhibitions[$exhibition["id"]] = $exhibition;
                    if (isset($exhibitions[$exhibition["id"]]["artist_name"])) {
                        unset($exhibitions[$exhibition["id"]]["artist_name"]);
                    }
                    $exhibitions[$exhibition["id"]]["artists"] = array();
                }
                if (!empty($exhibition["artist_id"])) {
                    $exhibitions[$exhibition["id"]]["artists"][$exhibition["artist_id"]] = $exhibition["artist_name"];
                }
            }
        }
        return $exhibitions;
    }

    public function get_artist_exhibitions($artist_id) {
        $this->db->select("exhibition.id, title, text, exhibition.start_date, exhibition.end_date, exhibition.reception_start, exhibition.reception_end, exhibition.image_id, artist.id as 'artist_id', trim(concat(artist.name,' ',artist.surname)) as 'artist_name'",false)
            ->from("exhibition")
            ->join("artist_exhibition JOIN artist ON artist_exhibition.artist_id = artist.id","artist_exhibition.exhibition_id = exhibition.id","left")
            ->where("exists (select 1 from artist_exhibition where artist_exhibition.exhibition_id = exhibition.id and artist_exhibition.artist_id = ".$this->db->escape($artist_id)." group by exhibition.id)",null,false);
        $this->db->order_by("start_date <= NOW() AND end_date >= NOW()","DESC")
            ->order_by("start_date > NOW()","DESC")
            ->order_by("start_date","DESC");
        $query = $this->db->get();
        $exhibitions = array();
        foreach ($query->result_array() as $exhibition) {
            if (!array_key_exists($exhibition["id"], $exhibitions)) {
                $exhibitions[$exhibition["id"]] = $exhibition;
                if (isset($exhibitions[$exhibition["id"]]["artist_name"])) {
                    unset($exhibitions[$exhibition["id"]]["artist_name"]);
                }
                unset($exhibitions[$exhibition["id"]]["text"]);
                $exhibitions[$exhibition["id"]]["artists"] = array();
            }
            if (!empty($exhibition["artist_id"])) {
                $exhibitions[$exhibition["id"]]["artists"][$exhibition["artist_id"]] = $exhibition["artist_name"];
            }
            if (!empty($exhibition["text"])) {
                $exhibitions[$exhibition["id"]]["text"][$exhibition["lang"]] = $exhibition["text"];
            }
        }
        return $exhibitions;
    }

    public function get_exhibition($exhibition_id) {
        $this->db->select("exhibition.id, title, text, exhibition.start_date, exhibition.end_date, exhibition.reception_start, exhibition.reception_end, exhibition.image_id, artist.id as 'artist_id', trim(concat(artist.name,' ',artist.surname)) as 'artist_name'",false)
            ->from("exhibition")
            ->join("artist_exhibition JOIN artist ON artist_exhibition.artist_id = artist.id","artist_exhibition.exhibition_id = exhibition.id","left")
            ->where("exhibition.id",$exhibition_id)
            ->order_by("artist.surname")->order_by("artist.name");
        $query = $this->db->get();
        $exhibition = null;
        if ($query->num_rows()) {
            foreach ($query->result_array() as $row) {
                if (!$exhibition) {
                    $exhibition = array(
                        "id"=>$row["id"],
                        "title"=>$row["title"],
                        "artists"=>array(),
                        "text"=>$row["text"],
                        "start_date"=>$row["start_date"],
                        "end_date"=>$row["end_date"],
                        "reception_start"=>$row["reception_start"],
                        "reception_end"=>$row["reception_end"],
                        "image_id"=>$row["image_id"]
                    );
                }
                if ($row["artist_id"]) {
                    $exhibition["artists"][$row["artist_id"]] = $row["artist_name"];
                }
            }
        }
        return $exhibition;
    }

    private function exhibition_id_exists($id) {
        $this->db->from("exhibition")
            ->where("id",$id);
        return $this->db->count_all_results() > 0;
    }

    public function update($user_id,$exhibition_id,$values,&$errorinfo=null) {
        $this->db->trans_start();
        if (isset($values["start_date"])) {
            $this->db->set("start_date",$values["start_date"]);
        }
        if (isset($values["end_date"])) {
            $this->db->set("end_date",$values["end_date"]);
        }
        if (isset($values["reception_start"]) && isset($values["reception_starttime"]) && isset($values["reception_endtime"])) {
            if (empty($values["reception_start"]) || empty($values["reception_starttime"]) || empty($values["reception_endtime"])) {
                $this->db->set("reception_start",null);
                $this->db->set("reception_end",null);
            } else {
                $this->db->set("reception_start",$values["reception_start"]." ".$values["reception_starttime"].":00");
                $this->db->set("reception_end",$values["reception_start"]." ".$values["reception_endtime"].":00");
            }
        }
        if (isset($values["image_id"])) {
            if (empty($values["image_id"])) {
                $this->db->set("image_id",null);
            } else {
                $this->db->set("image_id",$values["image_id"]);
            }
        }
        if (isset($values["title"])) {
            if (empty($values["title"])) {
                $this->db->set("title",null);
            } else {
                $this->db->set("title",$values["title"]);
            }
        }
        if (isset($values["text"])) {
            if (empty($values["text"])) {
                $this->db->set("text",null);
            } else {
                $this->db->set("text",$values["text"]);
            }
        }
        if ($exhibition_id) {
            $this->db->where("id",$exhibition_id);
            $this->db->update("exhibition");
            $this->log($user_id);
        } else {
            $base_id = "";
            $this->load->helper("text");
            if (!empty($values["title"])) {
                $base_id = preg_replace("/[^a-z0-9]+/","-",convert_accented_characters(strtolower($values["title"])));
            } else if (!empty($values["artist_ids"])) {
                $base_id = join("-",$values["artist_ids"]);
            } else {
                return false;
            }
            $exhibition_id = substr($base_id,0,128);
            $i = 1;
            while ($this->exhibition_id_exists($exhibition_id)) {
                $exhibition_id = substr($base_id,0,128-strlen("-".$i))."-".$i;
                $i ++;
            }
            $this->db->set("id",$exhibition_id);
            $this->db->insert("exhibition");
            $this->log($user_id);
        }
        $this->db->where("exhibition_id",$exhibition_id);
        $this->db->delete("artist_exhibition");
        $this->log($user_id);
        if (!empty($values["artist_ids"])) {
            $batch = array();
            foreach ($values["artist_ids"] as $artist_id) {
                $batch[] = array("artist_id"=>$artist_id,"exhibition_id"=>$exhibition_id);
            }
            $this->db->insert_batch("artist_exhibition",$batch);
            $this->log($user_id);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() !== false) {
            return true;
        }
        return false;
    }

    public function delete($user_id,$exhibition_id) {
        $this->db->trans_start();
        $this->db->where("exhibition_id",$exhibition_id);
        $this->db->delete("artist_exhibition");
        $this->log($user_id);
        $this->db->where("exhibition_id",$exhibition_id);
        $this->db->delete("image_exhibition");
        $this->log($user_id);
        $this->db->where("id",$exhibition_id);
        $this->db->delete("exhibition");
        $this->log($user_id);
        $this->db->trans_complete();
        if ($this->db->trans_status() !== false) {
            return true;
        }
        return false;
    }

    public function get_years() {
        $this->db->select("date_format(start_date,'%Y') as 'start', date_format(end_date,'%Y') as 'end'",false)
            ->distinct()
            ->from("exhibition");
        $this->db->order_by("start_date","desc");
        $query = $this->db->get();
        $years = array();
        if ($query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $years[] = $row["start"];
                $years[] = $row["end"];
            }
            $years = array_unique($years,SORT_DESC|SORT_NUMERIC);
        }
        return $years;
    }
}