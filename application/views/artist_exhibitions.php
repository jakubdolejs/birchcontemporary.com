<?php
if (!empty($artist) && !empty($exhibitions)) {
    foreach ($exhibitions as $exhibition) {
        echo '<div class="exhibition">';
        if (!empty($exhibition["image_id"])) {
            echo '<p><a href="/artist/'.$artist["id"].'/exhibition/'.$exhibition["id"].'"><img src="/images/410/'.$exhibition["image_id"].'.jpg" alt="'.$exhibition["id"].'" class="exhibition-feature" /></a></p>';
        }
        if (!empty($exhibition["title"])) {
            $title = $exhibition["title"];
        } else {
            $title = join(", ",$exhibition["artists"]);
        }
        $title = '<a href="/artist/'.$artist["id"].'/exhibition/'.$exhibition["id"].'" data-exhibition_id="'.$exhibition["id"].'" class="exhibition-link">'.htmlspecialchars($title).'</a>';
        $artists = "";
        if (!empty($exhibition["artists"])) {
            if (count($exhibition["artists"]) > 1) {
                $artists = array();
                foreach ($exhibition["artists"] as $artist_id=>$artist_name) {
                    $artists[] = '<a href="/artist/'.$artist_id.'">'.htmlspecialchars($artist_name).'</a>';
                }
                $artists = '<p>'.join(", ",$artists).'</p>';
            }
        }
        echo '<h2>'.$title.'</h2>'.$artists;
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
                echo '<p>Opening reception '.$reception.'</p>';
            }
        }
        echo '</div>';
    }
}