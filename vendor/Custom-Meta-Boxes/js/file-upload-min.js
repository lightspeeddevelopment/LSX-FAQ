jQuery(document).ready(function(){jQuery(document).on("click",".cmb-file-upload",function(e){e.preventDefault();var i=jQuery(this),t=jQuery(this).parent(),a={multiple:!1,title:"Select File"};library=t.attr("data-type").split(","),library.length>0&&(a.library={type:library});var r=wp.media(a);r.on("select",function(){var e=r.state().get("selection"),a=e.first(),l=t.find(".cmb-file-holder");if(jQuery(t).find(".cmb-file-upload-input").val(a.id),i.hide(),r.close(),l.html(""),l.show(),l.siblings(".cmb-remove-file").show(),"CMB_Image_Field"===t.closest(".field-item").attr("data-class")){var n={action:"cmb_request_image",id:a.attributes.id,width:t.width(),height:t.height(),crop:l.attr("data-crop"),nonce:i.attr("data-nonce")};l.addClass("cmb-loading"),jQuery.post(ajaxurl,n,function(e){jQuery("<img />",{src:e}).prependTo(l),l.removeClass("cmb-loading")}).fail(function(){jQuery("<img />",{src:a.attributes.url}).prependTo(l),l.removeClass("cmb-loading")})}else jQuery("<img />",{src:a.attributes.icon}).prependTo(l),l.append(jQuery('<div class="cmb-file-name" />').html("<strong>"+a.attributes.filename+"</strong>"))}),r.open()}),jQuery(document).on("click",".cmb-remove-file",function(e){e.preventDefault();var i=jQuery(this).parent().parent();i.find(".cmb-file-holder").html("").hide(),i.find(".cmb-file-upload-input").val(""),i.find(".cmb-file-upload").show().css("display","inline-block"),i.find(".cmb-remove-file").hide()});var e=function(){jQuery(".cmb-file-wrap").each(function(){var e=jQuery(this),i=e.closest(".postbox"),t=i.width()-12-10-10,a=e.height()/e.width();e.attr("data-original-width")?e.width(e.attr("data-original-width")):e.attr("data-original-width",e.width()),e.attr("data-original-height")?e.height(e.attr("data-original-height")):e.attr("data-original-height",e.height()),e.width()>t&&(e.width(t),e.find(".cmb-file-wrap-placeholder").width(t-8),e.height(t*a),e.css("line-height",t*a+"px"),e.find(".cmb-file-wrap-placeholder").height(t*a-8))})};e(),jQuery(window).resize(e)});