<?php
	require_once('_toolkit/main.php');
	
	$noticia = cmp::noticias()->fetch(); //TRAE EL ULTIMO REGISTRO
	
	
	echo $noticia; //no VAR_EXPORT!!!!
	echo html::br();
	
	
	$noticia->fecha = calendar::now();
	$noticia->update(); //ACTUALIZA EL REGISTRO NO LA TABLA.
	
	echo $noticia;
	
	/*--------------------*/
	$form = $noticia->form(); //TRAE EL FORMULARIO DEL REGISTRO.
	
	$form->addField(new Field('Penultimo'));
	$form->addField(new Field('Ultimo'));
	
	$form->prepend(new Field('Primero'));
	
	$form->addFieldAfter('Primero', new Field('Tercero'));
	$form->addFieldBefore('Tercero', new Field('Segundo'));
	$form->addFieldAt(4, new Field('Cuarto', 'select', array(1,2,3,4)));
	
	/*------------------*/
	
	$form->popField();
	$form->removeField('Penultimo');
	$form->removeFieldAt(4);
	
	
	//$form->sortFields();
	
	/*------------------*/
	
	echo new Field('what'); //IMPRIME UN FORM INPUT NUEVO QUE NO PERTENECE A NINGUN FORMULARIO.
	echo html::hr();
	
	echo html::h3('Update Form');
	echo $form;
	echo html::hr();
	
	echo html::h3('Insert Form');
	echo cmp::noticias()->form(); //TRAE EL FORMULARIO DE LA TABLA.
	
	/**-------------------------------------------*/
	
	/*while ($noticia = $cmp::noticias()->whileFetch()) {
		echo $noticia->titulo;	
	}*/
?>