<div id="upload"></div>

<div id="vimeoPreview"></div>
<div id="vimeoButtons" style="display:none"><button class="cancel">Cancel</button> <button class="save">Save</button></div>
<div id="vimeoForm">
    <h2>Add Vimeo video</h2>
    <p><label for="vimeoUrl">Vimeo URL</label><br /><input type="url" id="vimeoUrl" /></p>
    <p><button class="vimeoButton" disabled>Add</button></p>
</div>

<div id="imageList">
<?php
if (!empty($images)) {
    $this->load->view("admin/image_list",array("images"=>$images));
}
?>
</div>
<script type="text/javascript" src="/js/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="/js/jquery.exif.js"></script>
<script type="text/javascript">
    //<![CDATA[
    (function(){
        $("#upload").imageUpload().on("complete",function(){
            location.reload();
        });
        $("#vimeoUrl").on("keyup",function(){
            $("button.vimeoButton").prop("disabled",$.trim($(this).val()).length == 0);
        });
        $("button.vimeoButton").one("click",function(){
            $("#upload, #vimeoForm").hide();
            var url = $.trim($("#vimeoUrl").val());
            var matches = url.match(/vimeo\.com\/[^0-9]*([0-9]+)/i);
            if (url.length == 0 || !matches) {
                alert("Please enter a valid Vimeo URL.");
                return;
            }
            var videoId = matches[1];
            $("#vimeoPreview").html("Loading video info. Please wait.");
            $.ajax({
                "url":"/api/vimeo/"+videoId,
                "dataType":"json",
                "success":function(data){
                    $("#vimeoPreview").imageCrop(data.thumbnail_large,410,410).on("crop",function(event){
                        $("#vimeoButtons").show();
                        $("#vimeoButtons button").eq(1).off("click").on("click",function(){
                            $.ajax({
                                "url":"/api/vimeo/"+videoId,
                                "type":"post",
                                "dataType":"json",
                                "data":{"crop":event.crop},
                                "success":function(data) {
                                    location.reload();
                                },
                                "error":function() {
                                    alert("Error saving video");
                                    $("#vimeoPreview").empty();
                                    $("#upload, #vimeoForm").show();
                                    $("#vimeoButtons").hide();
                                }
                            });
                        });
                        $("#vimeoButtons button").eq(0).off("click").on("click",function(){
                            $("#vimeoPreview").empty();
                            $("#upload, #vimeoForm").show();
                            $("#vimeoButtons").hide();
                        })
                    }).on("error",function(){
                        $("#vimeoPreview").empty();
                        $("#upload, #vimeoForm").show();
                    });
                },
                "error":function() {
                    alert("Error loading Vimeo video.");
                    $("#upload, #vimeoForm").show();
                }
            });
        });
    })();
    //]]>
</script>
