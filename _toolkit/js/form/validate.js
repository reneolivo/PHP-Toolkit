$(document).ready(function() {
	var regex = {
		email: /[0-9]{2,3}-[0-9]{1,2}-[0-9]{1,2}/,
		datetime: /[0-9]{2,3}-[0-9]{1,2}-[0-9]{1,2}/
	};
	
	forEachValidationField(function(field) {
		$(field).change(function() {
			validate(this);
		});
	});
	
	$('.tkForm').submit(function() {
		validateAll();
		
		if ($(this).children('.tkError').length > 0) {
			$(this).addClass('.tkError');
			
		} else {
			$(this).removeClass('.tkError');
		}
		return false;
	});
	
	function forEachValidationField(funct) {
		var type;
		var fields = '';
		
		for (i in regex) {
			type = '.tkValidate.tkType_'+i;
			fields += type+' input, '+type+' textarea, '+type+' select, ';
		}
		
		$(fields).each(function() {
			funct(this);
		});
	}
	
	function validate(object) {
		var value = $(object).val();
			
		if (!value.match(regex[i])) {
			$(object).addClass('tkError');	
		} else {
			$(object).removeClass('tkError');
		}
	}
	
	function validateAll() {
		forEachValidationField(function(field) {
			validate(field);
		});
	}
});