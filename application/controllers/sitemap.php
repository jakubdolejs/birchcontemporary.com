<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'bc_controller.php';

/**
 * Class Sitemap
 * @property Artist_model $artist_model
 * @property Exhibition_model $exhibition_model
 * @property News_model $news_model
 */

class Sitemap extends Bc_controller {

    function __construct() {
        parent::__construct();
        $this->load->model("artist_model");
        $this->load->model("exhibition_model");
        $this->load->model("news_model");
    }

    public function index() {
        $cache_key = "xml_sitemap";
        header("Content-type: text/xml; charset=utf-8");
        $vars = $this->get_header_vars();
        $host = $this->input->server("HTTP_HOST");
        $this->output->append_output('<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<url><loc>http://'.$host.'/</loc><priority>1.0</priority></url>');
        $this->output->append_output('<url><loc>http://'.$host.'/exhibitions</loc><priority>1.0</priority></url>');
        $this->output->append_output('<url><loc>http://'.$host.'/artists</loc><priority>1.0</priority></url>');
        //$this->output->append_output('<url><loc>http://'.$host.'/news</loc><priority>1.0</priority></url>');
        $this->output->append_output('<url><loc>http://'.$host.'/contact</loc><priority>1.0</priority></url>');
        $exhibitions = $this->exhibition_model->get_all_exhibitions();
        foreach ($exhibitions as $exhibition) {
            $this->output->append_output('<url><loc>http://'.$host.'/exhibition/'.$exhibition["id"].'</loc><priority>0.9</priority></url>');
        }
        $artists = $this->artist_model->get_artists();
        foreach ($artists as $artist) {
            $this->output->append_output('<url><loc>http://'.$host.'/artist/'.$artist["id"].'</loc><priority>0.9</priority></url>');
        }
        /*
        $news = $this->news_model->get_news();
        foreach ($news as $story) {
            foreach ($story["galleries"] as $gallery) {
                $this->output->append_output('<url><loc>http://'.$host.'/news/'.$story["id"].'</loc><priority>0.9</priority></url>');
            }
        }
        */
        $this->output->append_output('</urlset>');
    }
}