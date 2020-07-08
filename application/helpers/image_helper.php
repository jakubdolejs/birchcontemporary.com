<?php
if (!function_exists("resize_image")) {
    function resize_image($img_file,$ext,$crop,$user_id,$vimeo_id=null) {
        $CI =& get_instance();
        $CI->load->model("image_model");

        $exif = @exif_read_data($img_file,NULL,TRUE);

        getimagesize($img_file, $info);

        $image_data = file_get_contents($img_file);

        $original = imagecreatefromstring($image_data);
        unset($image_data);

        if (!$original) {
            return array("error"=>"Cannot read image file.");
        }

        $meta = array();
        $meta["original_width"] = $src_w = $w = imagesx($original);
        $meta["original_height"] = $src_h = $h = imagesy($original);
        $whratio = $src_w/$src_h;
        $original_mp = $src_w*$src_h;
        $target_mp = 2000000;
        if ($original_mp > $target_mp) {
            $h = floor(sqrt($target_mp/($src_w/$src_h)));
            $w = round($h*$whratio);
        }
        $src_x = $src_y = 0;
        $rotation = 0;
        if (isset($exif['IFD0']['Orientation'])) {
            switch ($exif['IFD0']['Orientation']) {
                case 2:
                    //horizontal flip
                    $src_x = $src_w-1;
                    $src_w = 0-$src_w;
                    break;
                case 3:
                    $rotation = 180;
                    break;
                case 4:
                    //vertical flip
                    $src_y = $src_h-1;
                    $src_h = 0-$src_h;
                    break;
                case 5:
                    //vertical flip
                    $meta["original_width"] = $src_h;
                    $meta["original_height"] = $src_w;
                    $src_y = $src_h-1;
                    $src_h = 0-$src_h;
                    $rotation = -90;
                    break;
                case 6:
                    $meta["original_width"] = $src_h;
                    $meta["original_height"] = $src_w;
                    $rotation = -90;
                    break;
                case 7:
                    //horizontal flip
                    $meta["original_width"] = $src_h;
                    $meta["original_height"] = $src_w;
                    $src_x = $src_w-1;
                    $src_w = 0-$src_w;
                    $rotation = -90;
                    break;
                case 8:
                    $meta["original_width"] = $src_h;
                    $meta["original_height"] = $src_w;
                    $rotation = 90;
                    break;

            }

        }
        $version = 0;
        if ($CI->input->post("image_id")) {
            $image_id = $CI->input->post("image_id",true);
            $version = $CI->image_model->update_dimensions($user_id,$image_id,$meta["original_width"],$meta["original_height"]);
        } else {
            $image_id = $CI->image_model->insert($user_id,$meta["original_width"],$meta["original_height"],$vimeo_id);
            $version = 0;
        }

        if (!$image_id) {
            imagedestroy($original);
            return array("error"=>"Unable to insert image to the database","id"=>$image_id);
        }

        $images_dir = rtrim(FCPATH,"/")."/images";

        $error = null;

        if (!rename($img_file,$images_dir."/original/".$image_id.".".$ext)) {
            imagedestroy($original);
            $CI->image_model->delete($user_id,$image_id,$error);
            return array("error"=>"Unable to save uploaded file.","id"=>$image_id);
        }

        $target = imagecreatetruecolor($w,$h);
        $scale = $w/$src_w;
        if ($w != $src_w) {
            imagecopyresampled($target, $original, 0, 0, $src_x, $src_y, $w, $h, $src_w, $src_h);
        } else {
            imagecopy($target, $original, 0, 0, $src_x, $src_y, $w, $h);
        }

        if ($rotation != 0) {
            $target = imagerotate($target, $rotation, 0);
            $original = imagerotate($original,$rotation,0);
        }
        $large_file = $images_dir."/2mp/".$image_id.".jpg";
        imageinterlace($target,1);
        $success = imagejpeg($target, $large_file, 90);
        imagedestroy($target);

        if (!$success) {
            @imagedestroy($original);
            $CI->image_model->delete($user_id,$image_id,$error);
            return array("error"=>"Unable to save file ".$large_file,"id"=>$image_id);
        }

        if ($crop) {
            if (is_string($crop)) {
                $crop = explode(",",$crop);
            }

            $sq410 = imagecreatetruecolor(410,410);
            imagecopyresampled($sq410, $original, 0, 0, $crop[0], $crop[1], 410, 410, $crop[2], $crop[3]);
            imageinterlace($sq410,1);
            $success = imagejpeg($sq410, $images_dir."/410/".$image_id.".jpg");
            imagedestroy($sq410);

            if (!$success) {
                @imagedestroy($original);
                $CI->image_model->delete($user_id,$image_id,$error);
                return array("error"=>"Unable to save file ".$images_dir."/410/".$image_id.".jpg","id"=>$image_id);
            }

            $sq195 = imagecreatetruecolor(195,195);
            imagecopyresampled($sq195, $original, 0, 0, $crop[0], $crop[1], 195, 195, $crop[2], $crop[3]);
            imageinterlace($sq195,1);
            $success = imagejpeg($sq195, $images_dir."/195/".$image_id.".jpg");
            imagedestroy($sq195);

            if (!$success) {
                @imagedestroy($original);
                $CI->image_model->delete($user_id,$image_id,$error);
                return array("error"=>"Unable to save file ".$images_dir."/410/".$image_id.".jpg");
            }
        } else {
            @imagedestroy($original);
            $CI->image_model->delete($user_id,$image_id,$error);
            return array("error"=>"Missing cropping information for file ".$images_dir."/410/".$image_id.".jpg","id"=>$image_id);
        }
        imagedestroy($original);
        return array("id"=>$image_id,"version"=>$version);
    }
}