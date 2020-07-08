<?php
$this->load->helper("date_formatter");
echo '<div class="exhibitions">';
if (!empty($current)) {
    echo '<h1>Current Exhibition';
    if (count($current) > 1) {
        echo 's';
    }
    echo '</h1>';
    foreach ($current as $exhibition) {
        $this->load->view("exhibition_listing",array("exhibition"=>$exhibition,"image_size"=>"410","classes"=>array("featured")));
    }
}
if (!empty($upcoming)) {
    echo '<h1>Upcoming Exhibition';
    if (count($upcoming) > 1) {
        echo 's';
    }
    echo '</h1>';
    foreach ($upcoming as $exhibition) {
        $this->load->view("exhibition_listing",array("exhibition"=>$exhibition,"image_size"=>"195","classes"=>array("small")));
    }
}
if (!empty($past)) {
    echo '<p><a href="/past_exhibitions">Past Exhibitions</a></p>';
}
echo '</div>';