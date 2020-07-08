<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "admin.php";

/**
 * Class Api
 * @property Artist_model $artist_model
 * @property Image_model $image_model
 * @property Exhibition_model $exhibition_model
 * @property Staff_model $staff_model
 */

class Api extends Admin {

    function __construct() {
        parent::__construct();
        $this->load->model("artist_model");
        $this->load->model("image_model");
        $this->load->model("exhibition_model");
    }

    public function artists() {
        $artists = $this->artist_model->get_artists();
        $this->load->view("json",array("data"=>$artists));
    }

    public function images() {
        $images = $this->image_model->get_list();
        $this->load->view("json",array("data"=>$images));
    }

    public function artist_images($artist_id) {
        if ($this->input->post("featured") !== false || $this->input->post("archived") !== false) {
            $user = $this->get_logged_in_user();
            if (!$user) {
                $this->output_login_error();
                return;
            }
            $featured = $this->input->post("featured",true);
            $archived = $this->input->post("archived",true);
            $images = array();
            if (!empty($featured)) {
                foreach ($featured as $id) {
                    $images[$id] = 1;
                }
            }
            if (!empty($archived)) {
                foreach ($archived as $id) {
                    $images[$id] = 0;
                }
            }
            $updated = $this->image_model->set_artist_images($user["id"],$artist_id,$images);
            $this->load->view("json",array("data"=>$updated));
        } else {
            $images = $this->image_model->get_artist_images_with_details($artist_id);
            $this->load->view("json",array("data"=>$images));
        }
    }

    public function delete_cv($artist_id) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            $this->output_login_error();
            return;
        }
        $artist = $this->artist_model->get_artist($artist_id);
        if (!$artist) {
            $this->output_error("Artist ".$artist_id." does not exist in the database.");
            return;
        }
        $filename = rtrim(FCPATH,"/")."/cv_pdf/".$artist_id.".pdf";
        $success = true;
        if (file_exists($filename)) {
            $success = unlink($filename);
        }
        $this->load->view("json",array("data"=>intval($success)));
    }

    public function exhibition_images($exhibition_id) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        if ($this->input->post("images") !== false) {
            $images = $this->input->post("images",true);
            $updated = $this->image_model->set_exhibition_images($user["id"],$exhibition_id,$images);
            $this->load->view("json",array("data"=>$updated));
        }
    }

    public function vimeo($vimeo_id) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $ch = curl_init("http://vimeo.com/api/v2/video/{$vimeo_id}.json");
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $video_info = curl_exec($ch);
        curl_close($ch);
        $video_info = json_decode($video_info);
        if (count($video_info) > 0) {
            if ($this->input->post("crop")) {
                ignore_user_abort(false);
                set_time_limit(0);
                $crop = $this->input->post("crop",true);
                $ext = pathinfo($video_info[0]->thumbnail_large,PATHINFO_EXTENSION);
                $this->load->helper("image_helper");
                $ch = curl_init($video_info[0]->thumbnail_large);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                $image = curl_exec($ch);
                curl_close($ch);
                $temp_file = tempnam(sys_get_temp_dir(),'img');
                file_put_contents($temp_file,$image);
                $response = resize_image($temp_file,$ext,$crop,$user["id"],$vimeo_id);
                $this->load->view("json",array("data"=>$response));
            } else {
                $this->load->view("json",array("data"=>$video_info[0]));
            }
        } else {
            $this->load->view("json",array("data"=>false));
        }
    }

    protected function output_login_error() {
        $this->load->view("json",array("data"=>array("error"=>"Error loggin in")));
    }

    protected function output_error($error) {
        $this->load->view("json",array("data"=>array("error"=>$error)));
    }
}