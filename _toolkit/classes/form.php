<?php
class form {
	private static $text_f = array('varchar', 'id', 'integer', 'long', 'var_string', 'int', 'string', 'email', 'date', 'time', 'datetime');
	private static $password_f = array('password');
	private static $choice_f = array('radio', 'checkbox');
	private static $hiden_f	= array('hidden');
	private static $textarea_f = array('text', 'blob');
	private static $select_f = array('select', 'choice', 'boolean');
	private static $file_f = array('image', 'file');
	private static $button_f = array('button', 'submit');
	
	public static $submitText = 'Submit';
	
	static function init_params(&$action, &$method, &$enctype, &$attr) {
		if ($action == NULL) $action = '#';
		if ($method == NULL) $method = 'post';
		if ($enctype == NULL) $enctype = 'application/x-www-form-urlencoded';
		if ($attr == NULL) $attr = array();
		
		if (isset($attr['validate']) and $attr['validate'] === true) 
		{
			echo self::loadValidator();
		}
	}
	
	static function loadValidator() {
		return	PHPToolkit::loadCSS('form/validate.css').
				PHPToolkit::loadJavascript('jquery/jquery.js').
				PHPToolkit::loadJavascript('form/validate.js');	
	}
	
	static function init($action = NULL, $method = NULL, $enctype = NULL, $attr = NULL) {
		
		self::init_params($action, $method, $enctype, $attr);
		
		$attributes = (isset($attr['attributes'])) ? html::attr($attr['attributes']) : NULL;
		
		return "\n<form class=\"tkForm\" action=\"{$action}\" method=\"{$method}\" enctype=\"{$enctype}\" {$attributes}>\n";
	}
	
	static function field($field, $value = '', $type = 'varchar', $selected = NULL, $attr = NULL) {
		$str = ''; 
		
		
		if (in_array($type, self::$text_f)) {
			$str .= "\t\t<input type=\"text\" id=\"{$field}\" name=\"{$field}\" value=\"{$value}\" />";
		} else if (in_array($type, self::$choice_f)) {
			$str .= "\t\t<input type=\"{$type}\" id=\"{$field}_{$value}\" name=\"{$field}\" value=\"{$value}\"";
			if ($selected == true) $str .= "checked=\"checked\"";
			$str .= "/>";
		} else if (in_array($type, self::$textarea_f)) {
			$str .= "\t\t<textarea id=\"{$field}\" name=\"{$field}\">{$value}</textarea>";
		} else if (in_array($type, self::$select_f)) {
			if ($type == 'choice') {
				foreach($value as $k => $v) {
					$sel = ($k == $selected) ? true: NULL;
					
					$str .= html::choice($field, $k, $v, $sel);
				}
			} else {
				$str .= "\t\t<select id=\"{$field}\" name=\"{$field}\">";
				
				switch ($type) {
					case 'boolean':
						$str .= html::option('S&iacute;', 1);
						$str .= html::option('No', 0);
					break;
					
					case 'select':
						
						if (isset($attr['from']) or isset($attr['to'])) {
							if (!isset($attr['from']))
								$attr['from'] = 1;
								
							if (!isset($attr['to']))
								$attr['to'] = 10;
							
							for ($x = $attr['from']; $x <= $attr['to']; $x++) {
								$sel = ($x == $selected) ? true: NULL;
								
								$str .= html::option($x, $x, $sel);
							}
						} else {
							if (!is_array($value)) {
								//die("<br /><b>ERROR:</b> <i>\"field/input\"</i> function's property <i>\"value\"</i> must be an array. on {$field}.<br />");
								$value = array();
							}
							
							foreach($value as $k => $v) {
								$sel = ($k == $selected) ? true: NULL;
								
								$str .= html::option($v, $k, $sel);
							}
						}
					break;
				}
				
				$str .= "</select>";
			}
		} else if (in_array($type, self::$file_f)) {
			$str .= "\t\t<input type=\"file\" id=\"{$field}\" name=\"{$field}\" />";
		} else if (in_array($type, self::$password_f)) {
			$str .= "\t\t<input type=\"password\" id=\"{$field}\" name=\"{$field}\" />";
		} else if (in_array($type, self::$button_f)) {
			$str .= "\t\t<input type=\"{$type}\" id=\"{$field}\" value=\"{$field}\" />";
		} else {
			die("<br /><b>ERROR:</b> field type <i>\"{$type}\"</i> is not defined<br />\n");
		}
		
		return $str;
	}
	
	static function input($field, $type, $value = '', $label = true, $selected = NULL, $attr = NULL) {
		
		if (!in_array($type, self::$hiden_f)) 
		{
			//var_export($attr);
			$class  = ' class="tkFormInput tkType_'.$type; 
			$class .= (isset($attr['validate']) and $attr['validate'] === true) ? ' tkValidate' : NULL;
			$class .= '"';
			
			if (in_array($type, self::$choice_f))
				$str = "\n\t<div $class id=\"tkform_{$field}_{$value}\">\n";
			else
				$str = "\n\t<div $class id=\"tkform_{$field}\">\n";
			
			if ($label !== false and !in_array($type, self::$button_f)) {
				$label_str = $field;
				if ($label !== true) {
					$label_str = $label;
				}	
				
				
				if (in_array($type, self::$choice_f)) {
					$label = "\t\t<label for=\"{$field}_{$value}\">{$label_str}</label><br />\n";
				} else {
					$str .= "\t\t<label for=\"{$field}\">{$label_str}</label><br />\n";
				}
			}
			
			$str .= self::field($field, $value, $type, $selected, $attr);
			
			if (in_array($type, self::$choice_f))
					$str .= $label;
			
			$str .= "\n\t</div>\n";
				
			return $str;
		} else {
			return "\n<input type=\"hidden\" name=\"{$field}\" value=\"{$value}\" />\n";
		}
	}
	
	static function hidden($field, $value = '') {
		return self::input($field, 'hidden', $value, false,  NULL, NULL);
	}
	
	static function submit($value = NULL) {
		if ($value == NULL) $value = self::$submitText;
		
		return "\n\t<div class=\"tkFormInput tkFormInputSubmit\">\n\t\t<br />\n\t\t<input class=\"tk_form_submit\" type=\"submit\" value=\"{$value}\" />\n\t</div>\n\n";
	}	
}
?>