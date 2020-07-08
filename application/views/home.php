<?php
$class = "left";
echo '<div class="large-column '.$class.'">';
$class = $class == "right" ? "left" : "right";
$first_exhibition = current($exhibitions);
if ($first_exhibition["image_id"]) {
    $exhibition_images = array();
    foreach ($exhibitions as $exh) {
        if ($exh["image_id"]) {
            $exhibition_images[] = $exh["image_id"];
        }
    }
    $exhibition_images = array_unique($exhibition_images);
    echo '<a class="feature" data-image_ids="['.join(",",$exhibition_images).']" href="/exhibitions"><img class="feature" src="/images/410/'.$first_exhibition["image_id"].'.jpg" alt="image" /></a>';
}
foreach ($exhibitions as $exhibition) {
    $this->load->view("exhibition_listing",array("exhibition"=>$exhibition));
}
echo '</div>';
?>