jQuery( document ).ready(function($) {

	if( $('.wplc-color-box').length > 0 ) {
		$('.wplc-color-box').wpColorPicker();
	}

	if( $('#wpcdt-countdown-datepicker').length > 0 ) {
		$('#wpcdt-countdown-datepicker').datetimepicker({
			dateFormat	: 'yy-mm-dd',
			timeFormat	: 'HH:mm:ss',
			minDate		: 0,
			changeMonth	: true,
			changeYear	: true,
		});
	}

	if( $('.wpcdt-circle-slider').length > 0 ) {
		$(".wpcdt-circle-slider").slider({
			min		: 0.0033333333333333335,
			max		: 0.13333333333333333,
			step	: 0.003333333,
			slide	: function (event, ui) {
						$(this).parent().find(".wpcdt-number").val(ui.value);
					},
			create	: function(event, ui){
						$(this).slider('value',$(this).parent().find(".wpcdt-number").val());
					}
		});
	}

	if( $('.wpcdt-background-slider').length > 0 ) {
		$(".wpcdt-background-slider").slider({
			min		: 0.1,
			max		: 3,
			step	: 0.1,
			slide	: function (event, ui) {
						$(this).parent().find(".wpcdt-number").val(ui.value);
					},
			create	: function(event, ui){
						$(this).slider('value',$(this).parent().find(".wpcdt-number").val());
					}
		});
	}

	/* Click to Copy the Text */
	$(document).on('click', '.wpos-copy-clipboard', function() {
		var copyText = $(this);
		copyText.select();
		document.execCommand("copy");
	});
});