<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'bc_controller.php';

/**
 * @property Artist_model $artist_model
 * @property Image_model $image_model
 */

class Image extends Bc_controller {

    function __construct() {
        parent::__construct();
        $this->load->model("artist_model");
        $this->load->model("image_model");
    }

    public function artist_image($artist_id,$image_id) {
        $header_vars = $this->get_header_vars();
        $this->load->view("header",$header_vars);
        $artist = $this->artist_model->get_artist($artist_id);
        $images = $this->image_model->get_artist_images($artist_id);
        $this->output->append_output('<h1>'.$artist["name"].'</h1>');
        $this->load->view("image",array("artist"=>$artist,"image_id"=>$image_id,"images"=>$images));
        $this->load->view("footer");
    }

    public function jpeg($dir,$id) {
        $dirs = array(
            "195","410","2mp","original"
        );
        $filename = rtrim(FCPATH,"/")."/images/".$dir."/".$id.".jpg";
        if (in_array($dir,$dirs) && file_exists($filename)) {
            $this->output->set_content_type("image/jpeg");
            $this->output->set_header("Cache-Control: public, max-age=".(60*60*24*365*5));
            $this->output->set_header("Expires: ".date("r",time()+60*60*24*365*5));
            $this->output->set_output(file_get_contents($filename));
        }
    }
}