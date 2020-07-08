<?php
function format_inches($num) {
    $number = floor($num);
    $fraction = (round($num*4)/4) - $number;
    switch ($fraction) {
        case 0.25:
            return $number." &frac14;";
        case 0.5:
            return $number." &frac12;";
        case 0.75:
            return $number." &frac34;";
        default:
            return $number;
    }
}
echo '<h2>Works by '.$artist["name"].' viewed by '.$name.'</h2>';
if (empty($images)) {
    echo '<p>No works viewed</p>';
} else {
    echo '<ul class="thumbnails">';
    foreach ($images as $image) {
        $info = array();
        $titleSpan = null;
        if (!empty($image["title"])) {
            $info[] = '<span class="title">'.htmlspecialchars(join("/",array_unique(array_values($image["title"])))).'</span>';
        }
        if (!empty($image["year"])) {
            $info[] = '<span class="year">'.htmlspecialchars($image["year"]).'</span>';
        }
        $dimensions = array();
        if (!empty($image["height"])) {
            $dimensions[] = format_inches($image["height"]).'&quot;';
        }
        if (!empty($image["width"])) {
            $dimensions[] = format_inches($image["width"]).'&quot;';
        }
        if (!empty($image["depth"])) {
            $dimensions[] = format_inches($image["depth"]).'&quot;';
        }
        if (!empty($dimensions)) {
            $info[] = '<span class="dimensions">'.join(" &times; ",$dimensions).'</span>';
        }
        if (!empty($image["description"])) {
            $info[] = '<span class="description">'.htmlspecialchars(join("/",array_unique(array_values($image["description"])))).'</span>';
        }
        $info[] = $image["view_count"]." ".($image["view_count"] > 1 ? "views" : "view");
        echo '<li class="thumbnail"><img src="/images/195/'.$image["id"].($image["version"] ? "-".$image["version"] : "").'.jpg" /><div>'.join('</div><div>',$info).'</div></li>';
    }
    echo '</ul>';
}