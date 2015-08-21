var pdp_ins_results = jQuery.noConflict();
pdp_ins_results(document).ready(function($){
    //alert($('img.pdp_ins_results').length);
    var html = '';
    $('img.pdp_ins_results').each(function(){
        var $this = $(this);
        html += '<img src="'+$this.data('full')+'" thumb="'+$this.data('thumb')+'" full="'+$this.data('full')+'"  color="" />'; 
    });
    window.opener.G.updateAllInstagramImage(html,$('#pdc_ins_next_url').val(),$('#pdc_user_id').val(),$('#pdc_user_access_token').val());
	window.close();
});