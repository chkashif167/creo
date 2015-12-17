var pdp_ins_results = jQuery.noConflict();
pdp_ins_results(document).ready(function($){
    //alert($('img.pdp_ins_results').length);
    var html = '';
    $('img.pdp_ins_results').each(function(){
        var $this = $(this);
        html += '<img full="'+$this.data('full')+'" src="'+$this.data('thumb')+'" color="" />'; 
    })
    window.opener.G.updateAllInstagramImage(html);
	window.close();
});