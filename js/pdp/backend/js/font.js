var image = jQuery.noConflict();
image(function($){
	var baseUrl = $("#base_url").val();
	var mediaUrl = $("#media_url").val();
	FontItem = {
        upload : function() {
            $("#upload-files-form").modal('show');
        }
	}
	PDP.initFont();
});