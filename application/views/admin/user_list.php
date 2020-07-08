<?php
if (!empty($users)) {
    echo '<table class="listing"><tbody>';
    foreach ($users as $usr) {
        echo '<tr><td><form action="/admin/user/edit" method="post"><input type="hidden" name="id" value="'.$usr["id"].'" /><a href="javascript:void(0);" class="editLink">'.$usr["id"].'</a></form></td>';
        if (!$usr["superuser"]) {
            echo '<td><form action="/admin/user/delete" method="post"><input type="hidden" name="id" value="'.$usr["id"].'" /><a class="button deleteLink" href="javascript:void(0)">delete</a></form></td>';
        } else {
            echo '<td></td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table>';
}
?>
<script type="text/javascript">
    $("a.deleteLink").on("click",function(){
        if (confirm("Are you sure you want to delete "+$(this).parents("tr").first().find("td").first().text()+"? This cannot be undone.")) {
            $(this).parents("form").get(0).submit();
        }
    });
    $("a.editLink").on("click",function(){
        $(this).parents("form").get(0).submit();
    });
</script>