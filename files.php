<?php
	require_once('_toolkit/main.php');
	
	//var_export($_FILES);
	
	if ($_FILES) {	
		$file = file::upload('image', 'data/image/'.$_FILES['image']['name']);
		
		$ext = $file->getExtension();
		
		$file->rename('background'.$ext);
		
		$file->move('backgrounds/1/');
		
		echo $file->getBaseName().html::br();
		echo $file->getFolder().html::br();
		echo $file->getPath().html::br();
		
		$img = $file->getImage();
		if ($img !== false) {
			$img->resize(100, 75);	
		}
		
		//echo $img->htmlTag();
		echo html::img($file);
		
		
		//$file->delete();
	} else {
		echo form::init(NULL, NULL, 'multipart/form-data');
			echo new Field('image', 'file');
			echo form::submit();
		echo '</form>';
	}
?>