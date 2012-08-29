<?php
class html {
	static function attr($jason) {
		$attr = $jason;
		
		if (!is_array($jason)) {
			$attr = json_decode($jason);
		}
		
		$str = '';
		
		
		foreach($attr as $k => $v) {
			$str .= "{$k}=\"{$v}\"";
		}
		
		return $str;
	}
	
	static public function to_jason($values) {		
		$jason = "{\n";
		foreach ($values as $k => $v) {
			if ($v === true) $jason .= "\n\t\"{$k}\": true,"; else
			if ($v === false) $jason .= "\n\t\"{$k}\": false,"; else 
			if (is_integer($v)) $jason .= "\n\t\"{$k}\": {$v},"; else
			$jason .= "\n\t\"{$k}\": \"{$v}\",";
		}
		return substr($jason, 0, -1)."\n}";
	}
	
	static public function javascript($route) {
		return '<script type="text/javascript" src="'.$route.'" ></script>'."\n";
	}
	
	static function divs($class = NULL) {
		if ($class != NULL)
			$class = "class=\"{$class}\"";
		
		return "<div {$class}>";
	}
	
	static function dive() {
		return "</div>";	
	}
	
	static function div($string, $class = NULL) {
		return html::divs($class).$string.html::dive();
	}
	
	static function uls($class = NULL) {
		if ($class != NULL)
			$class = "class=\"{$class}\"";
		
		return "<ul {$class}>";
	}
	
	static function ule() {
		return "</ul>";
	}
	
	static function ul($items, $class = NULL) {
		return html::uls($class).$items.html::ule();
	}
	
	static function lis($class = NULL) {
		if ($class != NULL)
			$class = "class=\"{$class}\"";
		
		return "<li {$class}>";
	}
	
	static function lie() {
		return "</li>";
	}
	
	static function li($string, $class = NULL) {
		return html::lis($class).$string.html::lie();
	}
	
	static function a($string, $href = '#', $target = NULL, $class = NULL) {
		$a = "<a href=\"{$href}\"";
		if ($target !== NULL) $a .= " target=\"{$target}\"";
		if ($class != NULL) $a .= " class=\"{$class}\"";
		$a .= ">{$string}</a>";
		return $a;
	}
	
	static function img($source, $alt = NULL, $attr = NULL) {
		if ($alt == NULL) $alt = '';
		
		if ($attr != NULL) {
			$attr = self::attr($attr);
		}
		
		return "<img src=\"{$source}\" alt=\"{$alt}\" {$attr}/>\n";
	}
	
	static function ifimg($source, $alt_source, $alt = NULL, $attr = NULL) {
		if (!is_file($source)) {
			if (is_file($alt_source)) {
				return self::img($alt_source, $alt, $attr);
			}
		} else {
			return self::img($source, $alt, $attr);
		}
		
		return false;
	}

	static function option($label, $value, $selected = NULL) {
		if ($selected == true) { $selected = 'selected="selected"'; } else { $selected = ''; }
		
		return "<option value=\"{$value}\" {$selected} >{$label}</option>\n";
	}
	
	static function choice($name, $value, $label = NULL, $selected = NULL) {
		if ($selected == true) { $selected = 'selected="selected"'; } else { $selected = ''; }
		
		$id = $name.'_'.$value;
		
		return "<input id=\"{$id}\" type=\"radio\" name=\"{$name}\" value=\"{$value}\" {$selected} /> ".html::label($id, $label);
	}
	
	static function label($for, $label) {
		return "<label for=\"{$for}\">{$label}</label>";
	}
	
	
	static function p($string) {
		return "<p>{$string}</p>";
	}
	
	static function spans($class = NULL) {
		if ($class != NULL)
			$class = " class=\"{$class}\"";
		
		return "<span{$class}>";
	}
	
	static function spane() {
		return "</span>";
	}
	
	static function span($string, $class = NULL) {
		return html::spans($class).$string.html::spane();
	}
	
	static function b($string) {
		return self::strong($string);
	}
	
	static function strong($string) {
		return "<strong>{$string}</strong>";
	}
	
	static function br() {
		return "<br />";
	}
	
	static function hr() {
		return "<hr />";
	}
	
	static function thtd($th, $td) {
		return "<tr><th>{$th}</th><td>{$td}</td></tr>\n";
	}
	
	static function thtdin($th, $td, $field_name = NULL) {
		if ($field_name == NULL) {
			$field_name = $th;
		}
		
		return "<tr><th>{$th}</th><td>".form::field($field_name, $td)."</td></tr>\n";
	}
	
	static function fieldset($legend = NULL, $content) {
		$id = str_replace(array(' ', '-', '/', '\\'), '_', strtolower(trim($legend)));
		while (strpos($id, '__')) $id = str_replace('__', '_', $id);
		
		
		$str = "<fieldset id=\"_tk_fieldset_{$id}\">";
		if ($legend != NULL)
			$str .= "<legend>{$legend}</legend>";
			
		$str .= "{$content}</fieldset>";
		
		return $str;
	}
	
	static function table($html, $id = NULL) {
		if($html == NULL) {
			return false;
		}
		$table = "<table";
		if ($table !== NULL)
			$table .= ' id="'.$id.'"';
		
		$table .= ">{$html}</table>";
		
		return $table;
	}
	
	static function tr($html, $class = NULL) {
		$str = "<tr";
		if ($class !== NULL) $str .= ' class="'.$class.'"';
		$str .= ">\n{$html}</tr>";
		return $str;
	}
	
	static function td($html, $class = NULL) {
		$str = "\t<td";
		if ($class !== NULL) $str .= ' class="'.$class.'"';
		$str .= ">{$html}</td>\n";	
		return $str;
	}
	
	static protected function h($level, $text) {
		$level = (integer) $level;
		
		if ($level >= 1 and $level <= 6) {
			return "<h{$level}>{$text}</h{$level}>";
		} else {
			return false;
		}
	}
	
	static function h1($text) {
		return self::h(1, $text);
	}
	
	static function h2($text) {
		return self::h(2, $text);
	}
	
	static function h3($text) {
		return self::h(3, $text);
	}
	
	static function h4($text) {
		return self::h(4, $text);
	}
	
	static function h5($text) {
		return self::h(5, $text);
	}
	
	static function h6($text) {
		return self::h(6, $text);
	}
}
?>