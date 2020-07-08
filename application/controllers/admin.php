<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Admin
 * @property Admin_login_model $admin_login_model
 */

class Admin extends CI_Controller {

    const ACCESS_TOKEN_COOKIE_NAME = "acctkn";

    function __construct() {
        parent::__construct();
        $this->load->model("admin_login_model");
        $this->load->helper("bcrypt");
        $this->load->helper("url");
        $this->load->library('encrypt');
    }

    public function index() {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header",array("user"=>$user));
        $this->load->view("admin/home",array("user"=>$user));
        $this->load->view("admin/footer");
    }

    public function login() {
        if ($this->input->post("email") && $this->input->post("password")) {
            $user = $this->admin_login_model->get_user_by_email($this->input->post("email",true));
            if (!$user) {
                $this->output_login_error();
                return;
            }
            if (!$user["password_checksum"]) {
                $this->load->view("header");
                $this->output->append_output('<h1>Error</h1><p>The password on your account has not yet been set. Please follow the link sent to you by email to set your password.</p>');
                $this->load->view("footer");
                return;
            }
            if (!bcrypt_check($this->input->post("password"),$user["password_checksum"])) {
                $this->output_login_error();
                return;
            }
            $token = $this->admin_login_model->issue_access_token($user["id"],substr($this->input->server("HTTP_USER_AGENT"),0,64),$this->input->server("REMOTE_ADDR"));
            $this->input->set_cookie(array(
                "name"=>self::ACCESS_TOKEN_COOKIE_NAME,
                "value"=>$this->encrypt->encode($token),
                "expire"=>time()+60*60*24*60,
                "path"=>"/",
                "secure"=>true
            ));
            $this->load->view("javascript_redirect",array("url"=>site_url("/admin")));
            return;
        } else {
            $this->load->view("admin/header",array("no_menu"=>true));
            $this->load->view("admin/login");
            $this->load->view("admin/footer");
        }
    }

    public function logout() {
        $this->delete_access();
        $this->load->view("admin/header");
        $this->load->view("admin/login");
        $this->load->view("admin/footer");
    }

    public function appcache() {
        header("Content-Type: text/cache-manifest");
        header("Cache-Control: no-cache");
        echo "CACHE MANIFEST

#rev ".$this->config->item("svn_revision")."

CACHE:
/fonts/CALIBRIL.TTF
/fonts/CALIBRILI.TTF

NETWORK:
/login
*.*
*
";
    }

    private function delete_access() {
        $this->input->set_cookie(array(
            "name"=>self::ACCESS_TOKEN_COOKIE_NAME,
            "value"=>null,
            "expire"=>time()-60*60*24*60,
            "path"=>"/"
        ));
        $token = $this->get_access_token();
        $this->admin_login_model->delete_access_token($token);
    }

    protected function output_login_error() {
        $this->load->view("admin/header");
        $this->output->append_output("<p>Error logging in</p>");
        $this->load->view("admin/footer");
    }

    protected final function get_logged_in_user() {
        if (isset($this->logged_in_user)) {
            return $this->logged_in_user;
        }
        if ($this->input->cookie(self::ACCESS_TOKEN_COOKIE_NAME)) {
            $cookie = $this->input->cookie(self::ACCESS_TOKEN_COOKIE_NAME);
            $this->logged_in_user = $this->admin_login_model->get_user_by_token($this->encrypt->decode($cookie),$this->input->server("HTTP_USER_AGENT"),$this->input->server("REMOTE_ADDR"));
        } else {
            $this->logged_in_user = false;
        }
        return $this->logged_in_user;
    }

    protected final function get_access_token() {
        if ($this->input->cookie(self::ACCESS_TOKEN_COOKIE_NAME)) {
            $cookie = $this->input->cookie(self::ACCESS_TOKEN_COOKIE_NAME);
            if ($cookie) {
                return $this->encrypt->decode($cookie);
            }
        }
        return null;
    }
}