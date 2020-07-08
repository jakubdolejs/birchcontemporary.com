<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 */
class Bc_controller extends CI_Controller {
    
    protected function get_header_vars($selected=null) {
        $vars = array();
        if ($selected) {
            $vars["selected"] = $selected;
        }
        return $vars;
    }
}