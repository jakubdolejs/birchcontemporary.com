<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'bc_controller.php';
/**
 * @property Exhibition_model $exhibition_model
 * @property Image_model $image_model
 */
class Exhibition extends Bc_controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model("exhibition_model");
        $this->load->model("image_model");
    }
    
    public function index() {
        $current = $this->exhibition_model->get_exhibitions("current");
        $upcoming = $this->exhibition_model->get_exhibitions("upcoming");
        $past = $this->exhibition_model->get_exhibitions("past");
        $header_vars = $this->get_header_vars("/exhibitions");
        $this->load->view("header",$header_vars);
        $this->load->view("exhibitions",array("current"=>$current,"upcoming"=>$upcoming,"past"=>$past));
        $this->load->view("footer");
    }
    
    public function view($exhibition_id) {
        $exhibition = $this->exhibition_model->get_exhibition($exhibition_id);
        $images = $this->image_model->get_exhibition_images($exhibition_id);
        $header_vars = $this->get_header_vars("/exhibitions");
        $this->load->view("header",$header_vars);
        $this->load->view("exhibition",array("exhibition"=>$exhibition,"images"=>$images));
        $this->load->view("footer");
    }

    public function past() {
        $header_vars = $this->get_header_vars("/exhibitions");
        $past = $this->exhibition_model->get_exhibitions("past");
        $this->load->view("header",$header_vars);
        $this->output->append_output('<h1>Past Exhibitions</h1>');
        if (!empty($past)) {
            foreach ($past as $exhibition) {
                $this->load->view("exhibition_listing",array("exhibition"=>$exhibition,"image_size"=>"195","classes"=>array("small")));
            }
        }
    }

    public function image($exhibition_id,$image_id) {
        $images = $this->image_model->get_exhibition_images_with_details($exhibition_id);
        $base_url = "/exhibition/".$exhibition_id."/image/";
        $this->load->view("image",array("parent_url"=>"/exhibition/".$exhibition_id,"base_url"=>$base_url,"images"=>$images,"image_id"=>$image_id));
    }
}