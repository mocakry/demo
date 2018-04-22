(function ($) {
  $.extend({
    uploadPreview : function (options) {

      // Options + Defaults
      var settings = $.extend({
        input_field: ".image-input",
        preview_box: ".image-preview",
        label_field: ".image-label",
        label_default: "Choose File",
        label_selected: "Change File",
        back_image: "",
        no_label: false
      }, options);



      // Check if FileReader is available
      if (window.File && window.FileList && window.FileReader) {

        if (typeof($(settings.input_field)) !== 'undefined' && $(settings.input_field) !== null) {
          $(settings.input_field).change(function(event) {

            EVT = event || window.event;
            var files = event.target.files;

            if (files.length > 0) {
                var file = files[0];
                var reader = new FileReader();

                // Load file
                reader.addEventListener("load",function(event) {
                    var loadedFile = event.target;

                    // Check format
                    if (file.type.match('image')) {
                        // Image
                        $(settings.preview_box).css("background-image", "url("+loadedFile.result+")");
                        $(settings.preview_box).css("background-size", "100% 100%");
                        $(settings.preview_box).css("background-position", "center center");
                        // changeAvatar();
                    } else if (file.type.match('audio')) {
                        // Audio
                        $(settings.preview_box).html("<audio controls><source src='" + loadedFile.result + "' type='" + file.type + "' />Your browser does not support the audio element.</audio>");
                    } else {
                        alert("上传格式不正确！");
                    }
                });

              if (settings.no_label == false) {
                // Change label
                $(settings.label_field).html(settings.label_selected);
              }

              // Read the file
              reader.readAsDataURL(file);
            } else {
              if (settings.no_label == false) {
                // Change label
                $(settings.label_field).html(settings.label_default);
              }

              // Clear background
              $(settings.preview_box).css("background-image", 'url( '+settings.back_image+' )');
              $('.fix-pre-images').show(); 

              // Remove Audio
              $(settings.preview_box + " audio").remove();
            }
          });
        }
      } else {
        
        alert("You need a browser with file reader support, to use this form properly.");
        return false;
      }
    }
  });
})(jQuery);