<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bc_Model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->driver("cache");
    }

    public function log($user_id) {
        $this->db->set("user_id",$user_id)
            ->set("query",end($this->db->queries))
            ->set("time",time());
        $this->db->insert("admin_log");
    }
}