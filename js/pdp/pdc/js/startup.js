var mst = jQuery.noConflict();
mst(document).ready(function ($) {
    PDC_Actions.ini_design();
    $('[pdc-data="TEMP_SWITCH_SIDE"]').click(function() {
		if($(this).hasClass("active")) {
			return false;
		}
		$(".pdc-side-thumbnail .switch-side").removeClass("active");
		$(this).addClass("active");
		var sideId = $(this).attr("id").replace("temp_side_", "");
		console.info("Click side " + sideId);
		$('[pdc-side="'+ sideId +'"]').click();
	});
});