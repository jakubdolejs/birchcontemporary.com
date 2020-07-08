<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "admin.php";

/**
 * Class Image_admin
 * @property Image_model $image_model
 * @property Artist_model $artist_model
 */

class Image_admin extends Admin {

    function __construct() {
        parent::__construct();
        $this->load->model("image_model");
        $this->load->model("artist_model");
        $this->load->helper("image_helper");
    }

    public function index() {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header",array("user"=>$user));
        $images = $this->image_model->get_list();
        $this->output->append_output('<h1>Images</h1>');
        $this->load->view("admin/image_upload",array("images"=>$images));
        $this->load->view("admin/footer");
    }

    public function delete($id) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header",array("user"=>$user));
        if ($this->image_model->delete($user["id"],$id,$error)) {
            $this->output->append_output('<h1>Image deleted</h1><p>The image has been deleted.</p><p><a href="/admin/images">OK</a></p>');
        } else {
            $reason = "The script was unable to delete the image from the database.";
            if ($error["code"] > 0) {
                $reason = "";
                if (!empty($error["exhibitions"])) {
                    $reason .= "<p>The image is used in the ".(count($error["exhibitions"]) > 1 ? "following exhibitions: " : "exhibition ");
                    $exhibitions = array();
                    foreach ($error["exhibitions"] as $eid) {
                        $exhibitions[] = '<a href="/admin/exhibition/'.$eid.'">'.$eid.'</a>';
                    }
                    $reason .= join(", ",$exhibitions).'</p>';
                }
                /*
                if (!empty($error["news"])) {
                    $reason .= "<p>The image is used in the ".(count($error["news"]) > 1 ? "following news stories: " : "news ");
                    $news = array();
                    foreach ($error["news"] as $story) {
                        $news[] = '<a href="/admin/news/'.$story.'">story id '.$story.'</a>';
                    }
                    $reason .= join(", ",$news).'</p>';
                }
                */
            }
            $this->output->append_output('<h1>Error deleting image</h1><p>'.$reason.'</p><p><a href="/admin/images">OK</a></p>');
        }
    }

    public function float_check($val) {
        if (!$val) {
            return true;
        }
        return preg_match('/^\d+(\.\d+)*$/',$val) == 1;
    }

    public function edit($image_id) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header",array("user"=>$user));
        if ($this->input->post("save")) {
            $this->load->library("form_validation");
            $this->form_validation->set_rules('width','trim|callback_float_check');
            $this->form_validation->set_rules('height','trim|callback_float_check');
            $this->form_validation->set_rules('depth','trim|callback_float_check');
            if ($this->form_validation->run() == true) {
                $this->image_model->update($user["id"],$image_id,$this->input->post("width"),$this->input->post("height"),$this->input->post("depth"),$this->input->post("year"),$this->input->post("artists"),$this->input->post("title"),$this->input->post("description"));
                $this->output->append_output('<h1>Success</h1><p>Image updated</p><p><img src="/images/195/'.$image_id.'.jpg" /></p><p><a class="button" href="/admin/images">OK</a></p>');
            } else {
                $this->output->append_output('<h1>Error</h1><p>Error updating image. Please check that the dimensions are entered as numbers or left blank.</p><p><a class="button" href="/admin/image/'.$image_id.'">OK</a></p>');
            }
        } else {
            $image = $this->image_model->get($image_id);
            $artists = $this->artist_model->get_artists();
            $this->load->view("admin/image",array("image"=>$image,"artists"=>$artists));
        }
        $this->load->view("admin/footer");
    }

    private function resample($source_filename,$destination_filename,$max_width=null,$max_height=null,$crop=false) {
        $source = imagecreatefromjpeg($source_filename);
        if (!$source) {
            return false;
        }
        $src_w = $width = imagesx($source);
        $src_h = $height = imagesy($source);
        $src_x = $src_y = $dest_x = $dest_y = 0;
        $whratio = $src_w/$src_h;
        if ($max_width && $max_width == $max_height && $crop) { //crop to a square
            $width = $height = $max_width;
            if ($whratio > 1) {
                $src_x = round($src_w/2-$src_h/2);
                $src_w = $src_h;
            } else {
                $src_y = round($src_h/2-$src_w/2);
                $src_h = $src_w;
            }
        } else if ($max_width && $max_height) {
            if ($src_w/$src_h >= $max_width/$max_height) {
                $scaleRatio = $max_width/$src_w;
            } else {
                $scaleRatio = $max_height/$src_h;
            }
            $width = $src_w * $scaleRatio;
            $height = $src_h * $scaleRatio;
        } else if ($max_width && $max_width < $src_w) {
            $width = $max_width;
            $height = round($width/$whratio);
        } else if ($max_height && $max_height < $src_h) {
            $height = $max_height;
            $width = round($height*$whratio);
        }
        $destination = imagecreatetruecolor($width, $height);
        if (!$destination) {
            return false;
        }
        if ($width != $src_w || $height != $src_h) {
            imagecopyresampled($destination, $source, $dest_x, $dest_y, $src_x, $src_y, $width, $height, $src_w, $src_h);
        } else {
            imagecopy($destination, $source, $dest_x, $dest_y, $src_x, $src_y, $src_w, $src_h);
        }
        imagedestroy($source);
        imageinterlace($destination,1);
        $success = imagejpeg($destination,$destination_filename,95);
        imagedestroy($destination);
        return $success;
    }

    public function upload() {
        $user = $this->get_logged_in_user();
        if (!$user) {
            $this->load->view("json",array("data"=>array("error"=>"Not logged in")));
            return;
        }
        set_time_limit(0);
        ignore_user_abort(true);
        if (isset($_FILES["file"]) && !empty($_FILES["file"])) {
            $response = array();

            for ($i=0; $i<count($_FILES["file"]["tmp_name"]); $i++) {
                switch ($_FILES["file"]["error"][$i]) {
                    case UPLOAD_ERR_INI_SIZE:
                        //Value: 1; The uploaded file exceeds the upload_max_filesize directive in php.ini.
                        $response[$i] = array("error"=>"The uploaded file exceeds the maximum permitted upload size.");
                        break;
                        continue;
                    case UPLOAD_ERR_FORM_SIZE:
                        //Value: 2; The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.
                        $response[$i] = array("error"=>"The uploaded file exceeds the maximum permitted upload size.");
                        break;
                        continue;
                    case UPLOAD_ERR_PARTIAL:
                        //Value: 3; The uploaded file was only partially uploaded.
                        $response[$i] = array("error"=>"The file was only partially uploaded.");
                        break;
                        continue;
                    case UPLOAD_ERR_NO_FILE:
                        //Value: 4; No file was uploaded.
                        $response[$i] = array("error"=>"No file was uploaded.");
                        break;
                        continue;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        //Value: 6; Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.
                        $response[$i] = array("error"=>"Missing a temporary folder.");
                        break;
                        continue;
                    case UPLOAD_ERR_CANT_WRITE:
                        //Value: 7; Failed to write file to disk. Introduced in PHP 5.1.0.
                        $response[$i] = array("error"=>"Failed to write the uploaded file to disk.");
                        break;
                        continue;
                    case UPLOAD_ERR_EXTENSION:
                        //Value: 8; A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help. Introduced in PHP 5.2.0.
                        $response[$i] = array("error"=>"A PHP extension stopped the file upload.");
                        break;
                        continue;
                }

                $img_file = $_FILES["file"]["tmp_name"][$i];

                $crop = $this->input->post("crop",true);
                $response[$i] = resize_image($img_file,pathinfo($_FILES["file"]["name"][$i],PATHINFO_EXTENSION),$crop[$i],$user["id"]);
            }
        } else {
            $response = array("error"=>"No files submitted");
        }
        $this->load->view("json",array("data"=>$response));
    }
}