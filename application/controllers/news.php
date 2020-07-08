<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'bc_controller.php';

/**
 * @property Artist_model $artist_model
 * @property Image_model $image_model
 * @property Exhibition_model $exhibition_model
 * @property News_model $news_model
 */

class News extends Bc_controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model("artist_model");
        $this->load->model("image_model");
        $this->load->model("exhibition_model");
        $this->load->model("news_model");
    }
    
    public function index() {
        $header_vars = $this->get_header_vars("/news");
        $this->load->view("header",$header_vars);
        $this->output->append_output('<h1>News</h1>');
        $news = $this->news_model->get_all_news(6);
        if (empty($news)) {
            $news = $this->news_model->get_all_news();
            $news = array_slice($news,0,6,false);
        }
        $this->load->view("news",array("news"=>$news));
        $this->load->view("footer");
    }
}