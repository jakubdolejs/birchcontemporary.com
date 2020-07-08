<?php
if (!empty($exhibition)) {
    if (!empty($exhibition["title"])) {
        $title = htmlspecialchars($exhibition["title"]);
    } else if (!empty($exhibition["artists"])) {
        $title = join(", ",$exhibition["artists"]);
        $exhibition["artists"] = array();
    }
    $artists = "";
    if (!empty($exhibition["artists"])) {
        if (count($exhibition["artists"]) > 1) {
            $artists = array();
            foreach ($exhibition["artists"] as $artist_id=>$artist_name) {
                $artists[] = '<a href="/artist/'.$artist_id.'" itemprop="performer" itemscope itemtype="http://schema.org/Person"><span itemprop="name">'.htmlspecialchars($artist_name).'</span></a>';
            }
            $artists = '<p>'.join(", ",$artists).'</p>';
        } else {
            $title = htmlspecialchars(current($exhibition["artists"])).": ".$title;
        }
    }
    echo '<div itemscope itemtype="http://schema.org/VisualArtsEvent">';
    echo '<h1 itemprop="name">'.$title.'</h1>';
    if (!empty($images)) {
        $class = " small";
        $src = "195";
    } else {
        $class = " featured";
        $src = "410";
    }
    echo '<div class="exhibition'.$class.'" itemref="gallery-address">';
    if (!empty($exhibition["image_id"])) {
        echo '<div class="exhibition-image"><img itemprop="image" src="http://'.$this->input->server("HTTP_HOST").'/images/'.$src.'/'.$exhibition["image_id"].'.jpg" alt="'.$exhibition["id"].'" /></div>';
    }
    echo '<div class="exhibition-info">'.$artists;
    $start = DateTime::createFromFormat("Y-m-d", $exhibition["start_date"]);
    $end = DateTime::createFromFormat("Y-m-d", $exhibition["end_date"]);
    $this->load->helper("date_formatter");
    $dates = format_exhibition_dates($start,$end);
    echo '<p>'.$dates.'</p>';
    if ($exhibition["reception_start"] && $exhibition["reception_end"]) {
        $end = DateTime::createFromFormat("Y-m-d H:i:s", $exhibition["reception_end"]);
        if ($end->getTimestamp() >= time()) {
            $start = DateTime::createFromFormat("Y-m-d H:i:s", $exhibition["reception_start"]);
            $reception = format_opening_reception_dates($start,$end);
            echo '<p itemprop="subEvent" itemscope itemtype="http://schema.org/VisualArtsEvent"><span itemprop="name">Opening reception</span> '.$reception.'</p>';
        }
    }
    echo '</div></div>';
    if (!empty($images)) {
        $base_image_url = !empty($artist_id) ? "/artist/".@$artist_id."/exhibition/".$exhibition["id"]."/image/" : "/exhibition/".$exhibition["id"]."/image/";
        echo '<h3>Works in the exhibition</h3><ul class="thumbnails">';
        foreach ($images as $image) {
            echo '<li>
        <div><a href="'.$base_image_url.$image["id"].'"><img class="thumbnail" src="/images/195/'.$image["id"].'.jpg" alt="image" /></a></div>';
            $artists = array();
            if (!empty($image["title"])) {
                echo '<p class="title">'.htmlspecialchars($image["title"]).'</p>';
            }
            $exhibition_artist_keys = "";
            if (!empty($exhibition["artists"])) {
                $exhibition_artist_keys = array_keys($exhibition["artists"]);
                sort($exhibition_artist_keys);
                $exhibition_artist_keys = join(",",$exhibition_artist_keys);
            }
            if (!empty($image["artists"])) {
                $image_artists = array_keys($image["artists"]);
                sort($image_artists);
                if (join(",",$image_artists) != $exhibition_artist_keys) {
                    $image_artists = array();
                    foreach ($image["artists"] as $id=>$artist) {
                        $image_artists[] = '<a href="/artist/'.$id.'">'.htmlspecialchars($artist).'</a>';
                    }
                    echo "<p>".join(", ",$image_artists).'</p>';
                }
            }
            echo '</li>';
        }
        echo '</ul>';
    }
    if (!empty($exhibition["text"])) {
        echo '<div>'.$exhibition["text"].'</div>';
    }
    echo '</div>';
}