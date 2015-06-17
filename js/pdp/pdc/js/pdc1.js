//Load main actions and main setting. We can load more module in other file.
var pdc1 = jQuery.noConflict();
pdc1(document).ready(function ($) {
///////////////////////////Setting////////////////////////
var m = $('#url_site').val().replace('index.php/',''), 
    pdpMediaUrl = $("#pdp_media_url").val(), //Fix store code show in url
    pdpShapeMediaUrl = pdpMediaUrl.replace("/images/", "/shapes/"), //Fix store code show in url
    baseUrl = $("#base_url").val(),
    check_drag = true,
	//Set more time if final image errors
	defaultRenderTime = 1000,
    currencySym = $("#currency_symbol").val(),
	allSidePanel = {},
	mainWindow = top.document,
    jcropApi,
	renderTime;
    canvas  = new fabric.Canvas('canvas_area', { });
    pdc_history = [],
    pdc_product_config = {};
	if ($('#pdc_product_config').length) {
		pdc_product_config = JSON.parse($('#pdc_product_config').val());
	}
// Main object
var pdc = PDC();
    
/////////////Setting default info///////////////
    PDC_setting = {
       color_default: '#FF0000',
       font_family_default: "Arial",
       font_size_default: '20',
       div_side_action : $('#pdc_sides'),
       line_height_default: '1.3'
    }
/////////////Setting default info///////////////


    ///// All events in design/////////
    CanvasEvents = {
       rgb2hex: function(rgb) {
            var check = rgb.split('(');
            if(check[0]=='rgb'){
                var hexDigits = ["0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f"];
                rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
                function hex(x) {
                    return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
                }
                return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
            }else{
                return rgb;
            }
        },
        scaleSize: function(maxW, maxH, currW, currH){
			var ratio = currH / currW;
			if(currW >= maxW && ratio <= 1){
				currW = maxW;
				currH = currW * ratio;
				//To show object controls inside canvas
				currH *= 0.9;
				currW *= 0.9;
			} else if( currH >= maxH){
				currH = maxH;
				currW = currH / ratio;
				currH *= 0.9;
				currW *= 0.9;
			}
			return {
				width: currW,
				height: currH
			};
		},
        hexToRgb: function(hex) {
            var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
            hex = hex.replace(shorthandRegex, function(m, r, g, b) {
                return r + r + g + g + b + b;
            });
        
            var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : null;
        },
        hexFromRGB: function(r, g, b) {
            var hex = [
                r.toString( 16 ),
                g.toString( 16 ),
                b.toString( 16 )
            ];
            $.each( hex, function( nr, val ) {
                if ( val.length === 1 ) {
                    hex[ nr ] = "0" + val;
                }
            });
            return hex.join( "" ).toUpperCase();
        },
        refreshColor: function() {
            var red = $("#pdc_red" ).slider( "value" ),
            green = $("#pdc_green" ).slider( "value" ),
            blue = $("#pdc_blue" ).slider( "value" ),
            hex = CanvasEvents.hexFromRGB( red, green, blue );
            $( "#pdc_color_result" ).css( "background-color", "#" + hex );
            CanvasEvents.editItem('color',hex);
        },
        objectSelected: function (e) {
            $('[pdc-block="layer"] li.active').removeClass('active');
            if(e.target.get('type')!='group'){
                $('[pdc-box="toolbox"]').addClass('active');
                var activeObject = canvas.getActiveObject();
                console.log(activeObject.type);
                CanvasEvents.init_opacity(activeObject);
                if (activeObject.type =='text') {
                    CanvasEvents.init_text(activeObject);
                    CanvasEvents.edit_text('Text',activeObject.text);
                }
                if (activeObject.type =='image') {
                    CanvasEvents.edit_img(activeObject,'image');
                }
                if (activeObject.type =='path-group') {
                    CanvasEvents.edit_img(activeObject);
                }
                if((activeObject.type =='text')||(activeObject.type =='path-group')){
                    $('.color_fill_tool').addClass('active');
                }else{
                    $('.color_fill_tool').removeClass('active');
                }
                var name = activeObject.name;
                $('[pdc-block="layer"] li[name="'+name+'"]').addClass('active');
            }else{
                $('[pdc-box="toolbox"]').removeClass('active');
            }
            $('#add_image, #add_text').slideUp();
            //
            //canvasEvents.showinfo();
            //console.log(activeObject.type);
            
        },
        editItem: function(task,value){ 
            var active = canvas.getActiveObject();
            if (!active) return;
            switch (task) {
                case 'sendBackwards' : canvas.sendBackwards(active); break;
                case 'sendToBack' : canvas.sendToBack(active); break;
                case 'bringForward' : canvas.bringForward(active); break;
                case 'bringToFront' : canvas.bringToFront(active); break;
                case 'flipX'    : active.flipX = active.flipX ? false : true; break;
                case 'flipY'    : active.flipY = active.flipY ? false : true; break;
                case 'delete'   : canvas.remove(active); break;
                case 'duplicate': this.copyObject(); break;
                case 'color'    :   console.log(CanvasEvents.rgb2hex(value));   active.set('fill',CanvasEvents.rgb2hex(value));break;
                case 'opacity'  :   active.set('opacity',value);    break; 
                case 'move': 
                    var zoom = canvas.getZoom(),
                        w_canvas =   canvas.width/zoom,
                        h_canvas =   canvas.height/zoom,
                        w_obj =   active.getWidth(),
                        h_obj =   active.getHeight();
                    switch (value) {
                        case 'm_tl': active.set({"left": 1,"top": 1}); break;  //ok
                        case 'm_tc': active.set({"left": w_canvas/2 - w_obj/2 - 1,"top": 1}); break;
                        case 'm_tr': active.set({"left": parseInt(w_canvas) - parseInt(w_obj) + 1,"top": 1}); break;
                        case 'm_cl': active.set({"left": 1,"top":h_canvas/2-h_obj/2});  break; //ok
                        case 'm_cc': active.set({"top": h_canvas/2-h_obj/2-1,"left":w_canvas/2 - w_obj/2 - 1}); break; //ok
                        case 'm_cr': active.set({"left": w_canvas - w_obj - 1,"top":h_canvas/2-h_obj/2}); break;
                        case 'm_bl': active.set({"left": 1,"top": h_canvas - h_obj - 1}); break;
                        case 'm_bc': active.set({"top": h_canvas - h_obj - 1,"left":w_canvas/2 - w_obj/2 - 1}); break;
                        case 'm_br': active.set({"left": w_canvas - w_obj + 1,"top": h_canvas - h_obj - 1}); break;
                    }
                    //canvas.centerObjectH(active);
                    //canvas.centerObjectV(active);
                    break;
            }      
            active.setCoords();
            canvas.renderAll();
        },
        copyObject: function () {
            if(!$('[pdc-box="toolbox"]').hasClass('active')) return;
            var active = canvas.getActiveObject();
            if (!active) return;
            if (fabric.util.getKlass(active.type).async) {
                active.clone(function (clone) {
                    clone.set({
                        'isrc': active.isrc,
                        'price': active.price,
                        'name': 'item_'+parseInt($('#pdp_toolbox tr.pdp2_layer_item').length)+1,
                        transparentCorners: true,
                        //cornerColor: setting.border
                    })
                    canvas.add(clone);
                });
            } else {
                canvas.add(active.clone().set({
                    transparentCorners: true,
                    //cornerColor: setting.border
                }));
            }
            //canvasEvents.addlayer();
            canvas.renderAll();
        },
        reset_canvas: function(tab_index,tab,zoom){
            var item_act = $('[pdc-side][tab="'+tab+'"]'),
                item_img,
                tab_index_new = $('[pdc-side][tab="'+tab+'"]').index(),
                item_inlay;
            if(item_act.length > 0){
                item_img =  pdpMediaUrl +item_act.attr('side_img');
                //$('#main_image').attr("src",item_img);
                item_inlay = item_act.attr('inlay').split(',');
                pdc_history[tab_index] = JSON.stringify(canvas.toJSON(['name','price','tcolor','isrc','icolor','id']));
                PDC_Actions.setup_designarea(item_img,item_inlay[0],item_inlay[1],tab_index_new, zoom);
            }
        },
        edit_text: function(act,val){
              var active = canvas.getActiveObject();
              if (!active){
                if(act=='text'){
                   CanvasEvents.addText(val); 
                }
                return;
              }else{
                if(active.type!='text'){
                    if(act=='text'){
                       canvas.deactivateAll().renderAll(); 
                       CanvasEvents.addText(val); 
                    }
                }
              }
              $('.color_fill_tool, .opacity_tool').removeClass('expanded');
              $('#ColorFillBtn, #opacityBtn').removeClass('selected');
              $('[pdc-box="toolbox"]').show();
              active.set(act,val);
              canvas.renderAll();
        },
        edit_img: function(obj,type){
            if(!$('[pdc-box="toolbox"]').hasClass('active')) return;
            $('#pdc_toolbox .tools_text').hide();
          $('[pdc-data="text"]').val('');
          $('.color_fill_tool, .opacity_tool').removeClass('expanded');
          $('#ColorFillBtn, #opacityBtn').removeClass('selected');
          if(type=='image'){
                $('#ColorFillBtn').hide();
          }else{
                $('#ColorFillBtn').show();
          }
          CanvasEvents.init_color_item(obj);
        },
        init_opacity : function(obj){
            if(!$('[pdc-box="toolbox"]').hasClass('active')) return;
            if(obj){
                
                var opacity = obj.get('opacity');
            	$('#opacitySlider').val(opacity*100);
    
                  /*$( "#pdc_opacity_input" ).slider({
                    range: false,
                    min: 0,
                    max: 100,
                    value: opacity*100,
                    slide: function( event, ui ) {
                        obj.set("opacity",ui.value/100);
                        canvas.renderAll();
                        //$( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
                    }                
                }); */
            }
        },
        init_text: function (obj){
            if(!$('[pdc-box="toolbox"]').hasClass('active')) return;
            if(obj){
                $('[pdc-data="text"]').val(obj.text);
                /*
                var font_size = obj.get('fontSize');
                  $( "#pdc_font_size_input" ).slider({
                    range: false,
                    min: 0,
                    max: 100,
                    value: font_size,
                    slide: function( event, ui ) {
                        obj.set("fontSize",ui.value);
                        $('#pdc_font_size_value').html(ui.value+'px');
                        canvas.renderAll();
                        //$( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
                    }                
                }); 
                var lineHeight = obj.get('lineHeight');
                  $( "#pdc_edit_text_line_height_input" ).slider({
                    range: false,
                    min: -10,
                    max: 10,
                    step: .1,
                    value: lineHeight,
                    slide: function( event, ui ) {
                        obj.set("lineHeight",ui.value);
                        $('#pdc_font_line_height_value').html(ui.value);
                        canvas.renderAll();
                        //$( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
                    }             
                }); */
                $('.active[pdc-text], .active[pdc-text-align]').removeClass('active');
                if(obj.get('fontWeight')=='bold'){
                    $('[pdc-text="bold"]').addClass('active');    
                }
                if(obj.get('fontStyle')=='italic'){
                    $('[pdc-text="italic"]').addClass('active');    
                }
                var text_deco = obj.get('textDecoration');
                if (text_deco.indexOf('underline') > 0) { 
                    $('[pdc-text="underline"]').addClass("active");
                }
                if (text_deco.indexOf('overline') > 0) {
                    $('[pdc-text="overline"]').addClass("active");
                }
                if (text_deco.indexOf('line-through') > 0) {
                    $('[pdc-text="line-through"]').addClass("active");
                }
                var text_align = obj.get('textAlign');
                if(text_align=='right'){
                    $('[pdc-text-align="right"]').addClass("active");
                }else{
                    if(text_align=='center'){
                        $('[pdc-text-align="center"]').addClass("active");
                    }else{
                        $('[pdc-text-align="left"]').addClass("active");
                    }
                }
                CanvasEvents.init_color_item(obj);
            }
        },
        init_color_item: function(obj){
            var color = obj.get('fill'); 
            $('.color_fill_tool').addClass('active');
            if((color=='')||(color==undefined)||(color=='rgb(0,0,0)')) { $('#tools_color').hide(); return; }
            $('#ColorFillBtn').show();
            var rgb_color = CanvasEvents.hexToRgb(color);
            $("#pRed").val(rgb_color.r);
            $("#pGreen").val(rgb_color.g);
            $("#pBlue").val(rgb_color.b);
            $('[pdc-data="color"] .result').css('background-color',color);
            $('[pdc-data="color"]').ColorPicker({
            	color: color,
            	onShow: function (colpkr) {
            		$(colpkr).fadeIn(500);
            		return false;
            	},
            	onHide: function (colpkr) {
            		$(colpkr).fadeOut(500);
            		return false;
            	},
            	onChange: function (hsb, hex, rgb) {
            		$('[pdc-data="color"] div.result').css('backgroundColor', '#' + hex);
                    CanvasEvents.editItem('color','#' +hex);
                    $('.input_picker').val('#' +hex);
            	}
            });
            
            
            $( "#pdc_red" ).slider({
                orientation: "horizontal",
                range: "min",
                max: 255,
                value: rgb_color.r,
                slide: function(event,ui){
                    CanvasEvents.refreshColor()
                }
            });
            $( "#pdc_green" ).slider({
                orientation: "horizontal",
                range: "min",
                max: 255,
                value: rgb_color.g,
                slide: function(event,ui){
                    CanvasEvents.refreshColor()
                }
            });
            $( "#pdc_blue" ).slider({
                orientation: "horizontal",
                range: "min",
                max: 255,
                value: rgb_color.b,
                slide: function(event,ui){
                    CanvasEvents.refreshColor()
                }
            });
            $('input[name="color_filling"]').val(color);
        },
        renderall: function(){
            canvas.selection = false;
            //canvasEvents.save_history();
            //canvasEvents.save_design2();
            canvas.calcOffset().renderAll();
			//canvasEvents.addlayer();
        },
        activeobj: function(el){
            var objects = canvas.getObjects();
            if((el!='')&&(el!=undefined)){
                for (var i = 0; i < objects.length; i++) {
                    if(objects[i].name==el){
                        canvas.setActiveObject(objects[i]);
                    }
                }
            }
        },
        addText: function (text) {
            $('#add_text').slideUp(300);
            $('#add_text_input').val('');
            //$('#edit_text').css('opacity',"1");
            //$('#color_item').css('opacity',.5);
            var center = canvas.getCenter(),
                textObj = new fabric.Text(text, {
                    fontFamily: PDC_setting.font_family_default,
                    //left: center.left,
                    //top: center.top,
                    fontSize: PDC_setting.font_size_default,
                    textAlign: "left",
                    //perPixelTargetFind : true,
                    fill: PDC_setting.color_default,
                    price: pdc_product_config.text_price,
                    //name: 'item_'+pos_item,
                    lineHeight: PDC_setting.line_height_default,
                    fontStyle: "", //"", "normal", "italic" or "oblique"
                    fontWeight: "normal", //bold, normal, 400, 600, 800
                    textDecoration: "", //"", "underline", "overline" or "line-through"
                    shadow: '', //2px 2px 2px #fff
                    //padding: setting.padding
                });
            textObj.lockUniScaling = false;
            textObj.hasRotatingPoint = true;
            textObj.transparentCorners = true;
            //textObj.cornerColor = setting.border;
            canvas.centerObject(textObj);
            canvas.add(textObj).setActiveObject(textObj);
            canvas.calcOffset().renderAll();
            CanvasEvents.editItem('move','m_cc');
            //canvasEvents.center();
            //canvasEvents.addlayer();
            //$('#use_shadow').click();
            //canvasEvents.renderall();
        },
        addSvg: function (el,pr,name,tcolor,icolor,id) {
            fabric.loadSVGFromURL(el, function (objects, options) {
                var loadedObject = fabric.util.groupSVGElements(objects, options),
                    center = canvas.getCenter(),
                    zoom = canvas.getZoom(),
                    pos_item = parseInt($('#pdc_info_item').attr('critem'));
                $('#pdc_info_item').attr('critem',pos_item+1);
                if((pr =='')||((pr ==undefined))){pr=0;}
                if((name =='')||((name ==undefined))){name='not_add';}
                loadedObject.set({
                    //left: center.left,
                    //top: center.top,
                    fill: icolor,
                    perPixelTargetFind : true,
                    isrc: el,
                    price: pr,
                    tcolor: tcolor,
                    icolor: icolor,
                    name: name,
                    id: id,
                    //scaleY: canvas.height / loadedObject.height / 2,
                    scaleY: (canvas.width / loadedObject.width / 2)/zoom,
                    scaleX: (canvas.width / loadedObject.width / 2)/zoom,
                    //fill: setting.color,
                    transparentCorners: true,
                    //padding: setting.padding
                });
				if (loadedObject.width > canvas.width) {
					loadedObject.scaleToWidth(canvas.width - 20);
				}
                //if((loadedObject.path=='null')||((loadedObject.path==null))){
                    //loadedObject.set({icolor: 'svg'})
                //}
                loadedObject.setCoords();
                canvas.centerObject(loadedObject);
                loadedObject.hasRotatingPoint = true;
                //loadedObject.lockUniScaling = true;
                canvas.add(loadedObject).setActiveObject(loadedObject).centerObject(loadedObject);
                CanvasEvents.editItem('move','m_cc');
                //canvasEvents.center();
                //console.log(canvas.toJSON(['name','price']));
                //canvasEvents.addlayer();
            });
        },
        clearSelected: function () {
            this.objectUnselected();
            canvas.deactivateAll().renderAll();
            //canvasEvents.renderall();
        },
        objectUnselected: function(){
            CanvasEvents.resetTextBox();
            $('.color_fill_tool').removeClass('active');
            $('[pdc-box="toolbox"]').removeClass('active');
        },
        removeObject: function () {
            var active = canvas.getActiveObject();
            if (active) {
                $('#pdc_info_item li[rel="'+active.name+'"]').remove();
                canvas.remove(active);
                //canvasEvents.renderall();
            }
            CanvasEvents.resetTextBox();
        },
        resetTextBox: function(){
          $('.active[pdc-text], .active[pdc-text-align]').removeClass('active');
          $('[pdc-data="text"]').val('') ;
        },
        moveObject: function (direction) {
            var active = canvas.getActiveObject();
            if (active) {
                if (direction == 'up') {
                    active.setTop(active.getTop() - 1).setCoords();
                    //canvasEvents.renderall();
                } else if (direction == 'down') {
                    active.setTop(active.getTop() + 1).setCoords();
                    //canvasEvents.renderall();
                } else if (direction == 'left') {
                    active.setLeft(active.getLeft() - 1).setCoords();
                    //canvasEvents.renderall();
                } else if (direction == 'right') {
                    active.setLeft(active.getLeft() + 1).setCoords();
                    //canvasEvents.renderall();
                }
            }
        },
        restore_design: function (objs) {
            var json = JSON.parse(objs);
            var objects = json.objects;
            canvas.clear();
            $('#pdc_info_item').attr('critem',objects.length);
             //Check sample has text or not, if has text, then make sure font loaded before render
            var isSampleHasText = false;
            objects.forEach(function(o) {
                if (o.type == "text") {
                    isSampleHasText = true;
                }
                if(o.type == "text") {
                    pdc.updateImagePathBeforeAdd(o);
                }
            });
            if($("#is_backend").length && !isSampleHasText) {
                fabric.util.enlivenObjects(objects, function(objects) {
                    var origRenderOnAddRemove = canvas.renderOnAddRemove;
                    canvas.renderOnAddRemove = false;
                    objects.forEach(function(o) {
                        canvas.add(o);
                    });
                    canvas.renderOnAddRemove = origRenderOnAddRemove;
                    canvas.renderAll();
                });
            } else {
                setTimeout(function() {
                    //Need a flag here
                    if (pdc.firstLoadFlag) {
                        pdc.firstLoadFlag = false;
                        setTimeout(function() {
                            //canvas.remove(loadingText);
                            pdc.showLog("Render inside setTimout of another setTimout, make sure font loaded first time", "info");
                            canvas.renderAll();
                        }, 200);
                    }
                    fabric.util.enlivenObjects(objects, function(objects) {
                        var origRenderOnAddRemove = canvas.renderOnAddRemove;
                        canvas.renderOnAddRemove = false;
                        objects.forEach(function(o) {
                            canvas.add(o);
                        });
                        canvas.renderOnAddRemove = origRenderOnAddRemove;
                        canvas.renderAll();
                    });
                }, 100);
            }
        }
    }
///////////////////////////////Action in product magento + themes ///////////
    PDC_Actions = {
        ini_design : function(){
            if($('[pdc-side]').length > 0){
                var main_img = PDC_setting.div_side_action.find('li:eq(0) img.pdp-side-img').attr('src'),
                inlay = PDC_setting.div_side_action.find('li:eq(0)').attr('inlay');
                if((inlay!='')&&(inlay!=undefined)){
                    inlay = inlay.split(',');
                    $('#wrap_inlay').attr('tab',$('[pdc-side]:eq(0)').attr('tab'));
                    PDC_Actions.setup_designarea(main_img,inlay[0],inlay[1],0);
                }
                //$('#wrap_inlay').prepend('<img src="'+main_img+'" id="pdc_main_img" />');
            }
            $('.edit-tools').show();
        },
        init_drag: function(){
            if(check_drag){
                $("#add_text").draggable({ handle: "label" });
                $("#add_image").draggable({ handle: "label" });
                $("#pdc_toolbox").draggable({ handle: "label.pdc_label_drag" });
            }
        }(),
        init_first_design: function (){
            
        },
        addlayer_first_time: function(){
            var html, final_pr = 0;
            //PDPsetting.
            $('[pdc-side]').each(function(){
                var json = JSON.parse(pdc_history[$(this).index()]);
                //var json = canvas.toJSON(['name','price','tcolor','isrc','icolor','id']);
                var objects = json.objects, price = 0, side_act = $(this).attr('tab');
                $('.layer_'+side_act+' .layer_pricing tbody').html('');
                //json = JSON.parse(JSON.stringify(canvas.toJSON()));
                //objects = json.objects;
                //canvasEvents.load_json(pdc_history[$('#pdc_sides li.active').index()]);
                for (var i = 0; i < objects.length; i++) {
                    //objects[i].price = 32;
                    objects[i].left = 0;
                    objects[i].top = 0;
                    objects[i].angle = 0;
                    if((objects[i].name!=undefined)&&(objects[i].name!='null')){
                        item_name = objects[i].name;
                    }else{
                        item_name = 'item_'+side_act+i;
                    }
                    $('#pdp2_canvas_layer_cv').attr({
                        'width' : objects[i].width*objects[i].scaleX,
                        'height': objects[i].height*objects[i].scaleY
                    });
                    var pdp2_canvas_layer_cv = new fabric.Canvas('pdp2_canvas_layer_cv', {opacity: 1});
                        pdp2_canvas_layer_cv.clear();
                    var klass = fabric.util.getKlass(objects[i].type);
                    if (klass.async) {
                        klass.fromObject(objects[i], function (img) {
                            pdp2_canvas_layer_cv.add(img);
                        });
                    } else {
                        pdp2_canvas_layer_cv.add(klass.fromObject(objects[i]));
                    }
                    if($('#pdc_product_config').length > 0){
                        var pdc_product_config = JSON.parse($('#pdc_product_config').val());
                        if(objects[i].text){
                            if(pdc_product_config.text_price == ''){pdc_product_config.text_price = 0;}
                            if((objects[i].price == undefined)) {objects[i].price = pdc_product_config.text_price;}
                        }else{
                            if(pdc_product_config.clipart_price == ''){pdc_product_config.clipart_price = 0;}
                            if((objects[i].price == undefined)) {objects[i].price = pdc_product_config.clipart_price;}
                        }
                    }else{
                        if(objects[i].price == undefined) {objects[i].price = 0;}
                    }
                    final_pr+=parseFloat(objects[i].price);
                    price +=parseFloat(objects[i].price);
                    if((objects[i].type=='path')||(objects[i].type=='path-group')||(objects[i].type=='image')){ 
                        html = '<tr class="pdp2_layer_item" rel="'+item_name+'">'+
                            '<td class="item_type">'+parseInt(i+1)+'</td>'+
                            '<td class="item_info"><img src="'+pdp2_canvas_layer_cv.toDataURL('png')+'"/></td>'+
                            //'<td class="item_size">'+parseInt(objects[i].width)+' x '+parseInt(objects[i].height)+'</td>'+
                            '<td class="item_price">'+ currencySym + parseFloat(objects[i].price).toFixed(2) +'</td>'+
                            '<td><a class="item_delete" title="Remove"><i class="pi pi-trash-o"></i></a></td></tr>'
                        ;
                    }
                    if(objects[i].type=='text'){
                        html = '<tr class="pdp2_layer_item" rel="'+item_name+'">'+
                            '<td class="item_type">'+parseInt(i+1)+'</td>'+
                            '<td class="item_info">'+objects[i].text.substring(0,15)+'...</td>'+
                            //'<td class="item_size">'+parseInt(objects[i].width)+' x '+parseInt(objects[i].height)+'</td>'+
                            '<td class="item_price">'+ currencySym + parseFloat(objects[i].price).toFixed(2) +'</td>'+
                            '<td><a class="item_delete" title="Remove"><i class="pi pi-trash-o"></i></a></td></tr>'
                        ;
                    }   
                    $('.layer_'+side_act+' tbody').append(html).parents('.layer_pricing').attr('pr',price);
                }
            });
            $('#pdc_total_layer_price tfoot tr th:eq(2)').html(currencySym + final_pr.toFixed(2));
            $('.item_delete').parent().remove();
            var side_act = $('#pdc_sides li.active').attr('tab');
            //$('.prices prices_layer[tab="'+side_act+'"] label').click();
            //var active = canvas.getActiveObject();
            //if(active){$('#pdc_info_item ul li[rel="'+active.name+'"]').addClass('active')}
        },
        addlayer: function(){
			//Check if price tab (layer tab) enable or not, PERFORMANCE EFFECT
			if($('#pdc_product_config').length) {
				var pdc_product_config = JSON.parse($('#pdc_product_config').val());
				if(pdc_product_config.show_price == 2) {
					return false;
				}
			}
            var html, final_pr = 0;
            var json = JSON.parse(pdc_history[$('#pdc_sides li.active').index()]);
            //var json = canvas.toJSON(['name','price','tcolor','isrc','icolor','id']);
            var objects = json.objects, side_act = $('#pdc_sides li.active').attr('tab');
            $('.layer_'+side_act+' .layer_pricing tbody').html('');
            $('.layer_'+side_act+' tbody').append(html).parents('.layer_pricing').attr('pr',final_pr);
			//console.log("Add layer called" + objects.length);
			//return;
            for (var i = 0; i < objects.length; i++) {
                //objects[i].price = 32;
                objects[i].left = 0;
                objects[i].top = 0;
                objects[i].angle = 0;
                if(objects[i].name!=undefined){
                    item_name = objects[i].name;
                }else{
                    item_name = 'item_'+i;
                }
                $('#pdp2_canvas_layer_cv').attr({
                    'width' : objects[i].width*objects[i].scaleX,
                    'height': objects[i].height*objects[i].scaleY
                });
               
                if(pdc_product_config !== undefined){
                    //var pdc_product_config = JSON.parse($('#pdc_product_config').val());
                    if(objects[i].text){
                        if(pdc_product_config.text_price == ''){pdc_product_config.text_price = 0;}
                        if((objects[i].price == undefined)) {objects[i].price = pdc_product_config.text_price;}
                    }else{
                        if(pdc_product_config.clipart_price == ''){pdc_product_config.clipart_price = 0;}
                        if((objects[i].price == undefined)) {objects[i].price = pdc_product_config.clipart_price;}
                    }
                }else{
                    if(objects[i].price == undefined) {objects[i].price = 0;}
                }
                final_pr+=parseFloat(objects[i].price);
                if(objects[i].type=='image' || (objects[i].type=='path') || (objects[i].type=='path-group')){ 
                    html = '<tr class="pdp2_layer_item" rel="'+item_name+'">'+
                        '<td class="item_type">'+parseInt(i+1)+'</td>'+
                        '<td class="item_info"><img src="'+objects[i].isrc+'"/></td>'+
                        //'<td class="item_size">'+parseInt(objects[i].width)+' x '+parseInt(objects[i].height)+'</td>'+
                        '<td class="item_price">'+ currencySym + parseFloat(objects[i].price).toFixed(2) +'</td>'+
                        '<td><a class="item_delete" title="Remove"><i class="pi pi-trash-o"></i></a></td></tr>'
                    ;
                }
                if(objects[i].type=='text'){
                    html = '<tr class="pdp2_layer_item" rel="'+item_name+'">'+
                        '<td class="item_type">'+parseInt(i+1)+'</td>'+
                        '<td class="item_info">'+objects[i].text.substring(0,15)+'...</td>'+
                        //'<td class="item_size">'+parseInt(objects[i].width)+' x '+parseInt(objects[i].height)+'</td>'+
                        '<td class="item_price">'+ currencySym + parseFloat(objects[i].price).toFixed(2) +'</td>'+
                        '<td><a class="item_delete" title="Remove"><i class="pi pi-trash-o"></i></a></td></tr>'
                    ;
                }
                $('.layer_'+side_act+' tbody').append(html).parents('.layer_pricing').attr('pr',final_pr);
            }
            //canvasEvents.update_total_price_layer();
            //var active = canvas.getActiveObject();
            //if(active){$('#pdc_info_item ul li[rel="'+active.name+'"]').addClass('active')}
        },
        setup_designarea : function(img,w,h,index_view,zoom){
            pdc.showLog("setup_designarea in pdc1.js", "info");
            $('#wrap_inlay').html('<canvas id="canvas_area"></canvas>');
            var main_w = parseInt($('#pdc_main_design_area').width());
            $('#wrap_inlay').css({
               'width'  :   w+'px', 
               'height' :   h+'px',
               'top'    :   0, 
               'left'   :   0, 
               //'margin-left':   (main_w-w)/2+'px'
            });
            $('#canvas_area').attr({
                'width' :   w,
                'height':   h
            });
            canvas = new fabric.Canvas('canvas_area', { });
            //Check current side using colorBackground or imageBackground
            if($(".pdp_side_item_content.active").attr("background_type") == "image") {
                canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));    
            } else {
                //Color can use color_code of side
                //If use color in swap color list, then using them instead (side_color will added to pdp_side_item_content.active when click side color)
                //color from current json first load
                var sideColorInJson = pdc.sides[pdc.getActiveSideIndex()] ? pdc.sides[pdc.getActiveSideIndex()].side_color : "";
                if(sideColorInJson !== "") {
                    $(".pdp_side_item_content.active").attr("side_color", sideColorInJson);
                }
                var backgroundColor = $(".pdp_side_item_content.active").attr("side_color") || $(".pdp_side_item_content.active").attr("color_code") || 'fff';
                canvas.setBackgroundColor("#" + backgroundColor.replace("#", ""), canvas.renderAll.bind(canvas));
            }
            
			//Setup overlay image
			canvas.setOverlayImage($("#pdp_media_url").val() + $(".pdp_side_item_content.active").attr("overlay"), function(img){
				canvas.renderAll();
			});
			canvas.controlsAboveOverlay = true;
            canvas.clear();
            canvas.observe('object:selected', CanvasEvents.objectSelected);
            canvas.observe('before:selection:cleared', CanvasEvents.objectUnselected);
            PDC_layer.init();
            if(pdc_history.length==0){
                $('[pdc-side]').each(function(){
                    pdc_history[$(this).index()] = JSON.stringify(canvas.toJSON(['name','price','tcolor','isrc','icolor','id']));
                })
            }
            var _currentJson = pdc.sides[index_view] ? pdc.sides[index_view].json : pdc_history[index_view];
            //pdc.showLog(_currentJson);
            pdc.showLog("Restore design in pdc1", "info");
            //pdc.showLog(_currentJson);
            CanvasEvents.restore_design(_currentJson);
            //CanvasEvents.restore_design(pdc_history[index_view]);
            if((zoom==undefined)||(zoom=='')) zoom = 1;
            canvas.setZoom(zoom); 
            var new_w = w*(zoom); 
            var new_h = h*(zoom); 
            canvas.setWidth(new_w); 
            canvas.setHeight(new_h);
            canvas.renderAll();
            $('#wrap_inlay').css({'width':new_w,'height':new_h});
            /*
            
            canvas.observe('object:modified', canvasEvents.renderall);
            canvas.observe('object:modified', canvasEvents.showinfo);
            canvas.observe('object:added', canvasEvents.renderall);
            
            canvasEvents.load_json(pdc_history[tab_index]);
            canvasEvents.centerCanvas();
            canvasEvents.renderall();
            */
            this.auto_zoom(w, h);
            this.init_zoom(w, h, zoom);
        },
        auto_zoom: function(w, h){
            //if enable auto zoom//////
            var max_w = parseInt($('.pdc-main.design-area').width()),
                w_canvas = parseInt($('#wrap_inlay').width()),
                zoom = 1;
                max_w = 500;
            if(w_canvas > max_w) {
                zoom  = parseFloat(max_w/w_canvas);
                canvas.setZoom(zoom);
                var new_w = w*(zoom); 
                var new_h = h*(zoom); 
                canvas.setWidth(new_w); 
                canvas.setHeight(new_h);
                canvas.renderAll();
                $('#wrap_inlay').css({'width':new_w,'height':new_h});
            }
        },
        init_zoom : function (w_ori,h_ori, zoom) {
            ////////////////////////////////////Zoom takes easy/////////////////////////////////////////////
           var  zoom_step = 50,
                zoom_cr = zoom;
            $('[pdc-zoom]').click(function(){
                var zoom_cr = canvas.getZoom(),
                    w_cr = canvas.getWidth(),
                    h_cr = canvas.getHeight();
                switch ($(this).attr('pdc-zoom')) {
                    case 'in' : 
                        var new_w = parseInt(w_cr) + zoom_step; 
                        var new_h = h_ori*(new_w/w_ori); 
                        if(parseInt(new_w) > 1200)return; 
                        canvas.setZoom(new_w/w_ori);  
                        canvas.setWidth(new_w); 
                        canvas.setHeight(new_h);   
                        $('#wrap_inlay').css({'width':new_w,'height':new_h});
                        break;
                    case 'out' : 
                        var new_w = w_cr - zoom_step; 
                        var new_h = h_ori*(new_w/w_ori); 
                        if(parseInt(new_w) < 200)return;
                        canvas.setZoom(new_w/w_ori);  
                        canvas.setWidth(new_w); 
                        canvas.setHeight(new_h); 
                        $('#wrap_inlay').css({'width':new_w,'height':new_h});
                        break;
                    case 'reset' : canvas.setZoom(1); canvas.setWidth(w_ori); canvas.setHeight(h_ori); 
                        $('#wrap_inlay').css({'width':w_ori,'height':h_ori}); PDC_Actions.auto_zoom(w_ori, h_ori); break;
                }
            });
            ////////////////////////////////////Zoom takes easy/////////////////////////////////////////////
        }
    }
    
    Clipart = {
		init : function() {
			this.loadMoreImage();
            this.loadMoreShape();
			this.filterByCategory();
            this.filterShapeByCategory();
            this.searchShapeEvent();
		},
        showLoading : function() {
            if(window.Pace !== undefined) {
                Pace.restart();
            }
        },
        itemPerRow : 5,
		loadMoreImage : function() {
			$("[pdc-action='load-more-clipart']").click(function() {
                var selectedOption = $("[pdc-action='change-clipart-category'] option:selected");
				//var currentPage = $("#current_page").val();
                var currentPage = selectedOption.attr("cr_act");
				//var category = Clipart.getActiveCategory();
                var category = selectedOption.val();
				var pageSize = $("#default_page_size").val();
				$.ajax({
					type : "POST",
					url : baseUrl + "pdp/index/loadMoreImage",
					data : {
                        current_page : currentPage, 
                        category : category, 
                        page_size : pageSize
                    },
					beforeSend : function() {
						$('.content_designs .loading-img').show();
                        Clipart.showLoading();
					},
					error : function() {
					
					}, 
					success : function(response) {
						if (response != "nomore") {
							//Increment current page by 1
							//$("#current_page").val(parseInt(currentPage) + 1);
                            selectedOption.attr("cr_act",parseInt(currentPage) + 1);
							var data = $.parseJSON(response);
							var item = "", colorImages;
							for (var i = 0; i < data.length; i++) {
								colorImages = data[i].color_img;
								if (colorImages != "") {
									colorImages = "fff__" + data[i].filename + "," + data[i].color_img;
								}
								item += "<li cat='"+ data[i].category +"'> <a class='selection_img' rel='clover'><img color='"+ colorImages +"' src='" + 
								pdpMediaUrl + 'artworks/' + data[i].filename +"' id='img" + data[i].image_id + "' price='"+data[i].price+"' image_name='"+data[i].image_name+"' color_type='"+data[i].color_type+"'/></a> </li>";
							}
							$("#icon_list").append(item);
							Clipart.showOrHideLoadMoreBtn(data.length);
						} else {
							$("[pdc-action='load-more-clipart']").hide();
                            //alert('No more items to load!');
						}
						$('.content_designs .loading-img').hide();
					}
				});
			});
		},
        loadMoreShape : function() {
			$("[pdc-action='load-more-shape']").click(function() {
                var selectedOption = $("[pdc-action='change-shape-category'] option:selected");
				//var currentPage = $("#current_page").val();
                var currentPage = selectedOption.attr("cr_act");
				//var category = Clipart.getActiveCategory();
                var category = selectedOption.val();
				var pageSize = $("#default_page_size_shape").val();
				$.ajax({
					type : "POST",
					url : baseUrl + "pdp/shape/loadMoreImage",
					data : {
                        current_page : currentPage, 
                        category : category, 
                        page_size : pageSize
                    },
					beforeSend : function() {
						$('.content_designs .loading-img').show();
                        Clipart.showLoading();
					},
					error : function() {
					
					}, 
					success : function(response) {
						if (response != "nomore") {
							//Increment current page by 1
							//$("#current_page").val(parseInt(currentPage) + 1);
                            selectedOption.attr("cr_act",parseInt(currentPage) + 1);
							var data = $.parseJSON(response);
							var item = "";
							for (var i = 0; i < data.length; i++) {
								item += "<li cat='"+ data[i].category +"'> <a class='selection_img' rel='clover'><img color='' src='" + 
								pdpShapeMediaUrl + data[i].filename +"' id='shape_" + data[i].id + "' price='0' /></a></li>";
							}
							$("#shape_list").append(item);
							Clipart.showOrHideLoadMoreBtnShape(data.length);
						} else {
							$("[pdc-action='load-more-shape']").hide();
                            //alert('No more items to load!');
						}
						$('.content_designs .loading-img').hide();
					}
				});
			});
		},
        searchShapeEvent : function() {
            $("[pdc-action='search-shape']").click(function() {
                Clipart.searchShape();
            });
            $("#search_shape").on("keydown", function(e) {
                if(e && e.keyCode === 13) {
                    //When user press Enter, search shape
                    if($(this).val() !== "") {
                        Clipart.searchShape();
                    } else {
                        $(this).focus();
                    }
                }
            });
        },
        searchShape : function() {
            var keyword = $("#search_shape").val();
            if(!keyword) {
                $("#search_shape").focus();
                return false;
            }
            var pageSize = $("#default_page_size_shape").val();
            $.ajax({
                type : "POST",
                url : baseUrl + "pdp/shape/searchShape",
                data : {
                    current_page : 1,
                    page_size : 50, //Return max 50 result
                    keyword: keyword
                },
                beforeSend : function() {
                    $('.content_designs .loading-img').show();
                    Clipart.showLoading();
                },
                error : function() {

                }, 
                success : function(response) {
                    $("#search_shape_list").html("");
                    Clipart.switchCategoryFilterAndSearch(false);
                    if (response != "nomore") {
                        $('[pdc-data="no-shape-found"]').hide();
                        var data = $.parseJSON(response);
                        var item = "";
                        for (var i = 0; i < data.length; i++) {
                            item += "<li cat='"+ data[i].category +"'> <a class='selection_img' rel='clover'><img color='' src='" + 
                            pdpShapeMediaUrl + data[i].filename +"' id='shape_" + data[i].id + "' price='0' /></a></li>";
                        }
                        $("#search_shape_list").append(item);
                        //Clipart.showOrHideLoadMoreBtnShape(data.length);
                    } else {
                        $('[pdc-data="no-shape-found"]').show();
                        //alert('No more items to load!');
                    }
                    $('.content_designs .loading-img').hide();
                }
            });
		},
        resetSearchShape: function() {
            $('[pdc-action="reset-search-shape"]').click(function() {
                $("#search_shape").val("");
                Clipart.switchCategoryFilterAndSearch(true);
                $('[pdc-data="no-shape-found"]').hide();
            });
        }(),
		filterByCategory : function() {
            var selectedOption;
            $("[pdc-action='change-clipart-category']").change(function() {
                selectedOption = $($(this).find("option:selected"));
                $('#icon_list li[cat!='+ selectedOption.attr("value") +']').hide();
                $('#icon_list li[cat='+ selectedOption.attr("value") +']').show();
                if(!selectedOption.hasClass("cat_loaded")){
                    selectedOption.addClass("cat_loaded");
                    var currentPage = 2,
                        category = $(this).val(),
				        pageSize = $("#default_page_size").val(),
                        page_size = parseInt(currentPage - 1) * parseInt(pageSize);
                    $.ajax({
    					type : "POST",
    					url : baseUrl + "pdp/index/loadMoreImage",
    					data : {
                            current_page : 1, 
                            category : category, 
                            page_size : page_size
                        },
    					beforeSend : function() {
    						$('.content_designs .loading-img').show();
                            Clipart.showLoading();
    					},
    					error : function() {
    					
    					}, 
    					success : function(response) {
    						if (response != "nomore") {
    							var data = $.parseJSON(response);
    							var item = "", colorImages;
    							for (var i = 0; i < data.length; i++) {
									colorImages = data[i].color_img;
									if (colorImages != "") {
										colorImages = "fff__" + data[i].filename + "," + data[i].color_img;
									}
    								item += "<li cat='"+ data[i].category +"'> <a class='selection_img' rel='clover'><img color='"+ colorImages +"' src='" + 
    								pdpMediaUrl + 'artworks/' + data[i].filename +"' id='img" + data[i].image_id + "' price='"+ data[i].price+"' image_name='"+data[i].image_name+"' color_type='"+data[i].color_type+"'/></a> </li>";
    							}
    							$("#icon_list").append(item);
    					        selectedOption.attr("cr_act",2);
                                Clipart.showOrHideLoadMoreBtn(data.length);
    						} else {
    							$("[pdc-action='load-more-clipart']").hide();
                                //alert('No more items to load!');
    						}
    						$('.content_designs .loading-img').hide();
    					}
    				});
                } else {
                    Clipart.showOrHideLoadMoreBtn($('[pdc-data="clipart-list"] li:visible').length);
                }
            });
		},
        switchCategoryFilterAndSearch : function(isCategory) {
            if(isCategory) {
                $("#search_shape_list").hide();
                $("#shape_list").show();
                $("#shapeLibrary .add-more").show();
            } else {
                $("#shape_list").hide();
                $("#search_shape_list").show();
                $("#shapeLibrary .add-more").hide();
            }
        },
        filterShapeByCategory : function() {
            var selectedOption;
            $("[pdc-action='change-shape-category']").change(function() {
                selectedOption = $($(this).find("option:selected"));
                $('#shape_list li[cat!='+ selectedOption.attr("value") +']').hide();
                $('#shape_list li[cat='+ selectedOption.attr("value") +']').show();
                if(!selectedOption.hasClass("cat_loaded")){
                    selectedOption.addClass("cat_loaded");
                    var currentPage = 2,
                        category = $(this).val(),
				        pageSize = $("#default_page_size_shape").val(),
                        page_size = parseInt(currentPage - 1) * parseInt(pageSize);
                    $.ajax({
    					type : "POST",
    					url : baseUrl + "pdp/shape/loadMoreImage",
    					data : {
                            current_page : 1, 
                            category : category, 
                            page_size : page_size
                        },
    					beforeSend : function() {
    						$('.content_designs .loading-img').show();
                            Clipart.showLoading();
    					},
    					error : function() {
    					
    					}, 
    					success : function(response) {
                            Clipart.switchCategoryFilterAndSearch(true);
    						if (response != "nomore") {
    							var data = $.parseJSON(response);
    							var item = "", colorImages;
    							for (var i = 0; i < data.length; i++) {
									
    								item += "<li cat='"+ data[i].category +"'> <a class='selection_img' rel='clover'><img color='' src='" + 
    								pdpShapeMediaUrl + data[i].filename +"' id='shape_" + data[i].id + "' price='0'/></a> </li>";
    							}
    							$("#shape_list").append(item);
    					        selectedOption.attr("cr_act",2);
                                Clipart.showOrHideLoadMoreBtnShape(data.length);
    						} else {
    							$("[pdc-action='load-more-shape']").hide();
                                //alert('No more items to load!');
    						}
    						$('.content_designs .loading-img').hide();
    					}
    				});
                } else {
                    Clipart.showOrHideLoadMoreBtnShape($('[pdc-data="shape-list"] li:visible').length);
                }
            });
		},
        showOrHideLoadMoreBtn : function(items) {
            var remainItem = items % this.itemPerRow;
            if(remainItem !== 0) {
                $("[pdc-action='load-more-clipart']").hide();    
            } else {
                $("[pdc-action='load-more-clipart']").show();    
            }
        },
        showOrHideLoadMoreBtnShape : function(items) {
            var remainItem = items % this.itemPerRow;
            if(remainItem !== 0) {
                $("[pdc-action='load-more-shape']").hide();    
            } else {
                $("[pdc-action='load-more-shape']").show();    
            }
        },
        clickClipartTab : function() {
            $('[pdc-data="clipart-tab"]').click(function() {
                $('[pdc-data="clipart-tab"]').removeClass("active");
                $(this).addClass("active");
                var tab_act = $(this).attr("tab-content");
                $('[pdc-clipart-tab-content="clipart-tabs"] > div').hide();
                $('[pdc-clipart-tab="'+ tab_act +'"]').show();
            });
        }(),
        uploadClipartAction : function() {
            $('#fileToUpload').hover(function () {
                if (!$(this).hasClass("active")) {
                    document.getElementById('fileToUpload').addEventListener('change', Clipart.handleImageUpload.handleFileSelect, false);
                    $(this).addClass("active")
                }
            });
            $("#upload_custom_img_btn").click(function(){
                if (!Clipart.handleImageUpload.item.length) {
                    return false;
                }
                /* if(!$("#agreement").is(":checked")) {
                    alert($("#agreement_warming").val());
                    return false;
                }*/
                Clipart.handleImageUpload.uploadCustomImage(Clipart.handleImageUpload.item);
            });
        }(),
        handleImageUpload : {
            item : [],
            uploadCustomImage : function(items) {
                var form = $("#upload_custom_image");
                Clipart.handleImageUpload.uploadProgress(0);
                var formData = new FormData();
                $.each(items, function(i, item) {
                    if (item.upload) formData.append(item.name, item.file);
                });
                console.log(formData);
                var uploadUrl = $("#upload_images_form").attr("action");
                $.ajax({
                    url: uploadUrl,
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function(data){
                        Clipart.handleImageUpload.uploadProgress(100);
                        if (data != "") {
                            Clipart.handleImageUpload.showUploadedImage(data);
                        }
                    },
                    xhr: function() {
                        xhr = $.ajaxSettings.xhr()
                        xhr.upload.onprogress = Clipart.handleImageUpload.uploadProgress
                        xhr.upload.onloadend = Clipart.handleImageUpload.uploadProgress
                        return xhr
                    }
                });
            },
            handleFileSelect : function(d) {
                var g = d.target.files,
                    name = d.target.name,
                    checkFileResult = Clipart.handleImageUpload.checkFileSize(g[0]);
                //Hide all message before check the file
                $("#msg_error").addClass("hide");
                $("#msg_success").addClass("hide");
                if(checkFileResult.status == "error") {
                    $("#msg_error .message").text(checkFileResult.message);
                    $("#msg_error").removeClass("hide");
                    return false;
                } else {
                    $("#msg_success .message").text(checkFileResult.message);
                    $("#msg_success").removeClass("hide");
                }
                Clipart.handleImageUpload.item = [];
                $.each(g,function(i,file){
                     //test image types: image/svg+xml, image/png, image/gif, image/jpeg, image/jpg, image/bmp, image/tiff, image/tif
                    if (file.type.match("image/")) {
                        Clipart.handleImageUpload.item.push({name:name,file:file,upload:true});
                        //Clipart.handleImageUpload.uploadCustomImage(item);
                    } else {
                        alert("Please upload a file in one of the following formats: .svg, .jpg, .png, .jpeg, .bmp, .gif");
                        document.getElementById("upload_images_form").reset();
                    }

                });
            },
            //Return object.status = error | success with message
            checkFileSize : function(file) {
                var msg = {
                        status: "success",
                        message: file.name
                    },
                    uploadConfig = JSON.parse($("#pdc_upload_config").val()),
                    fileSize = parseFloat(file.size);
                if(file.type == "image/svg+xml") {
                    return msg;
                }
                if(fileSize !== undefined && fileSize > 0) {
                    //Check file size exceed the limit size or not
                    var maxSize = parseFloat(uploadConfig.upload_max_size),
                        minSize = parseFloat(uploadConfig.upload_min_size);
                    if(fileSize > maxSize && maxSize > 0) {
                        msg.status = "error";
                        msg.message = uploadConfig.max_size_alert;
                    } else if(fileSize < minSize && minSize > 0) {
                        msg.status = "error";
                        msg.message = uploadConfig.min_size_alert;
                    }
                }
                return msg;
            },
            percentage : function(i,total){
                return Math.round(total?i * 100 / total:100).toString() + '%'
            },
            uploadProgress : function(evt) {
                var t=$.type(evt),
                    pcent=t=='object'&&evt.lengthComputable?  Clipart.handleImageUpload.percentage(evt.loaded,evt.total):
                          t=="number"?evt+'%':
                          t=="string"?evt:
                          "0%";
                          //console.log(pcent);
                $('.progress-bar').html(pcent);
                $('.progress-bar').css("width", pcent);
                if(pcent=='100%'){
                    $('.progress').hide();
                } else {
                    $('.progress').show();
                }
            },
            showUploadedImage : function(response) {
                var images = $.parseJSON(response);
                if (images.length) {
                    $.each(images, function(index, imgSrc) {
                        var item = Clipart.getImageHtmlFormated(imgSrc);
                        $("#lists_img_upload .row").prepend(item);
                    });
                }
                document.getElementById("upload_images_form").reset();
            }
        },
        /** Add a div contain image to upload list image**/
        getImageHtmlFormated : function(imageurl) {
            var imgDiv = '';
            imgDiv += '<div class="col-sm-6 col-md-4">';
				imgDiv += '<div class="thumbnail" pdc-data="upload-item">';
					imgDiv += '<img pdc-data="upload-image" width="130px" alt="Invalid Image" src="'+ imageurl +'" color="">';
				        imgDiv += '<div class="caption" style="text-align: center;">';
							imgDiv += '<p>';
                                imgDiv += '<a class="btn btn-default crop-item" role="button"><span class="glyphicon glyphicon-retweet" aria-hidden="true"></span>Crop</a>';
									imgDiv += ' <a class="btn btn-default add-to-design" role="button"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span>Add To Design</a>';
								imgDiv += '</p>';
							imgDiv += '</div>';
						imgDiv += '</div>';
					imgDiv += '</div>';
            return imgDiv;
        },
        activeCropItem : function() {
            $("#uploadPhotos").on("click", ".crop-item", function() {
                var img = $(this).closest('[pdc-data="upload-item"]').find("[pdc-data='upload-image']");
                $("#crop_image").attr("src", $(img).attr("src"));
                $("#crop_image").Jcrop({
                    onSelect: function(c) {
                        console.log(c);
                        window.pdcCropSelection = c;
                    },
                    onchange: function(c) {
                        //console.log(c);
                    },
                    onRelease: function() {
                        console.log(this);
                    },
                    boxWidth: 870, 
                    boxHeight: 700,
                    setSelect:   [ 100, 100, 300, 300 ],
                    //aspectRatio: 16 / 9
                });
                
                $("#crop_modal").modal("show");
            });
        }(),
        cropImage : function() {
            $('[pdc-action="crop-image"]').click(function() {
                $(this).text("Cropping...");
                Clipart.sendCropRequest(window.pdcCropSelection);
            });
        }(),
        destroyJcrop : function() {
            $("#crop_panel #crop_image").data('Jcrop').destroy();
        },
        cancelCrop : function() {
            $('[pdc-action="cancel-crop"]').click(function() {
                Clipart.destroyJcrop();
                $("#crop_modal").modal("hide");
            });
        }(),
        sendCropRequest : function(data) {
            var cropImage = $("#crop_image").attr("src").split("/").splice(-1)[0];
            data.filename = cropImage;
            var cropUrl = baseUrl + "pdp/upload/cropImage";
            $.ajax({
				type : "POST",
				url : cropUrl,
				data : data,
				beforeSend : function() {
					$('.pdploading').show();
				},
				error : function() {
					console.log("Something went wrong...");
				}, 
				success : function(response) {
					if(response != "") {
                        var responseJson = JSON.parse(response);
                        if(responseJson.status === "success") {
                            var item = Clipart.getImageHtmlFormated(responseJson.crop_image);
                            $("#lists_img_upload .row").prepend(item);
                            $('[pdc-action="crop-image"]').text("Crop");
                            $('[pdc-action="cancel-crop"]').click();
                        } else if(responseJson.status === "error") {
                            alert(responseJson.message);
                        }
                    }
				}
			});
            
        },
        addImageToDesign : function() {
            $("#lists_img_upload").on("click", ".add-to-design", function() {
                $(this).closest('[pdc-data="upload-item"]').find('[pdc-data="upload-image"]').click();
                $("#uploadPhotos").modal("hide");
            });
        }()
	}
	PDCActions = {
		doRequest : function (url, data, callback) {
			$.ajax({
				type : "POST",
				url : url,
				data : data,
				beforeSend : function() {
					$('.pdploading').show();
                    if(window.Pace !== undefined) {
                        Pace.restart();
                    }
				},
				error : function() {
					console.log("Something went wrong...");
				}, 
				success : function(response) {
					callback(response);
					$('.pdploading').hide();
				}
			});
		},
		deferredRequest : function (url, data, callback) {
			var def = $.Deferred();
			return $.ajax({
				type : "POST",
				url : url,
				data : data,
				beforeSend : function() {
					$('.pdploading').show();
				},
				error : function() {
					console.log("Something went wrong...");
				}, 
				success : function(response) {
					callback(response);
					//$('.pdploading').hide();
					def.resolve();
				}
			});
			//return def.promise();
		},
        save_json_file: function(jsonString){
			/* var url = m + 'index.php/pdp/index/saveJsonfile',
			data = {json_file : jsonString};
            PDCActions.doRequest(url, data, function(response) {
                var jsonData = $.parseJSON(response);
				$("input[name='extra_options']").val(jsonData.filename);
                console.log(jsonData);
            }); */
        },
	    save_cr_design: function(add_to){
            var json_array = {
                    inlay   : $('#pdc_sides li.active').attr('inlay'),
                    side    : $('#pdc_sides li.active').attr('tab'),
                    img     : $('#pdc_sides li.active').attr('side_img'),
                    items   : []
                };
            var f = $('#skin_url').val(),
                g = $('#base_dir').val(),
                ml = $('#media_url').val(),
                tab = $('#pdc_sides li.active').attr('tab'),
                bof, item_info, 
                productId = $('#product_id').val();
            resetTextForm();
            var editid = $('#edit_id').val();
            var i__ = 0;
            $(".product-image .active").removeClass("active");
            return json_array;
        },
        pdc_add_to: function() {
    		//Check customer logged in or not
            var json_final = [];
            $('.pdc_info_save').each(function(){
                var side_name = $(this).attr('alt'),
                    side_img = $('#pdc_sides li[tab='+side_name+']').attr('side_img');
                    json_obj = {
                    name: side_name,
                    img: side_img,
                    json: $(this).val()
                }
                if($('.design-color-image li.active').length > 0){
                   json_obj.color = $('.design-color-image li.active').attr('id');
                }
                json_final.push(json_obj);
            });
    		var jsonString = JSON.stringify(json_final),
    			pdpBtnAction = $("#pdc_btn_action").val();
    		switch (pdpBtnAction) {
				case "pdc_add_to_cart" :
					LoadDesign.updatePDPCustomOption(jsonString);
					$('#pdc_design_popup').hide();
					$(".add-to-cart .btn-cart").click();
					break;
				case "pdc_add_to_wishlist" :
					LoadDesign.updatePDPCustomOption(jsonString);
					$('#pdc_design_popup').hide();
					if ($("#wishlist_item_id").val() != "") {
						//Update wishlist
						//Cause add to compare and update wishlist have the same class : link-compare
						//So find the correct link to click
						$(".add-to-links .link-compare").each(function() {
							if($(this).attr('onclick') != "") {
								$(this).click();
								return;
							}
						});
						//link-compare
					} else {
						$(".add-to-links .link-wishlist").click();
					}
					break;
				case "pdc_save_admin_sample" : 
					var url = $("#url_site").val() + "pdp/index/saveAdminTemplate";
						currentProductId = $("#current_product_id").val(),
						data = {product_id : currentProductId, pdc_design : jsonString};
					this.doRequest(url, data, function() {
						//alert('Done');
					});
					break;
				case "save_before_share" : 
					this.saveBeforeShare(jsonString);
					break;
                case "save_design_btn" :
                    LoadDesign.updatePDPCustomOption(jsonString);
					$("#pdc_design_popup .overlay").click();
                    break;
			}
    	},
		saveBeforeShare : function (jsonString) {
			var url = $("#save_design_url").val(),
				productUrl = $("#product_url").val(),
				postData = {pdpdesign: jsonString, url : productUrl};
				this.doRequest(url, postData, this.activeAddThis);
		},
		activeAddThis : function(response) {
			var jsonData = $.parseJSON(response);
			//Update share url
			for(var i = 0; i < addthis.links.length; i++){
				//console.log(addthis);
				addthis.links[i].share.url = jsonData.url;
			}
			//Active add this share button
			$(".social-bottom .overlay-btn").addClass('send-back');
		},
		pdpBtnClick : function () {
			$(".pdp-btn").on("click", function() {
				$("#pdc_btn_action").val($(this).attr('id'));
				PDCActions.save_cr_design('done');
			});
		},
		loadFonts : function() {
			var loadFontUrl = baseUrl + "pdp/index/loadFonts";
			$.ajax({
				type : "GET",
				url : loadFontUrl,
				beforeSend : function() {
					//$('.pdploading').show();
					console.log("Load Font");
				},
				error : function() {
					console.log("Something went wrong...");
				}, 
				success : function(response) {
					//$('.pdploading').hide();
					$(".fonts-container").html(response);
				}
			});
		}
	}
    Clipart.init();
    PDCActions.pdpBtnClick();
});