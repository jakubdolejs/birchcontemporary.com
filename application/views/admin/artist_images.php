<?php
echo '<h1>'.$artist_name.'</h1><p>Drag and drop to reorder images. Click to toggle between featured and archived.</p>';
echo '<div id="featured"><h3>Featured</h3>';
$this->load->view("admin/image_list",array("images"=>$featured_images));
echo '</div>';
echo '<div id="archived"><h3>Archive</h3>';
$this->load->view("admin/image_list",array("images"=>$archived_images));
echo '</div>';
?>
<p><button id="saveButton" style="display:none">Save</button></p>
<script type="text/javascript" src="/js/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript">
    function onUpdate() {
        $("#saveButton").show().off("click").on("click",save);
    }
    function save() {
        $("#saving").remove();
        var saving = $('<p id="saving">Updating. Please wait.</p>').insertBefore($("#saveButton"));
        $("#saveButton").off("click").hide();
        var data = {"featured":[],"archived":[]};
        $("#featured ul.thumbnails a.thumbnail").each(function(){
            data.featured.push($(this).attr("data-id"));
        });
        $("#archived ul.thumbnails a.thumbnail").each(function(){
            data.archived.push($(this).attr("data-id"));
        })
        function onError() {
            alert("Error saving images.");
            location.reload();
        }
        $.ajax({
            "url":"/api/artist/<?php echo $artist_id; ?>/images",
            "type":"post",
            "dataType":"json",
            "success":function(data){
                if (!data) {
                    onError();
                    return;
                }
                saving.remove();
            },
            "error":onError,
            "data":data
        });
    }
    $(document).on("ready",function(){
        $("#featured ul.thumbnails, #archived ul.thumbnails").sortable();
        $("#featured ul.thumbnails, #archived ul.thumbnails").disableSelection();
        $("#featured ul.thumbnails, #archived ul.thumbnails").on("sortstop",onUpdate);
        $("#featured ul.thumbnails, #archived ul.thumbnails").on("click","li",function(event){
            event.stopPropagation();
            var newParent = $("#featured ul.thumbnails");
            if ($(this).parents("#archived").length == 0) {
                newParent = $("#archived ul.thumbnails");
            }
            $(this).appendTo(newParent);
            onUpdate();
            return false;
        });
    });
</script>