<?php
if (empty($image) || !isset($artists)) {
    return;
}
$this->load->helper("form");
echo form_open("/admin/image/".$image["id"],array("method"=>"post","id"=>"imageForm".$image["id"]));
echo '<p><img src="/images/195/'.$image["id"].($image["version"] ? "-".$image["version"] : "").'.jpg" alt="image" id="thumbnail" /></p>';
echo '<div id="upload"></div>';
echo '<p>'.form_fieldset("Dimensions");
echo '<p>'.form_label("Height (inches)","height").'<br />'.form_input("height",$image["height"]).'</p>';
echo '<p>'.form_label("Width (inches)","width").'<br />'.form_input("width",$image["width"]).'</p>';
echo '<p>'.form_label("Depth (inches)","depth").'<br />'.form_input("depth",$image["depth"]).'</p>';
echo form_fieldset_close().'</p>';
echo '<p>'.form_label("Year","year").'<br />'.form_input("year",$image["creation_year"]).'</p>';
$options = array();
foreach ($artists as $artist) {
    $options[] = array("id"=>$artist["id"],"text"=>$artist["name"]);
}
$selected = array();
if (!empty($image["artists"])) {
    $selected = array_keys($image["artists"]);
    foreach ($selected as $artist) {
        echo form_hidden("artists[]",$artist);
    }
}
echo '<p>'.form_label("Artist(s)","artists[]").'<div id="artists"></div></p>';
echo '<p>'.form_label("Title","title").'<br />'.form_input("title",@$image["title"]).'</p>';
echo '<p>'.form_label("Description","description").'<br />'.form_textarea("description",@$image["description"]).'</p>';
echo '<p>'.form_submit('save','Save').'</p>';
echo form_close();
?>
<p><a id="deleteImage" href="/admin/image/<?php echo $image['id']; ?>/delete">Delete image</a></p>
<script type="text/javascript" src="/js/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="/js/jquery.exif.js"></script>
<script type="text/javascript">
    //<![CDATA[
    /*
    // Disabled upon request
    var form = $("#imageForm<?php echo $image["id"]; ?>");
    form.find("input[name='title[en]'],input[name='title[fr]'],textarea[name='description[en]'],textarea[name='description[fr]']").on("change",function(){
        var params = /^(.+)\[(.+)\]$/.exec($(this).attr("name"));
        var name = params[1];
        var lang = params[2];
        var otherLang = lang == "en" ? "fr" : "en";
        var otherField = form.find("*[name='"+name+"["+otherLang+"]']");
        if ($.trim($(this).val()).length > 0 && $.trim(otherField.val()).length == 0) {
            otherField.val($(this).val());
        }
    });
    */
    $("#deleteImage").on("click",function(){
        return confirm("Are you sure you want to delete the image. It cannot be undone.");
    });
    $("#upload").imageUpload(<?php echo $image["id"]; ?>).on("complete",function(event){
        var thumbnailParent = $("#thumbnail").parent();
        $("#thumbnail").remove();
        thumbnailParent.append('<img id="thumbnail" src="/images/195/'+event.imageId+(event.version ? '-'+event.version : '')+'.jpg?t='+(new Date().getTime())+'" alt="image" />');
    }).on("start",function(){
            $("#thumbnail").hide();
        }).on("error cancel",function(){
            $("#thumbnail").show();
        });
    var artistSelector = new GalleryAdmin.MultipleItemSelector(<?php echo json_encode($options); ?>,<?php echo json_encode($selected); ?>);
    artistSelector.changeCallback = function(selectedArtists) {
        $("input[name='artists[]']").remove();
        for (var i=0; i<selectedArtists.length; i++) {
            $('<input type="hidden" name="artists[]" value="'+selectedArtists[i].id+'" />').appendTo($("#artists"));
        }
    }
    artistSelector.appendTo($("#artists"));
    //]]>
</script>