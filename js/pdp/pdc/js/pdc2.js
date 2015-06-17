///////////////////////////All function in themes///////////////////////////////////////////////////////////////
var pdc2 = jQuery.noConflict();
pdc2(document).ready(function($){
var m = $('#url_site').val().replace('index.php/',''),
    pdcMediaUrl = $("#pdp_media_url").val();
    $('[pdc-action="show-textbox"]').on('click',function(){
       $('[pdc-block="textbox"]').slideToggle(0);
       $('#font_outline_colorpicker').hide();
       $('.tab_content:not(#add_text, #pdc_rotate_item), .pdc_extra_item').slideUp(0);
       $('.pdc_text_list li.active').removeClass("active");
    });
    $('[pdc-action="add-text"]').click(function(){
        var text = $('[pdc-data="text"]').val();
        if(text!=''){
            CanvasEvents.addText(text);
        }
    });
    $('[pdc-side]').click(function(){
        CanvasEvents.clearSelected();
        if($(this).hasClass('active')){ return };            
        //canvasEvents.save_design($('#pdc_side_items li.active').index(),$('#main_image').width(),$('#main_image').height());
        $('.active[pdc-side]').removeClass('active');
        $(this).addClass("active");
        var tab = $(this).attr("tab"),
            tab_current = $('#wrap_inlay').attr('tab'),
            tab_index = $('[pdc-side][tab='+tab_current+']').index();
        if($('#wrap_inlay').attr("tab")!=tab){
            $('#wrap_inlay').attr('tab',tab);
            var zoom = canvas.getZoom();
            CanvasEvents.reset_canvas(tab_index,tab,zoom);
        }
    });
    $('#pdc_search_text').keyup(function(){
       var key = $(this).val().toUpperCase(); 
       $('.pdc_text_list li').each(function(){
            if($(this).text().toUpperCase().indexOf(key)>=0){
                $(this).show();
            }else{
                $(this).hide();
            }
       });
    });
    $('[pdc-box="toolbox"]').hide();
    $('#pdc_text_tag li').click(function(){
        $('#pdc_text_tag li.active').removeClass('active');
        $(this).addClass('active');
        var tag = $(this).attr('tag');
        if(tag=='all_tag'){
            $('.pdc_text_list li').show();
        }else{
            $('.pdc_text_list li').each(function(){
                $(this).hide();
                var tag1 = $(this).attr('tag').replace(/ /g,'').split(','),
                    check = false;
                $.each(tag1, function(index, value) { 
                  if(value==tag){
                    check = true;
                    return false;
                  }
                });
                if(check)$(this).show();
            })
        }
    });
    $('[pdc-color]').click(function(){
        if(!$(this).hasClass('active')){
            $('.active[pdc-color]').removeClass('active');
            $(this).addClass('active');
            $('[pdc-side]').each(function(){
                var side = $(this).attr('tab'),
                    tab_index = $(this).attr('pdc-side'),
                    img = $('.active[pdc-color]').attr(side),
                    img_over = $('.active[pdc-color]').attr('overlay_'+tab_index);
                $(this).attr({'side_img':img,'overlay':img_over}).find('img').attr('src',pdcMediaUrl + img);
                //For some side using color as background
                $(this).attr('side_color', $('.active[pdc-color]').attr('color'));
            })
            var img_act = pdcMediaUrl + $('.active[pdc-side]').attr('side_img');
            //Set overlay image
            canvas.setOverlayImage($("#pdp_media_url").val() + $(".pdp_side_item_content.active").attr("overlay"), function(img){
                canvas.renderAll();
            });
            //Check active side using image or color for background
            var backgroundType = $(".pdp_side_item_content.active").attr("background_type");
            if(backgroundType === "image") {
                canvas.setBackgroundImage(img_act, canvas.renderAll.bind(canvas));
            } else {
                canvas.setBackgroundColor("#" + $('.active[pdc-color]').attr('color'), canvas.renderAll.bind(canvas));
            }
            
        }
    });
    $('.pdc_text_list li').click(function(){
        //$('.pdc_text_list li.active').removeClass('active');
        var text = $(this).text();
        $('[pdc-data="text"]').val(text);
        CanvasEvents.edit_text('text',text);
        //Close text modal
        $("#textLibrary").modal("hide");
    });
    $('div[pdc-action="add-clipart"]').on('click',function(){
       $('.tab_content:not(#add_image, #pdc_rotate_item),  [pdc-box="toolbox"]').slideUp(0);
       $('#add_image').slideToggle(0);
       CanvasEvents.clearSelected();
    });
    $('[pdc-data="text"]').on('keyup',function(){
             CanvasEvents.edit_text('text',$(this).val());
    });
    $('#select_font li').each(function(){
        $(this).css('font-family',$(this).attr('pdc-font'));
    });
    $('#select_font li').click(function(){
        if(!$(this).hasClass('active')){
            $('#select_font li.active').removeClass('active');
            $(this).addClass('active');
            CanvasEvents.edit_text('fontFamily',$(this).attr('pdc-font'));
        }
    })
    $('#font_menu').click(function(){
        //$('#select_font').slideToggle(200);
    });    
    $('[pdc-text-align]').click(function(){
        if(!$(this).hasClass('active')){
            $('.active[pdc-text-align]').removeClass('active');
            $(this).addClass('active');
            var textAlign = $(this).attr('pdc-text-align');
            CanvasEvents.edit_text('textAlign',textAlign);
        }
    })
    $('[pdc-text]').click(function(){
        $(this).toggleClass('active');
        var text_deco = font_weight = font_style = '';
        $('.active[pdc-text]').each(function(){
            var textstyle = $(this).attr('pdc-text');
                if(textstyle == 'underline'){
                    text_deco += ' underline ';
                }
                if(textstyle == 'overline'){
                    text_deco += 'overline ';
                }
                if(textstyle == 'line-through'){
                    text_deco += 'line-through ';
                }
                if(textstyle == 'bold'){
                    font_weight += 'bold';
                }
                if(textstyle == 'italic'){
                    font_style += 'italic';
                }
            CanvasEvents.edit_text('fontStyle',font_style);
            CanvasEvents.edit_text('fontWeight',font_weight);
            CanvasEvents.edit_text('textDecoration',text_deco);
        });
    })
    
    $('#icon_list, #shape_list ,#search_shape_list, #lists_img_upload, #photos_album, #pdc_instagram_list_img').on('click', 'img', function () {
        var url = $(this).attr("src"),
            type_img = url.split('.'),
            wimg = $(this).width(),
            icolor = url.split('/'),
            name = $(this).attr('image_name'),
            id = $(this).attr('id'),
            color_type = $(this).attr('color_type'),
            color = PDC_setting.color_default;
            himg = $(this).height();
        //canvasEvents.clearSelected();
        if(!$(this).hasClass('added_color')){
            $(this).addClass('added_color');
            //if(color_type == 2){ CanvasEvents.makelistcolor(color,id)}
        }
        var price = $(this).attr('price');
        if(price==undefined){
			var pdc_product_config = {};
			pdc_product_config.clipart_price = 0;
			if ($('#pdc_product_config').length) {
				pdc_product_config = JSON.parse($('#pdc_product_config').val());
			}
            price = pdc_product_config.clipart_price;
        }
        $('.tab_content:not(#pdc_rotate_item)').slideUp(200);
        //$('#select_image').slideToggle(600);
        if ((type_img[type_img.length - 1] != 'svg')) {
            fabric.Image.fromURL(url, function (image) {
				//console.log(clipartSize.width, clipartSize.height);
                image.set({
                    //left: 0,
                    //top: 0,
                    angle: 0,
                    price: price,
                    id: id,
                    scaleY: canvas.width / image.width / 2,
                    scaleX: canvas.width / image.width / 2,
                    isrc: url,
                    tcolor: color_type,
                    icolor: icolor[icolor.length - 1]
                });
				//image.scaleToWidth(clipartSize.width);
                image.transparentCorners = true;
                image.cornerSize = 10;
                image.scale(1).setCoords();
                canvas.centerObject(image);
                canvas.add(image).setActiveObject(image);
                CanvasEvents.editItem('move','m_cc');
                //CanvasEvents.addlayer();
                //pdc_history.push(JSON.stringify(canvas));
            });
        } else {
            CanvasEvents.addSvg(url,$(this).attr('price'),name,color_type,color,id);
        }
        //Hide upload modal if using custom upload image
        $("#uploadPhotos").modal("hide");
        //Hide shape modal if using shape plugin
        if($("#shapeLibrary").length) {
            $("#shapeLibrary").modal("hide");
        }
		//Hide instagram modal
		if($("#instagramPhotos").length) {
			$("#instagramPhotos").modal("hide");
		}
    });
//////////////////////////////////////////////Z-index////////////////////////////////////////////////////////////////////////////
    $('.flip_x').on('click', function () { CanvasEvents.editItem('flipX'); });
    $('.flip_y').on('click', function () { CanvasEvents.editItem('flipY'); });
    $('[pdc-action="move_to_back"]').on('click', function () { CanvasEvents.editItem('sendBackwards'); });
    //$('#move_to_back').on('click', function () { CanvasEvents.editItem('sendToBack'); });
    $('[pdc-action="move_to_front"]').on('click', function () { CanvasEvents.editItem('bringForward'); });
    //$('#move_to_front').on('click', function () { CanvasEvents.editItem('bringToFront'); });
    $('#delete_item').on('click', function () { CanvasEvents.editItem('delete'); });
    $('#duplicate_item').on('click', function () { CanvasEvents.editItem('duplicate'); });
    $('#color_fill_list').on('click', 'li', function () {
        if(!$('.color_fill_tool').hasClass('active'))return;
        $('#color_fill_list li.selected').removeClass('selected');
        $(this).addClass('selected');
        var color = $(this).find('a').css('backgroundColor');
        CanvasEvents.editItem('color',color);
        $('.input_picker').val(color);
        $('[pdc-data="color"] div.result').css('backgroundColor', color );
    });
    $('#move_item span').click(function(){
        CanvasEvents.editItem('move',$(this).attr('class'));
    })
//////////////////////////////////////////////Z-index////////////////////////////////////////////////////////////////////////////
    $('html').on('click', function (e) {
        var target = $('#pdc_info_item, #select_image, #add_text, #canvas_area, #textTab, #pdc_block_layer, #pdc_opacity_item, .color_fill_tool, .tools-tab,  .canvas-container,#pdc_toolbox, .edit-tools , #textLibrary, #add_text, #edit_item_wrap').has(e.target).length;
        if (target === 0) {
            //CanvasEvents.clearSelected();
        }
        var font_target = $('#font_menu_wrap').has(e.target).length;
        if (font_target===0){
            //$('#select_font').slideUp(200);
        }
        var cl_target = $('#font_outline_colorpicker, #font_outline_color').has(e.target).length;
        if (cl_target===0){
            $('#font_outline_colorpicker').slideUp(200);
        }
        var cl_target = $('#font_color_colorpicker, #font_color, .pdc_color_list').has(e.target).length;
        if (cl_target===0){
            $('#font_color_colorpicker').slideUp(200);
        }
    });
    $(window).on('keydown', function (e) {
        var key = e.keyCode || e.which;
        if (key == 37) { // left arrow
            CanvasEvents.moveObject('left');
            return false;
        } else if (key == 38) { // up arrow
            CanvasEvents.moveObject('up');
            return false;
        } else if (key == 39) { // right arrow
            CanvasEvents.moveObject('right');
            return false;
        } else if (key == 40) { // down arrow
            CanvasEvents.moveObject('down');
            return false;
        } else if (key == 46) { // delete key
            var hasFocus = $('[pdc-data="text"]').is(':focus');
            if(!hasFocus){
                CanvasEvents.removeObject();
                PDC_layer.load_layer();
            }
            return false;
        }
    });
})