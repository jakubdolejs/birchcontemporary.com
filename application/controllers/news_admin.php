<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "admin.php";

/**
 * Class Image_admin
 * @property News_model $news_model
 * @property Artist_model $artist_model
 * @property Exhibition_model $exhibition_model
 */

class News_admin extends Admin {

    function __construct() {
        parent::__construct();
        $this->load->model("news_model");
        $this->load->model("artist_model");
        $this->load->model("exhibition_model");
    }

    public function index($news_id=null) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header",array("user"=>$user));
        if ($this->input->post("html") && $this->input->post("date")) {
            if ($this->news_model->update($user["id"],DateTime::createFromFormat("Y-m-d",$this->input->post("date",true)),$this->input->post("html",true),$this->input->post("artist_ids",true),$this->input->post("exhibition_ids",true),$news_id)) {
                $this->output->append_output('<h1>Story added</h1>');
            } else {
                $this->output->append_output('<h1>Error adding news story</h1>');
            }
            $this->output->append_output('<p><a href="/admin/news">OK</a></p>');
        } else if ($news_id) {
            $story = $this->news_model->get_news($news_id);
            $artists = $this->artist_model->get_artists();
            $exhibitions = $this->exhibition_model->get_all_exhibitions();
            $this->load->view("admin/news",array("news"=>$story,"artists"=>$artists,"exhibitions"=>$exhibitions));
        } else {
            $news = $this->news_model->get_all_news();
            $this->output->append_output('<h1>News</h1><p><a href="/admin/news/create">Add news</a></p>');
            $this->load->view("admin/news_list",array("news"=>$news));
        }
        $this->load->view("admin/footer");
    }

    public function create() {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header",array("user"=>$user));
        $artists = $this->artist_model->get_artists();
        $exhibitions = $this->exhibition_model->get_all_exhibitions();
        $this->load->view("admin/news",array("artists"=>$artists,"exhibitions"=>$exhibitions));
        $this->load->view("admin/footer");
    }

    public function delete($news_id) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header",array("user"=>$user));
        if ($this->news_model->delete($news_id)) {
            $this->output->append_output('<h1>Story deleted</h1>');
        } else {
            $this->output->append_output('<h1>Error deleting news story</h1>');
        }
        $this->output->append_output('<p><a href="/admin/news">OK</a></p>');
        $this->load->view("admin/footer");
    }
}