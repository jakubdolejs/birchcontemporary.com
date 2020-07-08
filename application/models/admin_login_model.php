<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(rtrim(APPPATH,"/")."/models/Bc_Model.php");

class Admin_login_model extends Bc_Model {

    public function get_user_by_email($email,$is_checksum=false) {
        $this->db->select("user.id, user.superuser, user.password_checksum, ip_address, last_access_date")
            ->from("user")
            ->join("access_token","access_token.user_id = user.id","left");
        if ($is_checksum) {
            $this->db->where("md5(user.id)",$email);
        } else {
            $this->db->where("user.id",strtolower($email));
        }
        $this->db->order_by("last_access_date","DESC");
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row_array();
            return array("id"=>$row["id"],"superuser"=>$row["superuser"],"password_checksum"=>$row["password_checksum"],"ip_address"=>$row["ip_address"],"last_access_date"=>$row["last_access_date"]);
        }
        return null;
    }

    public function issue_access_token($user_id,$user_agent,$ip_address) {
        $query = $this->db->get_where("access_token",array("user_id"=>$user_id,"user_agent"=>$user_agent,"ip_address"=>$ip_address));
        $token = md5(uniqid());
        if ($query->num_rows() == 0) {
            $this->db->set("token",$token);
            $this->db->set("user_id",$user_id);
            $this->db->set("user_agent",$user_agent);
            $this->db->set("ip_address",$ip_address);
            $this->db->set("issue_date",'NOW()',false);
            $this->db->set("last_access_date",'NOW()',false);
            $this->db->insert("access_token");
        } else {
            $row = $query->row_array();
            $old_token = $row["token"];
            $this->db->set("issue_date", 'NOW()',false);
            $this->db->set("last_access_date", 'NOW()', false);
            $this->db->set("token",$token);
            $this->db->where("token",$old_token);
            $this->db->update("access_token");
        }
        return $token;
    }

    public function get_user_by_token($token,$user_agent,$ip_address) {
        $this->db->select("user.id, user.superuser, user.password_checksum")
            ->from("user")
            ->join("access_token","access_token.user_id = user.id")
            ->where("token",$token)
            ->where("user_agent",substr($user_agent,0,64))
            ->where("ip_address",$ip_address)
            ->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $user = array("id"=>$row["id"],"superuser"=>$row["superuser"],"password_checksum"=>$row["password_checksum"]);
            $this->db->set("last_access_date","NOW()",false)
                ->where("token",$token);
            $this->db->update("access_token");
            return $user;
        }
        return false;
    }

    public function get_all_users() {
        $this->db->select("user.id,superuser,ip_address,last_access_date")
            ->from("user")
            ->join("access_token","access_token.user_id = user.id","left")
            ->order_by("superuser","desc")
            ->order_by("id")
            ->order_by("last_access_date","desc");
        $query = $this->db->get();
        $users = array();
        foreach ($query->result_array() as $row) {
            if (!isset($users[$row["id"]])) {
                $users[$row["id"]] = array(
                    "id"=>$row["id"],
                    "superuser"=>$row["superuser"],
                    "last_access_date"=>$row["last_access_date"],
                    "ip_address"=>$row["ip_address"]
                );
            }
        }

        $users = array_values($users);
        return $users;
    }

    public function logout($token) {
        $this->db->from("access_token")
            ->where("token",$token);
        $this->db->delete();
    }

    public function reset_access($user_id,$id) {
        $this->db->set("password_checksum","")
            ->where("id",$id);
        $this->db->update('user');
        $this->log($user_id);
        $this->db->from("access_token")
            ->where("user_id",$id);
        $this->db->delete();
        $this->log($user_id);
    }

    public function reset_password($id,$password_checksum) {
        $this->db->set("password_checksum",$password_checksum)
            ->where("id",$id);
        $this->db->update("user");
        $this->db->from("access_token")
            ->where("user_id",$id);
        $this->db->delete();
    }

    public function delete_access_token($token) {
        $this->db->where("token",$token);
        $this->db->delete("access_token");
    }

    public function edit_user($logged_in_user_id,$user_id,$superuser) {
        $this->db->set("superuser",$superuser)
            ->where("id",$user_id);
        $this->db->update("user");
        $this->log($logged_in_user_id);
    }

    public function add_user($logged_in_user_id,$user_id,$superuser) {
        $this->db->from("user");
        $this->db->where("id",$user_id);
        if ($this->db->count_all_results() > 0) {
            return false;
        }
        $this->db->set("id",$user_id);
        $this->db->set("superuser",$superuser);
        if ($this->db->insert("user") !== false) {
            $this->log($logged_in_user_id);
            return true;
        }
        return false;
    }

    public function delete_account($logged_in_user_id,$user_id) {
        if ($logged_in_user_id != $user_id) {
            $this->db->from("user")
                ->where("id",$user_id)
                ->where("superuser",1);
            if ($this->db->count_all_results() > 0) {
                return false;
            }
        }
        $this->db->from("user")
            ->where("id",$user_id);
        if ($this->db->delete("user") !== false) {
            $this->log($logged_in_user_id);
            return true;
        }
        return false;
    }
}