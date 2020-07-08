<?php
$this->load->helper("form");
echo form_open("/admin/login");
echo '<p>'.form_label("Email","email").'<br /><input type="email" name="email" /></p>';
echo '<p>'.form_label('Password','password').'<br />'.form_password("password").'</p>';
echo '<p>'.form_submit('','Log in').'</p>';
echo form_close();