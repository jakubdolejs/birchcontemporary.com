<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'bc_controller.php';

/**
 * @property Artist_model $artist_model
 * @property Image_model $image_model
 * @property Exhibition_model $exhibition_model
 * @property News_model $news_model
 */

class Artist extends Bc_controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model("artist_model");
        $this->load->model("image_model");
        $this->load->model("exhibition_model");
        $this->load->model("news_model");
    }
    
    public function index() {
        $header_vars = $this->get_header_vars("/artists");
        $this->load->view("header",$header_vars);
        $artists = $this->artist_model->get_artists(false,true);
        $this->load->view("artist",array("artists"=>$artists));
        $this->load->view("footer");
    }
    
    public function view($artist_id,$show_archived=0) {
        $header_vars = $this->get_header_vars("/artists");
        $this->load->view("header",$header_vars);
        $artist = $this->artist_model->get_artist($artist_id);
        $images = $this->image_model->get_artist_images_with_details($artist_id);
        $featured = array();
        $archived = array();
        foreach ($images as $image) {
            if ($image["featured"]) {
                $featured[] = $image;
            } else {
                $archived[] = $image;
            }
        }
        $this->output->append_output('<h1>'.htmlspecialchars(trim($artist["name"].' '.$artist["surname"])).'</h1>');
        $links = array("links"=>$this->get_tabs($artist_id,"images"));
        $this->load->view("link_group",$links);
        $this->load->view("artist_images",array("artist"=>$artist,"featured"=>$featured,"archived"=>$archived,"show_archived"=>$show_archived));
        $this->load->view("footer");
    }

    public function cv($artist_id) {
        $filename = rtrim(FCPATH,"/")."/cv_pdf/".$artist_id.".pdf";
        if (file_exists($filename) && ($pdf = file_get_contents($filename)) !== false) {
            $this->output->set_header("Content-Type: application/pdf");
            $this->output->set_header("Content-Disposition: inline; filename=\"".$artist_id.".pdf\"");
            $this->output->set_header("Content-Length: ".strlen($pdf));
            $this->output->set_output($pdf);
        } else {
            $header_vars = $this->get_header_vars("/artists");
            $this->load->view("header",$header_vars);
            $artist = $this->artist_model->get_artist($artist_id);
            $this->output->append_output('<h1>'.htmlspecialchars(trim($artist["name"].' '.$artist["surname"])).'</h1>');
            $links = array("links"=>$this->get_tabs($artist_id,"cv"));
            $this->load->view("link_group",$links);
            $this->load->view("artist_cv",array("artist"=>$artist));
            $this->output->append_output('<h1>Error</h1><p>Error reading PDF file.</p>');
            $this->load->view("footer");
        }
    }

    public function download_cv($file) {
        if (!preg_match('/^([a-z0-9_\-]+)\.pdf$/i',$file,$match)) {
            return;
        }
        $artist_id = $match[1];
        $filename = rtrim(FCPATH,"/")."/cv_pdf/".$file;
        $pdf = file_get_contents($filename);
        $this->output->set_header("Content-Type: application/pdf");
        $this->output->set_header("Content-Disposition: attachment");
        $this->output->set_header("Content-Length: ".strlen($pdf));
        $this->output->set_output($pdf);
    }

    public function exhibitions($artist_id) {
        $header_vars = $this->get_header_vars("/artists");
        $this->load->view("header",$header_vars);
        $artist = $this->artist_model->get_artist($artist_id);
        $exhibitions = $this->exhibition_model->get_artist_exhibitions($artist_id);
        $this->output->append_output('<h1>'.htmlspecialchars(trim($artist["name"].' '.$artist["surname"])).'</h1>');
        $links = array("links"=>$this->get_tabs($artist_id,"exhibitions"));
        $this->load->view("link_group",$links);
        foreach ($exhibitions as $exhibition) {
            $this->load->view("exhibition_listing",array("exhibition"=>$exhibition,"image_size"=>"195","classes"=>array("small")));
        }
        $this->load->view("footer");
    }

    public function exhibition($artist_id,$exhibition_id) {
        $header_vars = $this->get_header_vars("/artists");
        $this->load->view("header",$header_vars);
        $artist = $this->artist_model->get_artist($artist_id);
        $this->output->append_output('<h1>'.htmlspecialchars(trim($artist["name"].' '.$artist["surname"])).'</h1>');
        $links = array("links"=>$this->get_tabs($artist_id,"exhibitions"));
        $this->load->view("link_group",$links);
        $exhibition = $this->exhibition_model->get_exhibition($exhibition_id);
        $images = $this->image_model->get_exhibition_images($exhibition_id);
        $this->load->view("exhibition",array("exhibition"=>$exhibition,"images"=>$images,"artist_id"=>$artist_id));
        $this->load->view("footer");
    }

    public function news($artist_id) {
        $header_vars = $this->get_header_vars("/artists");
        $this->load->view("header",$header_vars);
        $artist = $this->artist_model->get_artist($artist_id);
        $this->output->append_output('<h1>'.htmlspecialchars(trim($artist["name"].' '.$artist["surname"])).'</h1>');
        $links = array("links"=>$this->get_tabs($artist_id,"news"));
        $this->load->view("link_group",$links);
        $news = $this->news_model->get_artist_news($artist_id);
        $this->load->view("news",array("news"=>$news,"artist_id"=>$artist_id));
        $this->load->view("footer");
    }

    public function image($artist_id,$image_id) {
        $images = $this->image_model->get_artist_images_with_details($artist_id);
        $base_url = "/artist/".$artist_id."/image/";
        $this->load->view("image",array("parent_url"=>"/artist/".$artist_id,"base_url"=>$base_url,"images"=>$images,"image_id"=>$image_id));
    }

    public function exhibition_image($artist_id,$exhibition_id,$image_id) {
        $images = $this->image_model->get_exhibition_images_with_details($exhibition_id);
        $base_url = "/artist/".$artist_id."/exhibition/".$exhibition_id."/image/";
        $this->load->view("image",array("parent_url"=>"/artist/".$artist_id."/exhibition/".$exhibition_id,"base_url"=>$base_url,"images"=>$images,"image_id"=>$image_id));
    }

    private function get_tabs($artist_id,$selected=null) {
        $links = array();
        $sections = $this->artist_model->get_artist_sections($artist_id);
        if ($sections["images"]) {
            $links[] = array(
                "url"=>"/artist/".$artist_id,
                "label"=>"Works"
            );
            if ($selected == "images") {
                $links[0]["selected"] = true;
            }
        }
        if ($sections["exhibitions"]) {
            $links[] = array(
                "url"=>"/artist/".$artist_id."/exhibitions",
                "label"=>"Exhibitions"
            );
            if ($selected == "exhibitions") {
                $links[count($links)-1]["selected"] = true;
            }
        }
        if ($sections["cv"]) {
            $links[] = array(
                "url"=>"/artist/".$artist_id."/cv",
                "label"=>"CV"
            );
            if ($selected == "cv") {
                $links[count($links)-1]["selected"] = true;
            }
        }
        if ($sections["news"]) {
            $links[] = array(
                "url"=>"/artist/".$artist_id."/news",
                "label"=>"News"
            );
            if ($selected == "news") {
                $links[count($links)-1]["selected"] = true;
            }
        }
        return $links;
    }
}