<?php
if (!empty($staff)) {
    echo '<table class="listing"><tbody>';
    foreach ($staff as $person) {
        $title = join("/",array_unique($person["title"]));
        echo '<tr data-id="'.$person["id"].'" data-gallery_id="'.$person["gallery"]["id"].'"><td><a href="/admin/staff/'.$person["id"].'">'.$person["name"].' – '.$title.' ('.$person["gallery"]["city"].')</a></td>';
        echo '<td><a class="deleteLink button" href="/admin/staff/'.$person["id"].'/delete">delete</a></td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}
?>
<p><button id="saveOrder" disabled="disabled">Save order</button></p>
<script type="text/javascript" src="/js/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript">
    $("a.deleteLink").on("click",function(){
        return confirm("Are you sure you want to delete "+$(this).parents("tr").first().find("td").first().text()+"?");
    });
    $(document).on("ready",function(){
        function onSort() {
            $("#saveOrder").removeAttr("disabled");
        }
        $("table.listing tbody").sortable();
        $("table.listing tbody").disableSelection();
        $("table.listing tbody").on("sortstop",onSort);

        $("#saveOrder").on("click",function(){
            $(this).attr("disabled","disabled");
            var priority = {};
            $("table.listing tbody tr").each(function(){
                if (!priority.hasOwnProperty($(this).attr("data-gallery_id"))) {
                    priority[$(this).attr("data-gallery_id")] = [];
                }
                priority[$(this).attr("data-gallery_id")].push($(this).attr("data-id"));
            });
            $.ajax({
                "url":"/api/staff/order",
                "dataType":"json",
                "data":{"priority":priority},
                "type":"post",
                "error":function(){
                    alert("Error saving order.");
                    location.reload();
                }
            });
            return false;
        });
    });
</script>