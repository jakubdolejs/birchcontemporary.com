GalleryAdmin = {
    imageUpload: function(imageId) {
        var xhr = null;
        this.subscribe = function(eventType,callback) {
            if (!callbacks.hasOwnProperty(eventType)) {
                return;
            }
            callbacks[eventType].add(callback);
        }
        this.unsubscribe = function(callback) {
            if (!callbacks.hasOwnProperty(eventType)) {
                return;
            }
            callbacks[eventType].remove(callback);
        }
        var callbacks = {
            progress: $.Callbacks(),
            complete: $.Callbacks(),
            error: $.Callbacks(),
            abort: $.Callbacks()
        };
        this.upload = function(files,sizes) {
            xhr = new XMLHttpRequest();
            var fd = new FormData();
            for (var i=0; i<files.length; i++) {
                fd.append("file["+i+"]", files[i]);
                fd.append("crop["+i+"]", sizes[i]);
            }
            if ($.type(imageId) !== "undefined") {
                fd.append("image_id",imageId);
            }
            xhr.upload.addEventListener("progress", function(event) {
                if (event.lengthComputable) {
                    callbacks.progress.fire({"loaded":event.loaded,"total":event.total});
                }
            }, false);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4) {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status == 200 && !response.hasOwnProperty("error")) {
                        callbacks.complete.fire(response);
                    } else {
                        callbacks.error.fire(response.error);
                    }
                }
            }
            try {
                xhr.ontimeout = function() {
                    callbacks.error.fire("Request timed out");
                }
                xhr.timeout = 120000; // 2 minutes should do
            } catch (error) {
                //timeout probably not supported
            }
            xhr.open("POST", "/admin/image_upload");
            xhr.send(fd);
        }
        this.abort = function() {
            if (xhr) {
                xhr.abort();
            }
        }
    },
    imageForm: function(imageId) {
        var _this = this;
        var form = $('<form id="imageForm'+imageId+'" method="post"></form>');
        var loader = $('<p>Loading image info</p>').appendTo(form);
        $.ajax({
            "url":"/admin/image/"+imageId,
            "dataType":"json",
            "success":function(data) {
                loader.remove();
                $('#imageForm'+imageId).remove();
                form.append('<p><img src="/images/195/'+imageId+'.jpg" alt="image" /></p>');
                form.append($('<p><label for="width">Width</label><br /></p>').append('<input type="text" name="width" />'));
                if (data.width) {
                    form.find("input[name='width']").val(data.width);
                }
                form.append($('<p><label for="height">Height</label><br /></p>').append('<input type="text" name="height" />'));
                if (data.height) {
                    form.find("input[name='height']").val(data.height);
                }
                form.append($('<p><label for="depth">Depth</label><br /></p>').append('<input type="text" name="depth" />'));
                if (data.depth) {
                    form.find("input[name='depth']").val(data.depth);
                }
                form.append($('<p><label for="creation_year">Year</label><br /></p>').append('<input type="text" name="creation_year" />'));
                if (data.creation_year) {
                    form.find("input[name='creation_year']").val(data.creation_year);
                }
            },
            "error":function() {
                loader.text("Error loading image info");
            }
        });
        return form;
    },
    ImagePicker: function() {
        this.load = function(container,src,callback) {
            $.ajax({
                "url":src,
                "dataType":"json",
                "success":function(data) {
                    $(container).empty().append($('<p></p>').append($('<a href="javascript:void(0)">Cancel</a>').on("click",function(){
                        callback(null);
                    })));
                    if (data.length > 0) {
                        for (var i=0; i<data.length; i++) {
                            container.append($('<a href="javascript:void(0)" data-id="'+data[i].id+'" class="image-picker"><img src="/images/195/'+data[i].id+'.jpg" /></a>').on("click",function(){
                                callback($(this).attr("data-id"));
                            }));
                        }
                    }
                },
                "error":function() {
                    callback(null);
                }
            });
        }
    },
    MultipleItemSelector: function(items,selectedIds) {
        this.selectedItems = function() {
            var selected = [];
            element.find("div.item").each(function(){
                selected.push({"id":$(this).attr("data-id"),"text":$(this).find("span.name").text()});
            });
            return selected;
        }
        this.changeCallback = function(){

        }
        function addItem(id) {
            for (var i=0; i<availableItems.length; i++) {
                if (availableItems[i].id == id) {
                    select.before($('<div class="item" data-id="'+id+'"></div>').append($('<span class="name"></span>').text(availableItems[i].text)).append($('<a class="remove" data-id="'+id+'">&times;</a>').on("click",removeItem)));
                    availableItems.splice(i,1);
                    select.find("option:selected").remove();
                    _this.changeCallback(_this.selectedItems());
                    select.toggle(availableItems.length > 0);
                    return;
                }
            }
        }
        function removeItem() {
            var id = $(this).attr("data-id");
            $("div.item[data-id='"+id+"']").remove();
            for (var i=0; i<items.length; i++) {
                if (items[i].id == id) {
                    availableItems.push(items[i]);
                    break;
                }
            }
            populateSelect();
            _this.changeCallback(_this.selectedItems());
        }
        function sortFunc(a,b) {
            if (a.text == b.text) {
                return 0;
            } else {
                return a.text > b.text ? 1 : -1;
            }
        }
        function populateSelect() {
            select.empty().append('<option value=""></option>');
            availableItems.sort(sortFunc);
            for (var i=0; i<availableItems.length; i++) {
                select.append($('<option value="'+availableItems[i].id+'"></option>').text(availableItems[i].text));
            }
            select.toggle(availableItems.length > 0);
        }
        this.appendTo = function(elmt) {
            elmt.append(element);
        }
        this.remove = function() {
            element.remove();
        }
        var _this = this;
        var element = $('<div id="artists" class="multipicker"></div>');
        var select = $('<select></select>').on("change",function(){
            if ($(this).val()) {
                addItem($(this).val());
            }
        });
        element.append(select);
        var availableItems = items.slice();
        for (var i=0; i<items.length; i++) {
            if ($.type(selectedIds) === "array" && selectedIds.indexOf(items[i].id) > -1) {
                addItem(items[i].id);
            }
        }
        populateSelect();
    }
}
jQuery.fn.extend({
    "imageCrop":function(src,width,height,exif) {
        this.each(function(){
            var _this = this;
            $(_this).html("<p>Loading image. Please wait.</p>");
            var img = $('<img style="position:absolute" />');
            img.on("load",function(){
                $(this).off("error load");
                $(_this).html('<p class="imagePreview"><p>Adjust image cropping for thumbnails and exhibition pages</p><div style="width:'+width+'px;height:'+height+'px;position:relative;oveflow:hidden" class="croppedImage"><div style="position:absolute"></div></div></p>');
                var canvas = $('<canvas></canvas>');
                $(_this).find(".croppedImage div").append(canvas);
                var originalWidth = $(this).get(0).naturalWidth;
                var originalHeight = $(this).get(0).naturalHeight;
                var cropWidth = width;
                var cropHeight = height;
                var rotation = 0;
                if ($.type(exif) !== "undefined" && exif && exif.hasOwnProperty("Orientation")) {
                    switch (exif.Orientation) {
                        case 2:
                            //horizontal flip
                            break;
                        case 3:
                            rotation = 180;
                            break;
                        case 4:
                            //vertical flip
                            break;
                        case 5:
                            //vertical flip
                            var ow = originalWidth;
                            originalWidth = originalHeight;
                            originalHeight = ow;
                            rotation = -90;
                            break;
                        case 6:
                            var ow = originalWidth;
                            originalWidth = originalHeight;
                            originalHeight = ow;
                            rotation = 90;
                            break;
                        case 7:
                            //horizontal flip
                            var ow = originalWidth;
                            originalWidth = originalHeight;
                            originalHeight = ow;
                            rotation = -90;
                            break;
                        case 8:
                            var ow = originalWidth;
                            originalWidth = originalHeight;
                            originalHeight = ow;
                            rotation = -90;
                            break;
                    }
                }
                if (originalWidth < width) {
                    $(_this).find('.imagePreview, .warning').remove();
                    $(_this).prepend('<p class="warning">The image is too small. Please make sure it\'s at least '+width+' pixels wide.</p>');
                    $(_this).trigger("error");
                } else {
                    var imageRatio = originalWidth/originalHeight;
                    var cropRatio = cropWidth/cropHeight;
                    var w, h, axis;
                    if (imageRatio < cropRatio) {
                        w = cropWidth;
                        h = Math.round(w/imageRatio);
                        axis = "y";
                    } else {
                        h = cropHeight;
                        w = Math.round(h*imageRatio);
                        axis = "x";
                    }

                    canvas.attr("width",w).attr("height",h);
                    var ctx = canvas.get(0).getContext("2d");

                    if (rotation == 90) {
                        ctx.rotate(rotation*Math.PI/180);
                        ctx.drawImage(img.get(0),0,0-w,h,w);
                    } else if (rotation == -90) {
                        ctx.rotate(rotation*Math.PI/180);
                        ctx.drawImage(img.get(0),0-h,0,h,w);
                    } else {
                        ctx.drawImage(img.get(0),0,0,w,h);
                    }

                    var scale = w/originalWidth;
                    var imgContainerSize = {"width":(w-cropWidth)+w,"height":(h-cropHeight)+h,"left":cropWidth/2-((w-cropWidth)+w)/2,"top":cropHeight/2-((h-cropHeight)+h)/2};
                    canvas.draggable({"axis":axis,"containment":"parent"}).on("dragstop",function(event,ui){
                        var event = jQuery.Event("crop");
                        event.crop = [Math.round((w-cropWidth-ui.position.left)/scale),Math.round((h-cropHeight-ui.position.top)/scale),Math.round(cropWidth/scale),Math.round(cropHeight/scale)];
                        $(_this).trigger(event);
                    });
                    $(_this).find(".croppedImage div").css(imgContainerSize);
                    $(_this).find(".croppedImage").css({"overflow":"hidden"});
                    var event = jQuery.Event("crop");
                    event.crop = [Math.round((w-cropWidth-canvas.position().left)/scale),Math.round((h-cropHeight-canvas.position().top)/scale),Math.round(cropWidth/scale),Math.round(cropHeight/scale)];
                    $(_this).trigger(event);
                }
            }).on("error",function(){
                $(this).off("error load");
                $(_this).trigger("error");
            });
            img.attr("src",src);
        });
        return this;
    },
    "imageUpload":function(imageId) {
        this.each(function(){
            var _this = this;
            var form = $('<form class="uploadForm" method="post" enctype="multipart/form-data"></form>');
            var fileInput = $('<input type="file" name="file" accept="image/*" />');
            var cancelButton = $('<button class="cancelBtn" type="reset" style="display:none">Cancel</button>');
            var uploadButton = $('<button class="uploadBtn" disabled="disabled">Upload</button>');
            $(this).empty().append(form);
            form.append($('<p></p>').append(fileInput));
            form.append($('<p></p>').append(cancelButton).append(document.createTextNode(" ")).append(uploadButton));

            uploadButton.on("click",function(){
                var files = fileInput.get(0).files;
                var maxFiles = 1;
                if (files.length > maxFiles) {
                    alert("Please upload up to "+maxFiles+" files at a time.");
                    return false;
                }
                if (files.length > 0) {
                    form.hide();
                    $(_this).find('.imagePreview p, .uploading').remove();
                    $(_this).append('<p class="uploading">Uploading. Please wait.</p>');

                    function onProgress(data) {
                        if ($(_this).find(".progressBarContainer").length == 0) {
                            $('<div class="progressBarContainer"><div class="progressBar"></div></div>').appendTo($(_this));
                        }
                        $(_this).find("div.progressBar").width(data.loaded/data.total*$(_this).find("div.progressBarContainer").width());
                        if (data.loaded == data.total && data.loaded > 0) {
                            $(_this).find("div.progressBarContainer").remove();
                            $(_this).find("p.uploading").text("Resizing images. This may take a little while. Please be patient.");
                        }
                    }
                    function onComplete(response) {
                        $(_this).find(".progressBarContainer, .uploading").remove();
                        for (var i=0; i<response.length; i++) {
                            if (response[i].hasOwnProperty("id")) {
                                $(_this).find(".progressBarContainer, .uploading, .imagePreview, .warning").remove();
                                fileInput.show();
                                cancelButton.hide();
                                form.show();
                                var event = jQuery.Event("complete",{"imageId":response[i].id,"version":response[i].version});
                                $(_this).trigger(event);
                                return;
                            }
                        }
                    }
                    function onError(error) {
                        $(_this).find(".progressBarContainer, .uploading").remove();
                        form.show();
                        alert("Error uploading file");
                        $(_this).trigger("error");
                    }

                    var imageUpload = new GalleryAdmin.imageUpload(imageId);
                    imageUpload.subscribe("progress",onProgress);
                    imageUpload.subscribe("complete",onComplete);
                    imageUpload.subscribe("error",onError);
                    imageUpload.subscribe("abort",onError);
                    imageUpload.upload(files,[crop]);
                }
                return false;
            });
            cancelButton.on("click",function(){
                $(_this).find('.imagePreview, .warning').remove();
                uploadButton.attr("disabled","disabled");
                fileInput.show();
                $(this).hide();
                $(_this).trigger("cancel");
            });
            fileInput.on("change",function(){
                $(_this).trigger("start");
                fileInput.hide();
                cancelButton.show();
                if (this.files.length > 0) {
                    var files = this.files;
                    try {
                        $(this).fileExif(function(exif){
                            if (window.File && window.FileReader && window.FileList && window.Blob) {
                                for (var i=0, f; f=files[i]; i++) {
                                    if (!f.type.match('image.*')) {
                                        continue;
                                    }
                                    var reader = new FileReader();
                                    reader.onload = (function(theFile) {
                                        return function(e) {
                                            $(_this).find('.imagePreview, .warning').remove();
                                            var img = $('<img style="position:absolute" />');
                                            img.on("load",function(){
                                                $(this).off("load");
                                                var originalWidth = $(this).get(0).naturalWidth;
                                                var originalHeight = $(this).get(0).naturalHeight;
                                                var cropWidth = 410;
                                                var cropHeight = 410;
                                                var rotation = 0;
                                                if (exif && exif.hasOwnProperty("Orientation")) {
                                                    switch (exif.Orientation) {
                                                        case 2:
                                                            //horizontal flip
                                                            break;
                                                        case 3:
                                                            rotation = 180;
                                                            break;
                                                        case 4:
                                                            //vertical flip
                                                            break;
                                                        case 5:
                                                            //vertical flip
                                                            var ow = originalWidth;
                                                            originalWidth = originalHeight;
                                                            originalHeight = ow;
                                                            rotation = -90;
                                                            break;
                                                        case 6:
                                                            var ow = originalWidth;
                                                            originalWidth = originalHeight;
                                                            originalHeight = ow;
                                                            rotation = 90;
                                                            break;
                                                        case 7:
                                                            //horizontal flip
                                                            var ow = originalWidth;
                                                            originalWidth = originalHeight;
                                                            originalHeight = ow;
                                                            rotation = -90;
                                                            break;
                                                        case 8:
                                                            var ow = originalWidth;
                                                            originalWidth = originalHeight;
                                                            originalHeight = ow;
                                                            rotation = -90;
                                                            break;
                                                    }
                                                }
                                                if (originalWidth < 700) {
                                                    $(_this).find('.imagePreview, .warning').remove();
                                                    form.prepend('<p class="warning">The image is too small. Please make sure it\'s at least 700 pixels wide.</p>');
                                                    cancelButton.hide();
                                                    fileInput.show();
                                                } else {
                                                    var imageRatio = originalWidth/originalHeight;
                                                    var cropRatio = cropWidth/cropHeight;
                                                    var w, h, axis;
                                                    if (imageRatio < cropRatio) {
                                                        w = cropWidth;
                                                        h = Math.round(w/imageRatio);
                                                        axis = "y";
                                                    } else {
                                                        h = cropHeight;
                                                        w = Math.round(h*imageRatio);
                                                        axis = "x";
                                                    }

                                                    canvas.attr("width",w).attr("height",h);
                                                    var ctx = canvas.get(0).getContext("2d");

                                                    if (rotation == 90) {
                                                        ctx.rotate(rotation*Math.PI/180);
                                                        ctx.drawImage(img.get(0),0,0-w,h,w);
                                                    } else if (rotation == -90) {
                                                        ctx.rotate(rotation*Math.PI/180);
                                                        ctx.drawImage(img.get(0),0-h,0,h,w);
                                                    } else {
                                                        ctx.drawImage(img.get(0),0,0,w,h);
                                                    }

                                                    var scale = w/originalWidth;
                                                    var imgContainerSize = {"width":(w-cropWidth)+w,"height":(h-cropHeight)+h,"left":cropWidth/2-((w-cropWidth)+w)/2,"top":cropHeight/2-((h-cropHeight)+h)/2};
                                                    canvas.draggable({"axis":axis,"containment":"parent"}).on("dragstop",function(event,ui){
                                                        crop = [Math.round((w-cropWidth-ui.position.left)/scale),Math.round((h-cropHeight-ui.position.top)/scale),Math.round(cropWidth/scale),Math.round(cropHeight/scale)];
                                                    });
                                                    $(_this).find(".croppedImage div").css(imgContainerSize);
                                                    $(_this).find(".croppedImage").css({"overflow":"hidden"});
                                                    crop = [Math.round((w-cropWidth-canvas.position().left)/scale),Math.round((h-cropHeight-canvas.position().top)/scale),Math.round(cropWidth/scale),Math.round(cropHeight/scale)];
                                                    uploadButton.removeAttr("disabled");
                                                }
                                            });
                                            $(_this).find('.imagePreview, .warning').remove();
                                            form.before('<p class="imagePreview"></p>');
                                            $(_this).find("p.imagePreview").append('<p>Adjust image cropping for thumbnails and exhibition pages</p>').append('<div style="width:410px;height:410px;position:relative;oveflow:hidden" class="croppedImage"><div style="position:absolute"></div></div>');
                                            var canvas = $('<canvas></canvas>');
                                            $(_this).find(".croppedImage div").append(canvas);
                                            img.attr("src",e.target.result);
                                        };
                                    })(f);
                                    reader.readAsDataURL(f);
                                }
                            } else {
                                alert("Your browser does not support advanced features that allow for cropping images before they are uploaded. Please use the latest version of Google Chrome.");
                            }
                        });
                    } catch (error) {
                        alert("Your browser does not support advanced features that allow for cropping images before they are uploaded. Please use the latest version of Google Chrome.");
                    }
                } else {
                    uploadButton.attr("disabled","disabled");
                }
            });
        });
        return this;
    }
});