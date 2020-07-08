<?php
$this->load->helper("form");
if (!empty($user) && $logged_in_user["id"] == $user["id"]) {
    echo '<h1>My Account</h1>';
}
if ($logged_in_user["superuser"]) {
    echo form_open(empty($user) ? "/admin/user/add" : "/admin/user/edit",array("method"=>"post"));
    echo '<p>'.form_label("Email","id").'<br /><input name="id" type="email" value="'.(!empty($user) ? $user["id"] : "").'" /></p>';
    $extra = "";
    if (!empty($user) && $user["superuser"] && $user["id"] != $logged_in_user["id"]) {
        $extra = 'disabled="disabled"';
    }
    echo '<p>'.form_checkbox("superuser","1",!empty($user) && $user["superuser"],$extra).' '.form_label("Administrator privileges","superuser").'</p>';
    echo '<p>'.form_submit("save","Save").'</p>';
    echo form_close();
    if (!empty($user)) {
        echo form_open("/admin/user/password",array("method"=>"post"));
        echo '<input type="hidden" name="id" value="'.$user["id"].'" />';
        echo '<p><a href="javascript:void(0)" class="submit">Reset password</a></p>';
        echo form_close();
    }
} else {
    echo '<p>User name: '.$user["id"].'</p>';
    if (!empty($user) && $user["id"] == $logged_in_user["id"]) {
        $this->load->view("admin/set_password",array('user'=>$user));
    }
}
?>
