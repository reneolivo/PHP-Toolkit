// JavaScript Document

var waiting_form = null;
var validated = false;

$(document).ready(function() {	
	$('form._tk_form').submit(function() {
		if (validated == false) {
			waiting_form = $(this);
			
			var component = get_component($(this).attr('class'));
			
			validate(component);
			
			return false;
		}
	});
		
	function get_component(classes) {
		var classes = classes.split(' ');
		
		for(var i in classes) {
			if (classes[i].search(/_tk_cmp_/) != -1) {
				return classes[i];
			}
		}
	}
	
	function validate(cmp) {		
		$.post(toolkit.http+'_php/javascripts/properties.php', {'_tk_cmp': cmp}, function(data) {
			//alert(data);
	  		eval('data = '+data);
			
			for (i in data.fields) {
				var field = waiting_form.find('input[name='+i+']');
				
				if (data.fields[i].mandatory == true && field.val() == '') {
					field.addClass('error');
					alert('no se puede dejar vacio');
					return false;
				}
			}
			
			validated = true;
			waiting_form.submit();
	 	});
	}
});