<?php

/*
* arquivo que controla o que se mostr publicamente na páxina, é dicir, mostra o GIF do Apalpador
*/


class apalpador
{
	function __construct()
	    {
       	$this->imaxe_apalpador();
    }
	function imaxe_apalpador()
	{
		wp_enqueue_style('style', URL.'apalpador/css/style.css');
		printf('
			<div id="capa_apalpador">
				<img src="'.URL.'apalpador/img/apalpador.gif"/>
			</div>  
		');
	}
}

?>