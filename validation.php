<?php
	require_once('_toolkit/main.php');
	
	if ($_POST) {
		echo 'post';
	} else {
		$form = cmp::noticias()->form();
		
		//$form->addField(new Field('email', 'email', NULL, NULL, array('validate' => true)));
		
		$form->field('titulo')->type = 'email';
		//$form->field('titulo')->validateInput(true); //VALIDA EL CAMPO TITULO SOLAMENTE
		
		$form->validate(true); //VALIDA EL FORMULARIO COMPLETO SI HAY UN CAMPO QUE SEA VALIDABLE
		
		
		echo $form;
	}
?>