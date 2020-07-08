<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'bc_controller.php';

class About extends Bc_controller {
    
    public function index() {
        $header_vars = $this->get_header_vars();
        $this->load->view("header",$header_vars);
        $this->output->append_output('<h1>About</h1>');
        $this->load->view("footer");
    }
}