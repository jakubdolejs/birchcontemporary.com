<?php
if (!empty($news)) {
    echo '<table class="listing"><tbody>';
    foreach ($news as $story) {
        echo '<tr><td><a href="/admin/news/'.$story["id"].'">'.substr(strip_tags($story["html"]),0,100).'</a></td><td><a class="deleteLink button" href="/admin/news/'.$story["id"].'/delete">delete</a></td></tr>';
    }
    echo '</tbody></table>';
}
/*
if (!empty($years)) {
    $links = array('<a href="/admin/news">Recent</a>');
    foreach ($years as $year) {
        $links[] = '<a href="/admin/news/year/'.$year.'">'.$year.'</a>';
    }
    echo '<p>'.join(" | ",$links).'</p>';
}
*/
?>
<script type="text/javascript">
    $("a.deleteLink").on("click",function(){
        return confirm("Are you sure you want to delete "+$(this).parents("tr").first().find("td").first().text()+"?");
    });
</script>