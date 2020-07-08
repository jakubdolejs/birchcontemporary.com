<?php
echo '<ul class="thumbnails">';
if (!empty($images)) {
    foreach ($images as $image) {
        echo '<li><a href="/admin/image/'.$image["id"].'" class="thumbnail" data-id="'.$image["id"].'"><img src="/images/195/'.$image["id"].($image["version"] ? "-".$image["version"] : "").'.jpg" alt="" /></a>';
        if (!empty($image["artists"])) {
            echo '<p>'.htmlspecialchars(join(", ",array_values($image["artists"]))).'</p>';
        }
        echo '</li>';
    }
}
echo '</ul>';