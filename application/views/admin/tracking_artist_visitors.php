<?php
if (!empty($visitors)) {
    echo '<h2>'.$artist["name"].'</h2><table class="trackingListing">
    <thead>
    <tr><td>Name</td><td>Email</td><td>Works viewed</td><td>Date of last visit</td></tr>
    </thead><tbody>';
    foreach ($visitors as $visitor) {
        $query = http_build_query(array("visitor"=>$visitor["email"],"name"=>$visitor["name"],"month"=>$month),null,"&amp;");
        $link = '/admin/tracking/visit/'.$artist["id"].'?'.$query;
        echo '<tr>
        <td><a href="/admin/tracking/visitor?'.$query.'">'.htmlspecialchars($visitor["name"]).'</a></td>
        <td><a href="mailto:'.$visitor["email"].'">'.htmlspecialchars($visitor["email"]).'</a></td>
        <td><a href="'.$link.'">'.$visitor["work_count"].'</a></td>
        <td>'.$visitor["time"].'</td>
        </tr>';
    }
    echo '</tbody></table>';
}