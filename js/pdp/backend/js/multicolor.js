var multicolor = jQuery.noConflict();
multicolor(function($){
	Multicolor = {
		addMoreArtworkColorImage : function() {
			$("#add_more_color_image").click(function() {
				var counter = parseInt($("#counter").val()) + 1;
				$("#counter").val(counter);
				var row = "";
				row += "<tr>";
					row += "<td><input class='form-control required-entry' type='text' id='colorpicker_" + counter + "' name='color-image["+ counter +"]' value=''/></td>";
					row += "<td><input type='file' class='required-entry' name='artworkimage_" + counter + "' /></td>";
					row += "<td><input type='text' class='form-control validate-digits' name='sort["+ counter +"]' value=''/></td>";
					row += "<td><a class='colorimage-option btn btn-danger' id='removeimg_" + counter + "'>Remove</a></td>";
				row += "</tr>";

				$("#artwork_color_option").append(row);
				Multicolor.activeColorPicker("#colorpicker_" + counter);
			});
		},
		removeArtworkColorOption : function() {
			$("#artwork_color_option").on('click', '.colorimage-option' , function() {
				$(this).parent().parent().remove();
			});
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
		}
	}
	Multicolor.addMoreArtworkColorImage();
	Multicolor.removeArtworkColorOption();
});