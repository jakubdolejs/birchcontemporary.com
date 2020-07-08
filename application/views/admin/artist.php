<?php
if (!empty($artist)) {
    function get_cv_path($id) {
        return rtrim(FCPATH,"/")."/cv_pdf/".$id.".pdf";
    }
    $this->load->helper("form");
    echo form_open_multipart("/admin/artist/".$artist["id"],array("method"=>"post"));
    echo '<p>'.form_label("Name","name").'<br />'.form_input("name",$artist["name"]).'</p>';
    echo '<p>'.form_label("Surname","surname").'<br />'.form_input("surname",$artist["surname"]).'</p>';
    echo '<p>'.form_label("CV","pdf");
    $filename = get_cv_path($artist["id"]);
    if (file_exists($filename)) {
        echo '<span> | <a class="cv view" href="/cv_pdf/'.$artist["id"].'.pdf" target="_blank">View</a> | <a href="javascript:void(0);" class="delete cv" data-id="'.$artist["id"].'">Delete</a></span>';
    }
    echo '<br /><input type="file" name="pdf" accept="application/pdf" /></p>';
    echo '<div id="image">';
    if (!empty($artist["image_id"])) {
        echo '<p><a class="pick-image" href="javascript:void(0)"><img src="/images/195/'.$artist["image_id"].'.jpg" style="max-width:92px; max-height:92px" /></a></p>';
        echo '<p><a class="remove-image" href="javascript:void(0)">Remove image</a></p>';
        echo form_hidden('image_id',$artist["image_id"]);
    } else if ($artist["image_count"] > 0) {
        echo '<p><a class="add-image" href="javascript:void(0)">Main image</a></p>';
    }
    echo '</div>';
    echo '<p>'.form_checkbox("represented","1",$artist["represented"]).' '.form_label("Represented","represented").'</p>';
    echo '<p>'.form_submit("save","Save").'</p>';
    echo form_close();
?>
<div id="imagePicker"></div>
<script type="text/javascript" src="/js/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript">
    var imagePicker = new GalleryAdmin.ImagePicker();
    $('a.pick-image, a.add-image').on("click",addImage);
    function addImage() {
        var link = $(this);
        $('#imagePicker').show();
        imagePicker.load($('#imagePicker'),'/api/artist/<?php echo $artist["id"]; ?>/images',function(imageId){
            $('#imagePicker').empty().hide();
            if (imageId) {
                var galleryDiv = $('#image');
                galleryDiv.empty();
                galleryDiv.append($('<p></p>').append($('<a class="pick-image" href="javascript:void(0)"><img src="/images/195/'+imageId+'.jpg" style="max-width:92px; max-height:92px" /></a>').on("click",addImage)));
                galleryDiv.append($('<p></p>').append($('<a class="remove-image" href="javascript:void(0)">Remove image</a>').on("click",removeImage)));
                $('<input type="hidden" name="image_id" value="'+imageId+'" />').appendTo(galleryDiv);
            }
        });
    }
    function removeImage() {
        var link = $(this);
        var galleryDiv = $('#image');
        galleryDiv.empty().append($('<p></p>').append($('<a class="add-image" href="javascript:void(0);">Main image</a>').on("click",addImage)));
    }
    $('a.remove-image').on("click",removeImage);
    $("a.delete.cv").on("click",function(){
        if (!confirm("Are you sure you want to delete the CV?")) {
            return false;
        }
        function onError() {
            alert("Error deleting CV.");
        }
        $.ajax({
            "url":"/api/artist/"+$(this).data("id")+"/delete_cv",
            "dataType":"json",
            "success":function(data) {
                if (data) {
                    $(this).parent().remove();
                } else {
                    onError();
                }
            },
            "error":onError,
            "context":this
        });
        return false;
    });
</script>
<?php
}
?>