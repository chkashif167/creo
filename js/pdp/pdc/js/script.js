var pdc2 = jQuery.noConflict();
var isColorUpdated = false;
pdc2(document).ready(function($){

/*###### RESPONSIVE NOTIFY #####*/	
	jQuery(window).on('load resize', function(){ 
		var width = jQuery(window).width();
		console.log(width);
		if (width <= 560) {
			jQuery('#noticePopup').modal('show')
		} 
		if(!isColorUpdated){
			updateColorFromJson();
		}
	});
/*###### TOOLTIPS #####*/	
	$(function () {
	  $('[data-toggle="tooltip"]').tooltip({
	  animation:false,
	  trigger: 'hover focus tap taphold' 
	  })
	});

	$(function () {
		$('[data-toggle="popover"]').popover({ 
		trigger: "hover",
		placement: 'auto top',
		html:true
		});
	});
/* Swap Item Color */
	$('#show_productColors').click(function(){
		$(this).toggleClass('selected');
		$('.itemStyles').toggleClass('expanded');			
	}); 
/* Alignment popup */
	$('#pdc_alignment').click(function(){
		$(this).toggleClass('selected');
		$('#move_item').toggleClass('active');			
	});
/* Color Filling popup */
	$('#ColorFillBtn').click(function(){
		$(this).toggleClass('selected');
		$('.pdc-main.design-area').toggleClass('expandColor');			
	});
	
/*###### DRAG TOOLS PANEL #####*/
		/* var elem = window.document.querySelector('.edit-tools');
		var draggie = new Draggabilly( elem, {
		  handle: '.drag-panel',
		  containment: $("#pdcwrapper").length ? '#pdcwrapper' : '.wrap_pdp_design'
		});	  */ 
	 
		var color_panel = window.document.querySelector('.color_fill_tool');
		var draggie = new Draggabilly( color_panel, {
		  handle: '.drag-panel',
		  //containment: $("#pdcwrapper").length ? '#pdcwrapper' : '.product-detail'
		});		


	$('#opacityBtn').click(function(){
		$(this).toggleClass('selected');
		$('.opacity_tool').toggleClass('expanded');			
	});
	$('#ColorFillBtn').click(function(){
		$(this).toggleClass('selected');
		$('.color_fill_tool').toggleClass('expanded');			
	}); 
/*###### OPACITY OPTION #####*/
/* noUiSlider Config for Opacity slider */
	$('#opacitySlider').noUiSlider({
	start: 100,
	step: 1,
	connect: 'upper',
	direction: 'rtl', 
	orientation: 'vertical',
	
	// Configure tapping, or make the selected range dragable.
	behaviour: 'tap-drag',
	
	// Full number format support.
	format: wNumb({
		mark: ',',
		decimals: 0
	}),
	
	// Support for non-linear ranges by adding intervals.
	range: {
		'min': 0,
		'max': 100
	}
	});
	// Reading/writing + validation from an input? One line.
	$('#opacitySlider').Link('lower').to($('#opacity-input'));

	// Optional addon: creating Pips (Percentage In Point);
	$("#opacitySlider").noUiSlider_pips({
		mode: 'range',
		density: 4
	});
    $('#opacitySlider').on('slide', function(){
        CanvasEvents.editItem('opacity',$('#opacity-input').val()/100);
    });


/*###### PHOTO CATEGORY LIST #####*/
	/* $(".selected_category_photo").mCustomScrollbar({
		scrollButtons:{ enable: true },
		axis:"y",
		theme: 'inset-2-dark',
		live: true
	}); */

/*###### Design Area #####*/
	/* $(".design-area").mCustomScrollbar({
		scrollButtons:{ enable: true },
		axis:"yx",
		theme: 'inset-2-dark',
		live: true
	}); */
	
/*###### Quotes Library LIST #####*/
	/* $(".pdc_text_list").mCustomScrollbar({
		scrollButtons:{ enable: true },
		axis:"y",
		theme: 'inset-2-dark',
		live: true
	}); */
/*###### Uploaded Image List #####*/
	/* $("#lists_img_upload").mCustomScrollbar({
		scrollButtons:{ enable: true },
		axis:"y",
		theme: 'inset-2-dark',
		live: true
	}); */
	
/*###### Color filling CATEGORY LIST #####*/
	/* $(".color_fill_list").mCustomScrollbar({
		scrollButtons:{ enable: true },
		axis:"y",
		theme: 'inset-2-dark',
		live: true
	}); */
/* Show Color picker popup */
	$('.btn-picker, .btn-color-list').on('click',function(){
		$('#pdc_color_picker').toggle();
		$('#color_fill_list').toggle();
	});
	$('.sliders').noUiSlider({
		start: 127,
		connect: "lower",
		orientation: "vertical",
		range: {
			'min': 0,
			'max': 255
		},
		format: wNumb({
			decimals: 0
		})
	});

	// Bind the color changing function to the slide event.
	$('.sliders').on('slide', setInputColor);
	
	function setInputColor(){

		// Get the slider values,
		// stick them together.
		var color = 'rgb(' +
			$("#pRed").val() + ',' +
			$("#pGreen").val() + ',' +
			$("#pBlue").val() + ')';

		// Fill the color box.
		$(".result").css({
			background: color,
			color: color
		});
        CanvasEvents.editItem('color',color);
		/* Set value for .input_picker input field */
		var rgb = color.match(/\d+/g);
		hex = '#'+ ('0' + parseInt(rgb[0], 10).toString(16)).slice(-2) + ('0' + parseInt(rgb[1], 10).toString(16)).slice(-2) + ('0' + parseInt(rgb[2], 10).toString(16)).slice(-2);
		$('.input_picker').val(hex);
	}
	
	 
/*###### FONTs FAMILY LIST #####*/
/* 	$(".item-font-list").mCustomScrollbar({
		scrollButtons:{ enable: true },
		axis:"y",
		theme: 'inset-2-dark',
		live: true
	}); */

/*###### UPLOAD POPUP FORM #####*/
	/*$("#fileToUploadNew").fileinput({
        allowedFileTypes: ['image'],
		allowedFileExtensions : ['jpg', 'png','gif','svg','jpeg','bmp'],
        maxFileSize: 1000,
        maxFilesNum: 10,
		//previewSettings: {image: {width: "auto", height: "90px"}},
		browseClass: "btn btn-primary",
		showCaption: false,
		showPreview: true,
		uploadUrl: $("#upload_images_form").attr("action"), // server upload action
		uploadAsync: true,
		maxFileCount: 5,
	});*/
/* ##### Toggle Responsive Button ##### */	
	$('.open-right-panel').on('click',function() {		
		$(this).toggleClass('active-panel');		
		$(this).find('.btn-status').toggleClass('pdc-clear');		
		$('.pdc-design-area-left').toggleClass('design-are-to-left');		
		$('.pdc-design-area-right ').toggleClass('right-to-open');	
	});
});
//update product color based on json file
function updateColorFromJson(){	
	var redesign = getURLParameter('redesign');
	if(redesign != null){
		isColorUpdated = true;
		var mainWindow = top.document;	
		var _designInJson = JSON.parse(jQuery("#extra_options_value", mainWindow).val());
		if(_designInJson){
		jQuery.each( _designInJson, function( key, val ) {
			var side_color_id = val.side_color_id;
			console.log("color code is called here"+side_color_id);
			jQuery('.pdc_design_color').find("[pdc-color='"+side_color_id+"']").trigger('click');
		  });
		}
	}
	}
function getURLParameter(name) {
  return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null;
}	
 