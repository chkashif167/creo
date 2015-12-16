var pdc_text_curved = jQuery.noConflict();
pdc_text_curved(document).ready(function($){
    $('#pdc_ctext_convert').click(function(){
        var active = canvas.getActiveObject();
        if (!active) return;
        if((active.type=='text')||(active.type=='i-text')){
            $('[ pdc-box="curved"]').show();
            $('#pdc_ctext_convert').addClass('active');
            ////convert to curvedText/////
            var CurvedText = new fabric.CurvedText(active.text,{
                //width: 100,
                //height: 50,
                left: active.left,
                top: active.top,
                textAlign: 'center',
                fill: active.fill,
                radius: 100,
                fontSize: active.fontSize,
                spacing: 15,
                lockUniScaling: true,
                fontFamily: active.fontFamily,
                name: active.name,
                //scaleX: active.scaleX,
                //scaleY: active.scaleY,
                opacity: active.opacity,
                fontWeight: active.fontWeight,
                fontStyle: active.fontStyle,
                price: active.price,
                //angle: active.angle
            });
          canvas.remove(active);
          canvas.add(CurvedText).setActiveObject(CurvedText).calcOffset().renderAll();
        }else if(active.type=='curvedText'){
            $('[ pdc-box="curved"]').hide();
			$('#pdc_ctext_convert').removeClass('active');
            ////convert to i-text/////
            var IText = new fabric.IText(active.text,{
                //width: 100,
                //height: 50,
                left: active.left,
                top: active.top,
                textAlign: 'center',
                fill: active.fill,
                radius: 100,
                fontSize: active.fontSize,
                spacing: 15,
                lockUniScaling: true,
                fontFamily: active.fontFamily,
                name: active.name,
                scaleX: active.scaleX,
                scaleY: active.scaleY,
                opacity: active.opacity,
                fontWeight: active.fontWeight,
                fontStyle: active.fontStyle,
                price: active.price,
                angle: active.angle
            });
          canvas.remove(active);
          canvas.add(IText).setActiveObject(IText).calcOffset().renderAll();
        }else{
            ////do nothing/////
            $('[ pdc-box="curved"]').hide();
        }
    })
    $('#pdc_ctext_reverse').click(function(){
		var obj = canvas.getActiveObject(); 
		if(obj){
		    var scaleXobj =  obj.scaleX,
                scaleYobj = obj.scaleY;
            obj.set({
                scaleX: 1,
                scaleY: 1
            })
			obj.set('reverse',$(this).is(':checked')); 
			canvas.renderAll();
            obj.set({
                scaleX: scaleXobj,
                scaleY: scaleYobj
            })
			canvas.renderAll();
		}
	});
	$('#pdc_ctext_radius, #pdc_ctext_spacing').change(function(){
		var obj = canvas.getActiveObject(); 
		if(obj){
		      var scaleXobj =  obj.scaleX,
                scaleYobj = obj.scaleY;
            obj.set({
                scaleX: 1,
                scaleY: 1
            })
			obj.set($(this).attr('name'),$(this).val()); 
		   canvas.renderAll();    
            obj.set({
                scaleX: scaleXobj,
                scaleY: scaleYobj
            })
			canvas.renderAll();     
		}
	});
})