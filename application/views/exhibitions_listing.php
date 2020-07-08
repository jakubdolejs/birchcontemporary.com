<?php
if (!empty($exhibitions)) {
    echo '<ul class="exhibition-listing">';
    $this->load->helper("date_formatter");
    foreach ($exhibitions as $exhibition) {
        $title = $exhibition["title"];
        $artists = "";
        if (!empty($exhibition["artists"])) {
            if (count($exhibition["artists"]) > 1) {
                $artists = array();
                foreach ($exhibition["artists"] as $artist_id=>$artist_name) {
                    $artists[] = '<a href="/artist/'.$artist_id.'">'.$artist_name.'</a>';
                }
                $artists = '<p>'.join(", ",$artists).'</p>';
            } else {
                $title = '<span class="artist">'.current($exhibition["artists"])."</span> ".$title;
            }
        }
        echo '<li><h2><a href="/exhibition/'.$exhibition["id"].'">'.$title.'</a></h2>'.$artists;
        $start = DateTime::createFromFormat("Y-m-d", $exhibition["start_date"]);
        $end = DateTime::createFromFormat("Y-m-d", $exhibition["end_date"]);
        $dates = format_exhibition_dates($start,$end);
        echo '<p>'.$dates.'</p></li>';
    }
    echo '</ul>';
}