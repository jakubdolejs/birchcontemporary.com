<?php
echo '<h1>'.$exhibition_name.'</h1>';
echo '<div id="exhibition_images"><h3>Exhibition images</h3>';
$this->load->view("admin/image_list",array("images"=>$exhibition_images));
echo '</div>';
echo '<div id="all_images"><h3>All available images</h3>';
$this->load->view("admin/image_list",array("images"=>$all_images));
echo '</div>';
?>
<p><button id="saveButton" style="display:none">Save</button></p>
<script type="text/javascript" src="/js/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript">
    function onUpdate() {
        $("#saveButton").show().on("click",save);
    }
    function save() {
        $("#saveButton").off("click").hide();
        var data = {"images":[]};
        $("#exhibition_images ul.thumbnails a.thumbnail").each(function(){
            data.images.push($(this).attr("data-id"));
        });
        function onError() {
            alert("Error saving images.");
            location.reload();
        }
        $.ajax({
            "url":"/api/exhibition/<?php echo $exhibition_id; ?>/images",
            "type":"post",
            "dataType":"json",
            "success":function(data){
                if (!data) {
                    onError();
                }
            },
            "error":onError,
            "data":data
        });
    }
    $(document).on("ready",function(){
        $("#exhibition_images ul.thumbnails").sortable();
        $("#exhibition_images ul.thumbnails").disableSelection();
        $("#exhibition_images ul.thumbnails").on("sortstop",onUpdate);
    });
    function onRemoveImage() {
        $(this).parent().remove();
        onUpdate();
        return false;
    }
    $("#exhibition_images ul.thumbnails a.thumbnail").on("click",onRemoveImage);
    $("#all_images ul.thumbnails a.thumbnail").on("click",function(){
        var imageId = $(this).attr("data-id");
        var exhibitionList = $("#exhibition_images ul.thumbnails");
        if (exhibitionList.length == 0) {
            exhibitionList = $('<ul class="thumbnails"></ul>').appendTo("#exhibition_images");
            exhibitionList.sortable();
            exhibitionList.disableSelection();
            exhibitionList.on("sortstop",onUpdate);
        }
        if (exhibitionList.find("a.thumbnail[data-id='"+imageId+"']").length == 0) {
            var clone = $(this).clone().on("click",onRemoveImage);
            exhibitionList.prepend($('<li></li>').append(clone));
            onUpdate();
        }
        return false;
    });
</script>