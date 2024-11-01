function wcbn_trigger_change(val) {
	var option_class = jQuery('#wcbn-trigger-notify :selected').attr('class')
	var option_val = jQuery('#wcbn-trigger-notify :selected').val();

	if("wc-action-options" == option_class) {
		jQuery("#wcbn-override-open").show();
	} else {
		jQuery("#wcbn-override-open").hide();
	}

	if(option_val && ("open" == option_val || "scroll" == option_val)) {
		jQuery("#wcbn-override-open").hide();
	} else {
		jQuery("#wcbn-override-open").show();
	}
}

function wcbn_validate_form() {
	jQuery('#post').submit(function() {
		var selected_trigger = jQuery("#wcbn-trigger-notify :selected").val();
		var selected_popup = jQuery("#wcbn-notify-popup :selected").val();

		if(!selected_trigger) {
			alert("Trigger not selected");
			return false;
		} else if(!selected_popup) {
			alert("Popup not selected")
			return false;
		} else {
			return true;
		}
	});
}

jQuery(document).ready(function() {
	if(jQuery("#wcbn-trigger-notify").length == 0) {
		return;
	} else {
		wcbn_validate_form();
	}
});

