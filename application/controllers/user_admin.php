<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "admin.php";

/**
 * Class User_admin
 * @property Image_model $image_model
 * @property Artist_model $artist_model
 */

class User_admin extends Admin {

    function __construct() {
        parent::__construct();
        $this->load->helper("bcrypt");
    }

    public function index() {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header",array("user"=>$user));
        if (!$user["superuser"]) {
            $this->output->append_output('<h1>Access Denied</h1><p>Only superusers are allowed to edit other users\' accounts.</p>');
            $this->load->view("admin/footer.php");
            return;
        }
        $users = $this->admin_login_model->get_all_users();
        $this->output->append_output('<h1>Users</h1><p><a class="button" href="/admin/user/add">Add user</a></p>');
        $this->load->view("admin/user_list",array("users"=>$users,"user"=>$user));
        $this->load->view("admin/footer.php");
    }

    public function add() {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header",array("user"=>$user));
        if (!$user["superuser"]) {
            $this->output->append_output('<h1>Access Denied</h1><p>Only superusers are allowed to add other users.</p>');
            $this->load->view("admin/footer.php");
            return;
        }
        if ($this->input->post("save")) {
            $user_id = $this->input->post("id",true);
            if (!$user_id) {
                $this->output->append_output('<h1>Error</h1><p>User id not supplied.</p>');
            } else {
                $user_id = strtolower($user_id);
                if ($this->admin_login_model->add_user($user["id"],$user_id,$this->input->post("superuser",true))) {
                    $link = site_url("/admin/user/password/".md5($user_id));
                    $msg = "An account has been created for you by the system administrator. Please go to ".$link." to set a password.";
                    mail($user_id,"Your new birchcontemporary.com account",$msg);
                    $this->output->append_output('<h1>Success</h1><p>Created account for '.$user_id.'.</p><p>An email has been sent to the user with instructions on setting the password.</p><p><a class="button" href="/admin/users">OK</a></p>');
                } else {
                    $this->output->append_output('<h1>Error</h1><p>Error creating an account for '.$user_id.'. The user may already exist.</p>');
                }
            }
        } else {
            $params = array("user"=>null,"logged_in_user"=>$user);
            $this->load->view("admin/user",$params);
        }
        $this->load->view("admin/footer.php");
    }

    public function edit() {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $user_id = $this->input->post("id",true);
        if (!$user_id) {
            $user_id = $user["id"];
        }
        $this->load->view("admin/header",array("user"=>$user));
        if (!$user["superuser"] && $user["id"] != $user_id) {
            $this->output->append_output('<h1>Access Denied</h1><p>You are not permitted to edit this account.</p>');
            $this->load->view("admin/footer.php");
            return;
        }
        $edited_user = $this->admin_login_model->get_user_by_email($user_id);
        if ($this->input->post("save")) {
            if (!$user["superuser"]) {
                $this->output->append_output('<h1>Access Denied</h1><p>You are not permitted to edit this account.</p>');
                $this->load->view("admin/footer.php");
                return;
            }
            $this->admin_login_model->edit_user($user["id"],$this->input->post("id",true),$this->input->post("superuser",true));
            $this->output->append_output('<h1>Success</h1><p>'.$user_id.'\'s record has been updated.</p><p><a class="button" href="/admin/users">OK</a></p>');
        } else {
            $params = array("user"=>$edited_user,"logged_in_user"=>$user);
            $this->load->view("admin/user",$params);
        }
        $this->load->view("admin/footer.php");
    }

    public function password($checksum=null) {
        if ($checksum) {
            $edited_user = $this->admin_login_model->get_user_by_email($checksum,true);
            $this->load->view("admin/header");
            if (empty($edited_user)) {
                $this->output->append_output('<h1>Error</h1><p>The user account does not exist or has been suspended.</p>');
            } else if ($edited_user["password_checksum"]) {
                $this->output->append_output('<h1>Error</h1><p>The password for this account has already been set.</p>');
            } else {
                $this->load->view("admin/set_password",array("user"=>$edited_user));
            }
            $this->load->view("admin/footer");
            return;
        }
        if ($this->input->post("save")) {
            $user_id = $this->input->post("id",true);
            $this->load->view("admin/header");
            if ($user_id) {
                $edited_user = $this->admin_login_model->get_user_by_email($user_id);
                if (!empty($edited_user)) {
                    $pwd1 = $this->input->post("password1");
                    $pwd2 = $this->input->post("password2");
                    if (!empty($pwd1) && !empty($pwd2)) {
                        if (strlen($pwd1) > 7) {
                            if ($pwd1 == $pwd2) {
                                if ($edited_user["password_checksum"]) {
                                    $old_pwd = $this->input->post("old_password");
                                    if ($old_pwd) {
                                        if (bcrypt_hash($old_pwd) != $edited_user["password_checksum"]) {
                                            $this->output->append_output('<h1>Error</h1><p>The old password for this account does not match the password you entered.</p>');
                                            $this->load->view("admin/footer");
                                            return;
                                        }
                                    } else {
                                        $this->output->append_output('<h1>Error</h1><p>The password for this account has already been set.</p>');
                                        $this->load->view("admin/footer");
                                        return;
                                    }
                                }
                                $password_checksum = bcrypt_hash($pwd1);
                                $this->admin_login_model->reset_password($user_id,$password_checksum);
                                $this->output->append_output('<h1>Password Reset</h1><p>Your password has been reset.</p><p><a href="/admin/login">Log in</a></p>');
                            } else {
                                $this->output->append_output('<h1>Error</h1><p>The password does not match the re-typed password.</p>');
                            }
                        } else {
                            $this->output->append_output('<h1>Error</h1><p>The password must be at least 8 characters long.</p>');
                        }
                    } else {
                        $this->output->append_output('<h1>Error</h1><p>The password or the re-typed password were not entered.</p>');
                    }

                } else {
                    $this->output->append_output('<h1>Error</h1><p>The user account does not exist or has been suspended.</p>');
                }
            } else {
                $this->output->append_output('<h1>Error</h1><p>The user account id is missing.</p>');
            }
            $this->load->view("admin/footer");
            return;
        }

        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $user_id = $this->input->post("id",true);
        $this->load->view("admin/header",array("user"=>$user));
        if (!$user["superuser"] && $user["id"] != $user_id || !$user_id) {
            $this->output->append_output('<h1>Access Denied</h1><p>You are not permitted to edit this account.</p>');
            $this->load->view("admin/footer.php");
            return;
        }
        if ($user["superuser"] && $user["id"] != $user_id) {
            $this->admin_login_model->reset_access($user['id'],$user_id);
            $link = site_url("/admin/user/password/".md5($user_id));
            $msg = "Your password has been reset by the administrator. Please go to ".$link." to set a new password.";
            mail($user_id,"birchcontemporary.com password reset",$msg);
            $this->output->append_output('<h1>Password reset</h1><p>The password for '.$user_id.' has been reset and an email with instructions on how to set a new password has been emailed to the user.</p>');
        } else if ($user_id) {
            $edited_user = $this->admin_login_model->get_user_by_email($user_id);
            if ($edited_user) {
                $this->load->view("admin/set_password",array("user"=>$edited_user));
            } else {
                $this->output->append_output('<h1>Error</h1><p>The user account does not exist or has been suspended.</p>');
            }
        }
        $this->load->view("admin/footer.php");
    }

    public function delete() {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $user_id = $this->input->post("id",true);
        $this->load->view("admin/header",array("user"=>$user));
        if ($user_id) {
            if (!$user["superuser"] && $user["id"] != $user_id || !$user_id) {
                $this->output->append_output('<h1>Access Denied</h1><p>You are not permitted to delete this account.</p>');
            } else {
                if ($this->admin_login_model->delete_account($user["id"],$user_id)) {
                    $this->output->append_output('<h1>Success</h1><p>User '.$user_id.' has been deleted.</p>');
                } else {
                    $this->output->append_output('<h1>Error</h1><p>We were unable to delete the user '.$user_id.' at this time.</p>');
                }
            }
        } else {
            $this->output->append_output('<h1>Error</h1><p>We were unable to delete the user. No user id supplied.</p>');
        }
        $this->output->append_output('<p><a class="button" href="/admin/users">OK</a></p>');
        $this->load->view("admin/footer.php");
    }

    public function images($artist_id) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header",array("user"=>$user));
        $all_images = $this->image_model->get_artist_images($artist_id);
        $artist = $this->artist_model->get_artist($artist_id);
        $this->load->view("admin/artist_gallery_images",array("all_images"=>$all_images,"artist_name"=>$artist["name"],"artist_id"=>$artist_id));
        $this->load->view("admin/footer.php");
    }
}