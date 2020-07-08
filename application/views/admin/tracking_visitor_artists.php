<?php
echo '<h2>'.$name.'</h2>';
if (!empty($artists)) {
    echo '<table class="trackingListing">
    <thead>
    <tr><td>Name</td><td>Works viewed</td></tr>
    </thead><tbody>';
    foreach ($artists as $artist) {
        $query = http_build_query(array("month"=>$month,"name"=>$name,"visitor"=>$email),null,"&amp;");
        $link = '/admin/tracking/visit/'.$artist["id"].'?'.$query;
        echo '<tr>
        <td><a href="/admin/tracking/artist/'.$artist["id"].'?month='.$month.'">'.htmlspecialchars($artist["name"]).'</a></td>
        <td><a href="'.$link.'">'.$artist["work_count"].'</a></td>
        </tr>';
    }
    echo '</tbody></table>';
} else {
    echo '<p>No artists viewed by '.$name.' in '.$month.'</p>';
}