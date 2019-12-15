<?php

/* 
* Neste arquivo irei incluíndo as funcións do meu plugin 
*/


/* esta función ten como obxectivo incluír unha mensaxe en todo o back-end
de administración. Para iso chamamos ao hook 'admin_notices' que se lanza  
como aviso en todas as páxinas de administración */
function apa_mensaxe_admin() {
	/* clase que lle vou aplicar. Todas están predefinidas. Notice pinta a "caixiña", 
	notice-success faina verde e is-dismissible fai que se poda pechar */
    $clase = 'notice notice-success is-dismissible'; 
    /*$message = __( 'Isto é un aviso de activación de plugin', 'sample-text-domain' );*/
	$mensaxe = 'O Apalpador está presente :D';
 	
	/* esc_html quítalle as marcas de HTML */
    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr($clase), esc_html($mensaxe) ); 
}
add_action( 'admin_notices', 'apa_mensaxe_admin' );


/* funcion que chama a crear a imaxe do apalpador */
function apa_apalpador (){
	/* seleccionamos onde se vai mostrar. Segundo isto móstrase en todas */
	$paxinas_a_mostrar = get_option('display_on_pages');
	/*chamo ao arquivo onde se sitúa o código para mostrar a imaxe do apalpador */
	include(PATH.'apalpador/apalpador.php');
	/* creamos a instancia da clase Apalpador */
	new apalpador();	
}
add_action('wp_head', 'apa_apalpador' );
	
	


?>