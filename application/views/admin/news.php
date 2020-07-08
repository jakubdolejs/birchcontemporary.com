<?php
if (!empty($news)) {
    $action = "/admin/news/".$news["id"];
    $date = DateTime::createFromFormat("Y-m-d",$news["date"]);
} else {
    $action = "/admin/news";
    $date = new DateTime();
}
$artist_options = array();
foreach ($artists as $artist) {
    $artist_options[] = array("id"=>$artist["id"],"text"=>trim($artist["name"].' '.$artist["surname"]));
}
$exhibition_options = array();
foreach ($exhibitions as $exhibition) {
    $exhibition_options[] = array("id"=>$exhibition["id"],"text"=>$exhibition["title"]);
}
$this->load->helper("form");
echo form_open($action,'method="post"');
echo '<p>'.form_label("Text","html").'<br />'.form_textarea("html",@$news["html"]).'</p>';
echo '<p>'.form_label("Publish date","date").'<br />'.form_input("date",$date->format("Y-m-d"),'class="date"').'</p>';
echo '<p>'.form_label("Artists mentioned in the story","artist_ids[]").'<div id="artists"></div></p>';
if (!empty($news["artists"])) {
    foreach (array_keys($news["artists"]) as $artist_id) {
        echo '<input type="hidden" name="artist_ids[]" value="'.$artist_id.'" />';
    }
}
echo '<p>'.form_label("Exhibitions mentioned in the story","exhibition_ids[]").'<div id="exhibitions"></div></p>';
if (!empty($news["exhibitions"])) {
    foreach (array_keys($news["exhibitions"]) as $exhibition_id) {
        echo '<input type="hidden" name="exhibition_ids[]" value="'.$exhibition_id.'" />';
    }
}
echo '<p>'.form_submit("save","Save").'</p>';
echo form_close();
?>
<script src="//tinymce.cachefly.net/4.0/tinymce.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript">
    tinymce.init({
        selector: "textarea[name='html']",
        valid_elements: "a[href|target=_blank],strong/b,em/i,p",
        menubar: false,
        plugins: "link autolink",
        toolbar: "bold italic link unlink",
        statusbar: false
    });
    $("input.date").datepicker({"dateFormat":"yy-mm-dd"});

    var artistSelector = new GalleryAdmin.MultipleItemSelector(<?php echo json_encode($artist_options); ?>,<?php echo !empty($news["artists"]) ? json_encode(array_keys($news["artists"])) : "[]"; ?>);
    artistSelector.changeCallback = function(selectedArtists){
        $("input[name='artist_ids[]']").remove();
        for (var i=0; i<selectedArtists.length; i++) {
            $("#artists").after('<input name="artist_ids[]" value="'+selectedArtists[i].id+'" type="hidden" />');
        }
    }
    artistSelector.appendTo($("#artists"));

    var exhibitionSelector = new GalleryAdmin.MultipleItemSelector(<?php echo json_encode($exhibition_options); ?>,<?php echo !empty($news["exhibitions"]) ? json_encode(array_keys($news["exhibitions"])) : "[]"; ?>);
    exhibitionSelector.changeCallback = function(selectedExhibitions){
        $("input[name='exhibition_ids[]']").remove();
        for (var i=0; i<selectedExhibitions.length; i++) {
            $("#exhibitions").after('<input name="exhibition_ids[]" value="'+selectedExhibitions[i].id+'" type="hidden" />');
        }
    }
    exhibitionSelector.appendTo($("#exhibitions"));

    $("form").on("submit",function(){
        console.log($("form").get(0).elements);
    });

</script>
