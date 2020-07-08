<?php
if (!empty($links)) {
    $anchors = array();
    foreach ($links as $link) {
        if (empty($link["selected"])) {
            $anchors[] = '<a href="'.$link["url"].'">'.htmlspecialchars($link["label"]).'</a>';
        } else {
            $anchors[] = '<a href="'.$link["url"].'" class="selected">'.htmlspecialchars($link["label"]).'</a>';
        }
    }
    if (count($anchors) > 1) {
        echo '<p class="linkGroup">'.join(" | ",$anchors).'</p>';
    }
}