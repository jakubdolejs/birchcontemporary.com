<?php
echo '<ul class="news">';
if (!empty($news)) {
    foreach ($news as $story) {
        echo '<li class="story">';
        if (!empty($story["html"])) {
            echo '<div class="text" data-id="'.$story["id"].'"><div>'.$story["html"].'</div></div>';
        }
        $links = array();
        if (!empty($story["exhibitions"])) {
            foreach ($story["exhibitions"] as $id=>$exhibition) {
                $links[] = '<a href="/exhibition/'.$id.'">'.htmlspecialchars($exhibition).'</a>';
            }
        }
        if (!empty($story["artists"])) {
            foreach ($story["artists"] as $id=>$news_artist) {
                if (empty($artist_id) || $artist_id != $id) {
                    $links[] = '<a href="/artist/'.$id.'">'.htmlspecialchars($news_artist).'</a>';
                }
            }
        }
        if (!empty($links)) {
            echo '<p class="links">Links: '.join(', ',$links).'</p>';
        }
        echo '</li>';
    }
} else {
    echo '<li>No news is good news</li>';
}
echo '</ul>';
?>
<script type="text/javascript">
    (function(){
        $(window).on("load",function(){
            var stories = $("ul.news li.story div.text div");
            if (stories.length > 0) {
                stories.each(function(){
                    if (this.scrollHeight > $(this).height()) {
                        $(this).parent().append('<a class="more" href="/news/'+$(this).data("id")+'">&hellip;</a>');
                    }
                });
            }
        });
    })();
</script>