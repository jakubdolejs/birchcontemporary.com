<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(rtrim(APPPATH,"/")."/models/Bc_Model.php");

class News_model extends Bc_Model {

    public function update($user_id,DateTime $date,$html,$artist_ids,$exhibition_ids,$news_id=null) {
        $this->db->trans_start();
        $this->db->set("date",$date->format("Y-m-d"))
            ->set("html",strip_tags($html,'<p><a><strong><em>'));
        if ($news_id) {
            $this->db->where("id",$news_id);
            $this->db->update("news");
            $this->log($user_id);
            $this->db->where("news_id",$news_id);
            $this->db->delete("news_artist");
            $this->log($user_id);
            $this->db->where("news_id",$news_id);
            $this->db->delete("news_exhibition");
            $this->log($user_id);
        } else {
            if ($this->db->insert("news") !== false) {
                $news_id = $this->db->insert_id();
                $this->log($user_id);
            } else {
                return false;
            }
        }
        if (!empty($artist_ids)) {
            $batch = array();
            foreach ($artist_ids as $id) {
                $batch[] = array("artist_id"=>$id,"news_id"=>$news_id);
            }
            $this->db->insert_batch("news_artist",$batch);
            $this->log($user_id);
        }
        if (!empty($exhibition_ids)) {
            $batch = array();
            foreach ($exhibition_ids as $id) {
                $batch[] = array("exhibition_id"=>$id,"news_id"=>$news_id);
            }
            $this->db->insert_batch("news_exhibition",$batch);
            $this->log($user_id);
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== false;
    }

    private function select($modify_callback=null) {
        $this->db->select("news.id, news.html, news.date, news_artist.artist_id, artist.name, artist.surname, exhibition.title, news_exhibition.exhibition_id")
            ->from("news")
            ->join("news_artist JOIN artist ON artist.id = news_artist.artist_id","news_artist.news_id = news.id","left")
            ->join("news_exhibition JOIN exhibition ON exhibition.id = news_exhibition.exhibition_id","news_exhibition.news_id = news.id","left")
            ->order_by("news.date","desc");
        if (is_callable($modify_callback)) {
            $modify_callback();
        }
        $response = array();
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result_array() as $row) {
                if (!array_key_exists($row["id"],$response)) {
                    $response[$row["id"]] = array(
                        "id"=>$row["id"],
                        "html"=>$row["html"],
                        "date"=>$row["date"],
                        "artists"=>array(),
                        "exhibitions"=>array()
                    );
                }
                if ($row["artist_id"]) {
                    $response[$row["id"]]["artists"][$row["artist_id"]] = trim($row["name"].' '.$row["surname"]);
                }
                if ($row["exhibition_id"]) {
                    $response[$row["id"]]["exhibitions"][$row["exhibition_id"]] = $row["title"];
                }
            }
            $response = array_values($response);
        }
        return $response;
    }

    public function get_artist_news($artist_id) {
        $me = $this;
        return $this->select(function() use($me,$artist_id) {
            $me->db->where("exists (select 1 from news_artist where news_artist.news_id = news.id and news_artist.artist_id = ".$me->db->escape($artist_id)." group by news.id)",null,false);
        });
    }

    public function get_exhibition_news($exhibition_id) {
        $me = $this;
        return $this->select(function() use($me,$exhibition_id) {
            $me->db->where("news_exhibition.exhibition_id",$exhibition_id);
        });
    }

    public function get_all_news($interval_months=null) {
        if (!$interval_months) {
            return $this->select();
        }
        $me = $this;
        return $this->select(function() use($me,$interval_months) {
            $me->db->where("news.date >= now() - interval ".$interval_months." month",null,false);
        });
    }

    public function get_news($news_id) {
        $me = $this;
        $news = $this->select(function() use($me,$news_id) {
            $me->db->where("news.id",$news_id);
        });
        if (!empty($news)) {
            return $news[0];
        }
        return null;
    }

    public function delete($news_id) {
        $this->db->where("id",$news_id);
        return $this->db->delete("news") !== false;
    }
}