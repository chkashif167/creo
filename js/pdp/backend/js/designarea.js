var tshirt = jQuery.noConflict();
tshirt(function($){
	var baseUrl = $("#base_url").val();
	var mediaUrl = $("#media_url").val();
	Tshirt = {
        previewImageDesignArea : function(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                var designObj = $('#design_image' );
                reader.onload = function (e) {
                    //console.log(e);
                	designObj.attr('src', e.target.result).load(function(){
                        var w_inlay = designObj.width(),
                                h_inlay = designObj.height();
                            //$('.img_area_' ).css("margin-left",(parseInt($('.tab-content').width())-w_inlay)/2+'px');
                        	$('.img_area').css({"width" : w_inlay + 'px'});
                            $('.inlay_area').css({"width":w_inlay+'px','height':h_inlay+'px',"left":'0',"top":'0'});
                            $('#inlay_w').val(w_inlay);
                            $('#inlay_h').val(h_inlay);
                            $('#inlay_t').val(0);
                            $('#inlay_l').val(0);
                        Tshirt.showOverlayImage();
                    });
                	designObj.removeAttr('style');
                }
                reader.readAsDataURL(input.files[0]);
            }
        },
		previewOverImage : function(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                var designObj = $('#overlay_image' );
                reader.onload = function (e) {
                	designObj.attr('src', e.target.result).load(function(){
                        $("#inlay_w").val($(this)[0].naturalWidth);
						$("#inlay_h").val($(this)[0].naturalHeight);
                        Tshirt.showOverlayImage();
                    });
                }
                reader.readAsDataURL(input.files[0]);
            }
        },
        showOverlayImage: function() {
            console.log($("#design_image:visible").length);
            if($("#design_image").attr("src") != "" && $("#design_image:visible").length) {
                $("#overlay_image").css({"position" : "absolute", "top" : 0, "left" : 0});
            } else {
                $("#overlay_image").css({"position" : "relative", "top" : 0, "left" : 0});
            }
        },
        submit : function() {
        	$("#pdp_add_inlay_form").submit();
        },
		activeColorPicker : function(selector) {
			/**Color picker**/
			$(selector).ColorPicker({
				color: '#c4b5c4',
				onShow: function (colpkr) {
					$(colpkr).fadeIn(300);
					return false;
				},
				onHide: function (colpkr) {
					$(colpkr).fadeOut(300);
					return false;
				},
				onChange: function (hsb, hex, rgb) {
					$(selector).css('backgroundColor', '#' + hex);
					$(selector).val(hex);
				}
			});
		},
        backgroundColor: function() {
            var selector = "#color_code";
            $(selector).ColorPicker({
                //color: '#c4b5c4',
                onShow: function (colpkr) {
                    $(colpkr).fadeIn(300);
                    return false;
                },
                onHide: function (colpkr) {
                    $(colpkr).fadeOut(300);
                    return false;
                },
                onChange: function (hsb, hex, rgb) {
                    $(selector).css('backgroundColor', '#' + hex);
                    $(".pdp-img-area").css('backgroundColor', '#' + hex);
                    $(selector).val(hex);
                }
            });
        }(),
        switchBackgroundType: function() {
            $("#background_type").change(function() {
                if(this.value == "image") {
                    $("#color_code").removeClass("required-entry").closest("div.design-area-input").hide();
                    $("#color_name").closest("div.design-area-input").hide();
                    if($("#background_image").attr("rel") == "") {
                        $("#background_image").addClass("required-entry");    
                    }
                    $("#background_image").closest("div.design-area-input").show();
                    $("#design_image").show();
                } else {
                    $("#background_image").removeClass("required-entry").closest("div.design-area-input").hide();
                    $("#color_code").addClass("required-entry").closest("div.design-area-input").show();
                    $("#color_name").closest("div.design-area-input").show();
                    $("#design_image").hide();
                }
                Tshirt.showOverlayImage();
            }).change();
        },
		initializeBoxDesignArea : function(design_area){
			var iWidth = $('#inlay_w' ),
				iHeight = $('#inlay_h' ),
				iTop = $('#inlay_t' ),
				iLeft = $('#inlay_l' );
			
            var w = iWidth.val(),
                h = iHeight.val(),
                t = iTop.val(),  
                l = iLeft.val();
			var src = $('#design_image').attr('src');
			var img = new Image();
			img.src = src;
			//$('.img_area' ).attr("style",'width:' + img.width + 'px; height:' + img.height + 'px');
			$('.img_area').attr("style",'width:' + img.width);
            $('.inlay_area').attr("style",'width:'+w+'px; height:'+h+'px;top:'+t+'px;left:'+l+'px;');
            $('.inlay_area').resizable({
                containment: '.img_area' ,
    			aspectRatio: false,
    			handles:     'se',
                resize: function(event) {
                	iWidth.val(parseInt($(this).width()));
                    iHeight.val(parseInt($(this).height()));
                }
    		});
            $( ".inlay_area").draggable({ containment: '.img_area',
                start: function(){
                    
                },
                drag: function(){
                    iTop.val(parseInt($(this).position().top));
                    iLeft.val(parseInt($(this).position().left));        
                }
             });
            $('.pdp-form-container').append('<div id="save_img"></div>');
            $('#save_img' ).css("opacity",0).append('<image id="img_forsave" src="'+$('#design_image').attr("src")+'" />');
            var w_img_back;
            $('#img_forsave').load(function(){
                w_img_back =  $(this).width();
                $('#save_img' ).remove();
                var w_wrap = $('.tab-content').width();
       	     	$('.img_area' ).attr("style",'margin:0 0 0 '+(parseInt(w_wrap)-parseInt(w_img_back))/2+'px');
            }); 
             iWidth.change(function(){
                var max_w = $('.img_area' ).width(),
                    change_w = $(this).val(),
                    left_cr = iLeft.val(),
                    max_w_cr = parseInt(max_w)-parseInt(left_cr)-2;
                if(change_w>max_w_cr){
                    change_w = max_w_cr;
                    $(this).val(max_w_cr);
                }  
                if(change_w<10){
                    change_w = 10;
                    $(this).val(10);
                }   
                $('.inlay_area').width(change_w);
             });
             
             iHeight.change(function(){
                var max_h = $('.img_area').height(),
                    change_h = $(this).val(),
                    top_cr = iTop.val(),
                    max_h_cr = parseInt(max_h)-parseInt(top_cr)-2;
                if(change_h>max_h_cr){
                    change_h = max_h_cr;
                    $(this).val(max_h_cr);
                }  
                if(change_h<10){
                    change_h = 10;
                    $(this).val(10);
                }   
                $('.inlay_area').height(change_h);
             });
             iLeft.change(function(){
                var max_h = $('.img_area').width(),
                    change_h = $(this).val(),
                    top_cr = iWidth.val(),
                    max_h_cr = parseInt(max_h)-parseInt(top_cr)-2;
                if(change_h>max_h_cr){
                    change_h = max_h_cr;
                    $(this).val(max_h_cr);
                }  
                if(change_h<0){
                    change_h = 0;
                    $(this).val(0);
                }
                $('.inlay_area').css("left",change_h+'px');
             });
             iTop.change(function(){
                var max_t = $('.img_area').height(),
                    change_t = $(this).val(),
                    height_cr = iHeight.val(),
                    max_t_cr = parseInt(max_t)-parseInt(height_cr)-2;
                if(change_t>max_t_cr){
                    change_t = max_t_cr;
                    $(this).val(max_t_cr);
                }  
                if(change_t<0){
                    change_t = 0;
                    $(this).val(0);
                }   
                $('.inlay_area').css("top",change_t+'px');
             });
             var first_click = 0;
             $('.save').click(function(){
                var check = true;
                $(this).parents('form').find('.validation-failed').each(function(){
                    if($(this).val()!=''){
                        $(this).removeClass('validation-failed').next().hide();
                    }else{
                        check = false;
                    }
                });
                if((first_click++ > 0)&&(check)){
                    $(this).parents('form').submit();
                }
             })
        }
	}
    Tshirt.switchBackgroundType();
});