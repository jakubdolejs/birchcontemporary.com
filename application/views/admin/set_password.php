<?php
$this->load->helper("form");
echo form_open("/admin/user/password",array("method"=>"post"));
echo form_hidden("id",$user["id"]);
if ($user["password_checksum"]) {
    echo '<h1>Change your password</h1>';
    echo '<p>'.form_label("Old password","old_password").'<br />'.form_password("old_password").'</p>';
} else {
    echo '<h1>Set your password</h1>';
}
echo '<p>'.form_label("New password","password1").'<br />'.form_password("password1").'</p>';
echo '<p>'.form_label("Re-type new password","password2").'<br />'.form_password("password2").'</p>';
echo '<p>'.form_submit("save","Save").'</p>';
echo form_close();