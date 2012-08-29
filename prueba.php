<?php
	require_once('_toolkit/main.php');
	
	$articulos = cmp::noticias()->forEachRow(function($articulo) {
		echo $articulo->titulo;
	});

?>