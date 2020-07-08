<?php
if (!empty($artists)) {
    usort($artists,function($a,$b){
        if ($a["surname"] == $b["surname"]) {
            if ($a["name"] == $b["name"]) {
                return 0;
            }
            return $a["name"] > $b["name"] ? 1 : -1;
        }
        return $a["surname"] > $b["surname"] ? 1 : -1;
    });
    echo '<table class="listing"><tbody>';
    foreach ($artists as $artist) {
        echo '<tr><td><a href="/admin/artist/'.$artist["id"].'">'.trim($artist["name"].' '.$artist["surname"]).'</a></td><td>';
        if ($artist["image_count"] > 0) {
            echo '<a class="button" href="/admin/artist/'.$artist["id"].'/images">images</a>';
        }
        echo '</td><td><a class="deleteLink button" href="/admin/artist/'.$artist["id"].'/delete">delete</a></td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}
?>
<script type="text/javascript">
    $("a.deleteLink").on("click",function(){
        return confirm("Are you sure you want to delete "+$(this).parents("tr").first().find("td").first().text()+"?");
    });
</script>