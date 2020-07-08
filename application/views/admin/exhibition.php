<?php
$url = "/admin/exhibitions";
$title = $text = "";
$start_date = new DateTime();
$end_date = new DateTime();
$reception_start = new DateTime();
$reception_end = new DateTime();
$image_id = "";
usort($artists,function($a,$b){
    if ($a["name"] == $b["name"]) {
        return 0;
    }
    return $a["name"] > $b["name"] ? 1 : -1;
});
$artist_ids = array();
$artist_options = array();
foreach ($artists as $artist) {
    $artist_options[] = array("id"=>$artist["id"],"text"=>trim($artist["name"].' '.$artist["surname"]));
}
if (!empty($exhibition)) {
    $url = "/admin/exhibition/".$exhibition["id"];
    if (!empty($exhibition["title"])) {
        $title = $exhibition["title"];
    }
    if (!empty($exhibition["text"])) {
        $text = $exhibition["text"];
    }
    $start_date = DateTime::createFromFormat("Y-m-d",$exhibition["start_date"]);
    $end_date = DateTime::createFromFormat("Y-m-d",$exhibition["end_date"]);
    $reception_start = DateTime::createFromFormat("Y-m-d H:i:s",$exhibition["reception_start"]);
    $reception_end = DateTime::createFromFormat("Y-m-d H:i:s",$exhibition["reception_end"]);
    if (!empty($exhibition["artists"])) {
        $artist_ids = array_keys($exhibition["artists"]);
    }
    if (!empty($exhibition["image_id"])) {
        $image_id = $exhibition["image_id"];
    }
}
$this->load->helper("form");
echo form_open($url,array("method"=>"post"));
if ($image_id) {
    echo '<div id="image"><p><a class="pick-image" href="javascript:void(0)"><img src="/images/410/'.$image_id.'.jpg" /></a></p><p><a class="remove-image" href="javascript:void(0)">Remove image</a></p></div>';
} else {
    echo '<p id="no-image"><a class="pick-image" href="javascript:void(0)">Add image</a></p>';
}
echo '<p>'.form_label("Title","title").'<br />'.form_input("title",$title).'</p>';
echo '<p>'.form_label("Text","text").'<br />'.form_textarea("text",$text).'</p>';
echo '<p>'.form_label("Start date","start_date").'<br />'.form_input("start_date",$start_date->format("Y-m-d"),'class="date"').'</p>';
echo '<p>'.form_label("End date","end_date").'<br />'.form_input("end_date",$end_date->format("Y-m-d"),'class="date"').'</p>';
$times = array();
foreach (range(0,23) as $hour) {
    $time = str_pad($hour,2,"0",STR_PAD_LEFT);
    $times[$time.":00"] = $time.":00";
    $times[$time.":30"] = $time.":30";
}
echo '<p>'.form_label("Reception","reception_start").'<br />'.form_input("reception_start",$reception_start->format("Y-m-d"),'class="date"').' '.form_dropdown("reception_starttime",$times,array($reception_start->format("H:i"))).'–'.form_dropdown("reception_endtime",$times,array($reception_end->format("H:i"))).'</p>';
echo '<p>'.form_label("Artists","artist_ids[]").'<div id="artists"></div></p>';
foreach ($artist_ids as $artist_id) {
    echo '<input type="hidden" name="artist_ids[]" value="'.$artist_id.'" />';
}
echo form_hidden("image_id",$image_id);
echo '<p>'.form_submit("save","Save").'</p>';
echo form_close();
?>
<div id="imagePicker"></div>
<script src="//tinymce.cachefly.net/4.0/tinymce.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript">
    tinymce.init({
        selector: "textarea",
        valid_elements: "a[href|target=_blank],strong/b,em/i,p",
        menubar: false,
        plugins: "link autolink",
        toolbar: "bold italic link unlink",
        statusbar: false
    });
    var imagePicker = new GalleryAdmin.ImagePicker();
    function addImage() {
        $('#imagePicker').show();
        imagePicker.load($('#imagePicker'),'/api/images',function(imageId){
            $('#imagePicker').empty().hide();
            if (imageId) {
                $("#image, #no-image").remove();
                $("input[name='image_id']").val(imageId);
                $("form").prepend('<div id="image"><p><a class="pick-image" href="javascript:void(0)"><img src="/images/410/'+imageId+'.jpg" /></a></p><p><a class="remove-image" href="javascript:void(0)">Remove image</a></p></div>');
                $('a.remove-image').on("click",removeImage);
                $('a.pick-image').on("click",addImage);
            }
        });
    }
    function removeImage() {
        $("#image, #no-image").remove();
        $("input[name='image_id']").val("");
        $("form").prepend($('<p id="no-image"></p>').append($('<a class="pick-image" href="javascript:void(0)">Add image</a>').on("click",addImage)));
    }
    $('a.remove-image').on("click",removeImage);
    $('a.pick-image').on("click",addImage);
    $("input.date").datepicker({"dateFormat":"yy-mm-dd"});

    var artistSelector = new GalleryAdmin.MultipleItemSelector(<?php echo json_encode($artist_options); ?>,<?php echo json_encode($artist_ids); ?>);
    artistSelector.changeCallback = function(selectedArtists){
        $("input[name='artist_ids[]']").remove();
        for (var i=0; i<selectedArtists.length; i++) {
            $("#artists").after('<input name="artist_ids[]" value="'+selectedArtists[i].id+'" type="hidden" />');
        }
    }
    artistSelector.appendTo($("#artists"));

    $("form").on("submit",function(){
        console.log($("form").get(0).elements);
    });

</script>