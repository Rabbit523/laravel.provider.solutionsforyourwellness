$(document).ready(function() {
        $("#image").on('change', function() {
          //Get count of selected filesimage-holder
          var countFiles = $(this)[0].files.length;
          var imgPath = $(this)[0].value;
          var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
          var image_holder = $("#image-holder");
          image_holder.empty();
          if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof(FileReader) != "undefined") {
              //loop for each file selected for uploaded.
              for (var i = 0; i < countFiles; i++)
              {
                var reader = new FileReader();
                reader.onload = function(e) {
                  $(
					          "<img />", {
                    "src": e.target.result,
                    "class": "image-thumbnail",
					          "height": "80px",
					          "width": "120px",
                  }).appendTo(image_holder);
                }
                image_holder.show();
                reader.readAsDataURL($(this)[0].files[i]);
              }
            } else {
              alert("This browser does not support FileReader.");
            }
          } else {
            alert("Pls select only images");
          }
        });
      });
