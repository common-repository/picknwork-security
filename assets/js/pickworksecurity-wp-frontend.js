jQuery(document).ready(function($) {
$(document).ready(function() {
	
	/* ***************************************
	WORKFLOW FOR RESEND OTP CHECKBOX
	*************************************** */
	$('#pnwresendotp').on('click', function() {
		
		var getvalue = $('#pnwresendotp').val();
		
		if(getvalue === 'resend'){
		 document.forms['pnwloginform'].submit();
		}
		
	});

	
	
	
	
	/* ***************************************
	WORKFLOW FOR FIELD ERROR POINTERS FOR NEWSLETTER SUBSCRIPTION
	*************************************** */
	var finderror = $('.pnwfielderror').text();
	if(finderror === 'subscribefield'){
	$('.pnwsubscription').css({"border": "2px solid red"});	
	}
	
	if(finderror === 'subscribetypefield'){
	$('.pnwsubscriptiontype').css({"border": "2px solid red"});	
	}
	if(finderror === 'subscribegroupfield'){
	$('.pnwsubscriptiongroup').css({"border": "2px solid red"});	
	}
	
});
});












