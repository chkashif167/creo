function saveAjax()
{
    editForm.validator.validate();
    varienGlobalEvents.fireEvent('formSubmit', editForm.formId);
    $(editForm.formId).request({
    	onComplete: function(response) {
    		if (response.responseText.isJSON()) {
    			var json = response.responseText.evalJSON();
    			if (json.success) {
    				$('messages').innerHTML = '';
        			$('messages').insert('<ul class="messages"><li class="success-msg"><ul><li><span>' + json.message + '</span></li></ul></li></ul>');
                }
            }
    	}
    });
}