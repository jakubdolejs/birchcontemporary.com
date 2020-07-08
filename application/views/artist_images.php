<?php
if (!empty($artist)) {
    $echoImages = function($images,$class=null) use($artist) {
        $class = $class ? "thumbnails ".$class : "thumbnails";
        echo '<ul class="'.$class.'">';
        foreach ($images as $image) {
            echo '<li><div><a href="/artist/'.$artist["id"].'/image/'.$image["id"].'"><img class="thumbnail" src="/images/195/'.$image["id"].($image["version"] ? "-".$image["version"] : "").'.jpg" alt="image" /></a></div>';
            $artists = array();
            if (!empty($image["title"])) {
                echo '<p class="title">'.htmlspecialchars($image["title"]).'</p>';
            }
            if (count($image["artists"]) > 1) {
                foreach ($image["artists"] as $id=>$image_artist) {
                    if ($id != $artist["id"]) {
                        $artists[$id] = '<a href="/artist/'.$id.'">'.htmlspecialchars($image_artist).'</a>';
                    }
                }
                if (!empty($artists)) {
                    echo "<p>".htmlspecialchars(trim($artist["name"].' '.$artist["surname"]))." with ".join(", ",$artists).'</p>';
                }
            }
            echo '</li>';
        }
        echo '</ul>';
    };
    if (!empty($featured)) {
        $echoImages($featured);
        if (!empty($archived)) {
            if ($show_archived) {
                echo '<h3 id="archivedTitle">Archived work</h3>';
                $echoImages($archived);
            } else {
                echo '<p><a href="/artist/'.$artist["id"].'/archived" id="archivedLink">More</a></p>';
                $echoImages($archived,"hidden archived");
            }
        }
    } else if (!empty($archived)) {
        $echoImages($archived);
    }
}
if (!empty($archived)) {
?>
    <script type="text/javascript">
        //<![CDATA[
        if (history.pushState) {
            window.onpopstate = function(event) {
                if (location.href.indexOf("/archived") > -1) {
                    showArchived();
                } else {
                    $("ul.thumbnails.archived").addClass("hidden");
                    $("#archivedTitle").replaceWith('<p><a href="/artist/<?php echo $artist["id"]; ?>/archived" id="archivedLink">More</a></p>');
                    $('#archivedLink').on("click",onViewArchived);
                }
            }
            function showArchived() {
                $("ul.thumbnails.archived").removeClass("hidden");
                $('#archivedLink').parent().replaceWith('<h3 id="archivedTitle">Archived work</h3>');
            }
            function onViewArchived(event) {
                event.preventDefault();
                history.pushState("archived",null,$(this).attr("href"));
                showArchived();
                return false;
            }
            $('#archivedLink').on("click",onViewArchived);
        }
        //]]>
    </script>
<?php
}
?>