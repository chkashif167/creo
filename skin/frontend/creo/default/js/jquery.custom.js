// j.work enable disable back button
jQuery(document).ready(function(){
	if(history.length > 1){
		jQuery('#goback').click(function(){
			parent.history.back();
			return false;
		});
	}else{
		jQuery('#goback').prop('disabled', true);
	}
});
