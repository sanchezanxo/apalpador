<?php

/* 
* As opcións do menú esquerdo de administración de WordPress
*/

/* esta función o que fai é crear un campo raiz das opcións do plugin que se situarán
no menú lateral de administración de WordPress */
function apa_menu_lateral_admin()	{
	
	/* os campos son 'Nome no navegador', 'nome na opción do menú', 'permisos', 'ruta do arquivo onde
	estará programado o que se vai mostrar'. Ten máis opcións, estas son as obrigatorias. Vén sendo o primeiro campo*/
	add_menu_page('Apalpador','Apalpador','manage_options',PATH.'/admin/admin.php');
	
	/* tamén podemos crear submenús (FILLOS) que colguen da opción principal. Neste caso é moi similar e simplemente 
	hai que engadir un campo ao inicio de todo indicando quen é o sei PAI (top-menu). */
	add_submenu_page(PATH.'/admin/admin.php','Ler máis','Ler máis','manage_options',PATH.'/admin/lermais.php');
	
}
/* lanzamos o hook para que se pinte */
add_action( 'admin_menu', 'apa_menu_lateral_admin' );

?>