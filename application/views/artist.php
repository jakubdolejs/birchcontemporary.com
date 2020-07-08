<?php
if (!empty($artists)) {
    $nonrepresented_header_echoed = FALSE;
    echo '<h1>Gallery Artists</h1><ul class="thumbnails">';
    foreach ($artists as $artist) {
        if (!$artist["represented"] && !$nonrepresented_header_echoed) {
            $nonrepresented_header_echoed = TRUE;
            echo '</ul><h1>Works available by</h1><ul class="thumbnails">';
        }
        echo '<li itemscope itemtype="http://schema.org/Person">';
        if (!empty($artist["image_id"])) {
            echo '<div><a href="http://'.$this->input->server("HTTP_HOST").'/artist/'.$artist["id"].'" itemprop="url"><img itemprop="image" class="thumbnail" src="http://'.$this->input->server("HTTP_HOST").'/images/195/'.$artist["image_id"].'.jpg" alt="image" /></a></div>';
        }
        echo '<a class="artist" href="http://'.$this->input->server("HTTP_HOST").'/artist/'.$artist["id"].'" itemprop="name">'.htmlspecialchars(trim($artist["name"].' '.$artist["surname"])).'</a></li>';
    }
    echo '</ul>';
}