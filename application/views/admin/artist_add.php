<?php
$this->load->helper("form");
echo form_open("/admin/artists",array("method"=>"post"));
echo form_fieldset("Add an artist");
echo '<p>'.form_input("name",'','placeholder="name"').' '.form_input("surname",'','placeholder="surname"').' '.form_submit("add","Add").'</p>';
echo form_fieldset_close();
echo form_close();