<?php
if (!empty($exhibition)) {
    $class = "exhibition";
    if (!empty($classes)) {
        $class .= " ".join(" ",$classes);
    }
    echo '<div class="'.$class.'" itemscope itemtype="http://schema.org/VisualArtsEvent" itemref="gallery-address">';
    if (!empty($exhibition["image_id"]) && !empty($image_size)) {
        echo '<div class="exhibition-image"><a href="/exhibition/'.$exhibition["id"].'"><img src="http://'.$this->input->server("HTTP_HOST").'/images/'.$image_size.'/'.$exhibition["image_id"].'.jpg" itemprop="image" alt="'.$exhibition["id"].'" /></a></div>';
    }
    $title = '<a href="/exhibition/'.$exhibition["id"].'" data-exhibition_id="'.$exhibition["id"].'" class="exhibition-link">'.htmlspecialchars($exhibition["title"]).'</a>';
    $artists = "";
    if (!empty($exhibition["artists"])) {
        if (count($exhibition["artists"]) > 1) {
            $artists = array();
            foreach ($exhibition["artists"] as $artist_id=>$artist_name) {
                $artists[] = '<a href="/artist/'.$artist_id.'" itemprop="performer" itemscope itemtype="http://schema.org/Person"><span itemprop="name">'.htmlspecialchars($artist_name).'</span></a>';
            }
            $artists = '<p>'.join(", ",$artists).'</p>';
        } else {
            $title = '<a class="artist exhibition-link" data-exhibition_id="'.$exhibition["id"].'" href="/exhibition/'.$exhibition["id"].'">'.htmlspecialchars(current($exhibition["artists"]))."</a>: ".$title;
        }
    }
    echo '<div class="exhibition-info"><h2 itemprop="name">'.$title.'</h2>'.$artists;
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
}
?>