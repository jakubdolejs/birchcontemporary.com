<?php
$this->load->helper("form");
if (!empty($staff)) {
    $action = "/admin/staff/".$staff["id"];
} else {
    $action = "/admin/staff";
}
echo form_open($action,array("method"=>"post"));
echo '<p>'.form_label("Name","name").'<br />'.form_input("name",!empty($staff) ? $staff["name"] : '').'</p>';
echo '<p>'.form_label("Email","email").'<br /><input type="email" name="email" value="'.(!empty($staff) ? $staff["email"] : '').'" /></p>';
if (!$user["superuser"] && count($user["galleries"]) == 1) {
    echo form_hidden("gallery_id",$user["galleries"][0]);
} else {
    $options = array();
    foreach ($galleries as $gallery) {
        $options[$gallery["id"]] = $gallery["city"];
    }
    $selected = array();
    if (!empty($staff)) {
        $selected[] = $staff["gallery"]["id"];
    }
    echo '<p>'.form_label("Gallery","gallery_id").'<br />'.form_dropdown("gallery_id",$options,$selected).'</p>';
}
echo '<p>'.form_label("Title in French","title[fr]").'<br />'.form_input("title[fr]",!empty($staff["title"]["fr"]) ? $staff["title"]["fr"] : '').'</p>';
echo '<p>'.form_label("Title in English","title[en]").'<br />'.form_input("title[en]",!empty($staff["title"]["en"]) ? $staff["title"]["en"] : '').'</p>';
echo '<p>'.form_submit("save","Save").'</p>';
echo form_close();