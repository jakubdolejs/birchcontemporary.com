<?php
if (!empty($exhibitions)) {
    echo '<table class="listing"><tbody>';
    foreach ($exhibitions as $exhibition) {
        if ($user["superuser"] || in_array($exhibition["gallery_id"],$user["galleries"])) {
            echo '<tr><td><a href="/admin/exhibition/'.$exhibition["id"].'">'.$exhibition["title"].'</a></td><td><a class="button" href="/admin/exhibition/'.$exhibition["id"].'/images">images</a></td><td><a class="deleteLink button" href="/admin/exhibition/'.$exhibition["id"].'/delete">delete</a></td></tr>';
        }
    }
    echo '</tbody></table>';
}
if (!empty($years)) {
    $links = array('<a href="/admin/exhibitions">Recent</a>');
    foreach ($years as $year) {
        $links[] = '<a href="/admin/exhibitions/'.$year.'">'.$year.'</a>';
    }
    echo '<p>'.join(" | ",$links).'</p>';
}
?>
<script type="text/javascript">
    $("a.deleteLink").on("click",function(){
        return confirm("Are you sure you want to delete "+$(this).parents("tr").first().find("td").first().text()+"?");
    });
</script>