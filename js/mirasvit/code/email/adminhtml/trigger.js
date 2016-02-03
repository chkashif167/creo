function saveAndSend(button, send, email)
{
	if (send) {
		editForm.submit($('edit_form').action + 'back/send/test_email/' + $('test_email').value);
	} else {
	    $(button).hide();
	    $(button).parentElement.innerHTML +=
	        '<input type="text" id="test_email" value="' + email + '"class="required-entry absolute-advice input-text" style="margin-left: 5px; width:200px">'
	         + '<button type="button" class="scalable" onclick="saveAndSend(this, true, null)"><span><span><span>Send</span></span></span></button>';
	}
}